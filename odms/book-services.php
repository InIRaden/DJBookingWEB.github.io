<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

$userInfo = null;

if (isset($_SESSION['odmsaid'])) {
    $uid = $_SESSION['odmsaid'];
    $sqlUser = "SELECT NameUser, Email, MobileNumber FROM tbluser_login WHERE ID = :uid";
    $queryUser = $dbh->prepare($sqlUser);
    $queryUser->bindParam(':uid', $uid, PDO::PARAM_INT);
    $queryUser->execute();
    $userInfo = $queryUser->fetch(PDO::FETCH_ASSOC);
}



// Tahap pertama - Simpan data booking ke session tapi jangan masukkan ke database dulu
if (isset($_POST['confirm_submit'])) {
    $userId = $_SESSION['odmsaid']; // Get user ID from session
    $bid = $_POST['bookid'];
    
    // Get user data dari tbluser_login
    $sqlUser = "SELECT NameUser, Email, MobileNumber FROM tbluser_login WHERE ID = :uid";
    $queryUser = $dbh->prepare($sqlUser);
    $queryUser->bindParam(':uid', $userId, PDO::PARAM_INT);
    $queryUser->execute();
    $userInfo = $queryUser->fetch(PDO::FETCH_ASSOC);

    // Pake user data bukan dari form tapi dari login soalnya biar aman azza
    $name = $userInfo['NameUser'];
    $mobnum = $userInfo['MobileNumber'];
    $email = $userInfo['Email'];
    $edate = $_POST['edate'];
    $est = $_POST['est'];
    $eetime = $_POST['eetime'];
    $vaddress = $_POST['vaddress'];
    $eventtype = $_POST['eventtype'];
    $addinfo = $_POST['addinfo'];
    $bookingid = mt_rand(100000000, 999999999);
    $paymentMethod = $_POST['payment_method'];
    $selectedBank = isset($_POST['selected_bank']) ? $_POST['selected_bank'] : '';
    $installmentCount = isset($_POST['installment_count']) ? $_POST['installment_count'] : null;

    // Get service price
    $sqlPrice = "SELECT ServicePrice FROM tblservice WHERE ID = :bid";
    $queryPrice = $dbh->prepare($sqlPrice);
    $queryPrice->bindParam(':bid', $bid, PDO::PARAM_STR);
    $queryPrice->execute();
    $serviceData = $queryPrice->fetch(PDO::FETCH_ASSOC);
    $amount = $serviceData['ServicePrice'];

    // Cek apakah user sudah login
    if (!isset($_SESSION['odmsaid'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first to make a booking']);
        exit;
    }

    // Simpan data booking ke session untuk digunakan nanti
    $_SESSION['temp_booking'] = [
        'bookingid' => $bookingid,
        'bid' => $bid,
        'userid' => $_SESSION['odmsaid'], // Tambahkan UserID dari session
        'name' => $name,
        'mobnum' => $mobnum,
        'email' => $email,
        'edate' => $edate,
        'est' => $est,
        'eetime' => $eetime,
        'vaddress' => $vaddress,
        'eventtype' => $eventtype,
        'addinfo' => $addinfo,
        'paymentMethod' => $paymentMethod,
        'selectedBank' => $selectedBank,
        'installmentCount' => $installmentCount,
        'amount' => $amount,
        'expiryTime' => date('Y-m-d H:i:s', strtotime('+24 hours')),
        'va_number' => mt_rand(1000000000000000, 9999999999999999) // Generate random VA number ini
    ];

    echo json_encode(['success' => true, 'booking_data' => $_SESSION['temp_booking']]);
    exit;
}

// Step terakhir - Masukkan data booking ke database setelah konfirmasi pembayaran menggunakan stored procedure
if (isset($_POST['final_submit'])) {
    if (!isset($_SESSION['odmsaid'])) {
        echo json_encode(['success' => false, 'message' => 'Please login to continue']);
        exit;
    }

    if (isset($_SESSION['temp_booking'])) {
        // Verifikasi apakah booking ini milik user yang sedang login
        if ($_SESSION['temp_booking']['userid'] != $_SESSION['odmsaid']) {
            echo json_encode(['success' => false, 'message' => 'Invalid booking session']);
            exit;
        }
        $bookingData = $_SESSION['temp_booking'];
        $userPay = isset($_POST['user_pay']) ? floatval($_POST['user_pay']) : 0;
        $amount = floatval($bookingData['amount']);
        $paymentMethod = $bookingData['paymentMethod'];
        // Validation
        if ($paymentMethod === 'installment') {
            $minPay = $amount * 0.5;
            if ($userPay < $minPay) {
                echo json_encode(['success' => false, 'message' => 'Minimum payment for installment is 50% of total amount']);
                exit;
            }
        } else {
            if ($userPay < $amount) {
                echo json_encode(['success' => false, 'message' => 'Payment amount must be equal to total amount']);
                exit;
            }
        }

        // Set CompletedDate dan PaymentStatus berdasarkan metode pembayaran
        $completedDate = null;
        $paymentStatus = 'Pending';
        if ($paymentMethod === 'cash' || $paymentMethod === 'transfer') {
            $completedDate = date('Y-m-d H:i:s');
            $paymentStatus = 'Paid';
        } elseif (strtolower($paymentMethod) === 'installment') {
            $completedDate = null;
            $paymentStatus = 'First Payment';
        }

        try {
            $dbh->beginTransaction();

            // Memanggil stored procedure buat masukkin data booking dan payment
            $sql = "CALL CreateBookingAndPayment(
                :bookingid, :serviceid, :userid, :name, :mobnum, :email, :edate, :est, :eetime, :vaddress, :eventtype, :addinfo, :paymentMethod, :amount, :selectedBank, :va_number, :paymentStatus, :completedDate, :installmentCount, :userPay
            )";
            $query = $dbh->prepare($sql);
            $query->bindParam(':bookingid', $bookingData['bookingid']);
            $query->bindParam(':serviceid', $bookingData['bid']);
            $query->bindParam(':userid', $_SESSION['odmsaid']);
            $query->bindParam(':name', $bookingData['name']);
            $query->bindParam(':mobnum', $bookingData['mobnum']);
            $query->bindParam(':email', $bookingData['email']);
            $query->bindParam(':edate', $bookingData['edate']);
            $query->bindParam(':est', $bookingData['est']);
            $query->bindParam(':eetime', $bookingData['eetime']);
            $query->bindParam(':vaddress', $bookingData['vaddress']);
            $query->bindParam(':eventtype', $bookingData['eventtype']);
            $query->bindParam(':addinfo', $bookingData['addinfo']);
            $query->bindParam(':paymentMethod', $bookingData['paymentMethod']);
            $query->bindParam(':amount', $bookingData['amount']);
            $query->bindParam(':selectedBank', $bookingData['selectedBank']);
            $query->bindParam(':va_number', $bookingData['va_number']);
            $query->bindParam(':paymentStatus', $paymentStatus);
            $query->bindParam(':completedDate', $completedDate);
            $query->bindParam(':installmentCount', $bookingData['installmentCount']);
            $query->bindParam(':userPay', $userPay);
            $query->execute();

            // Masukkan ke payment history
            $sqlHistory = "INSERT INTO tblpayment_history (UserID, BookingID, Amount, PaymentMethod, PaymentDate, PaymentStatus) 
                          VALUES (:userid, :bookingid, :amount, :paymentMethod, :paymentDate, :paymentStatus)";
            $queryHistory = $dbh->prepare($sqlHistory);
            $currentDateTime = date('Y-m-d H:i:s');
            $queryHistory->bindParam(':userid', $_SESSION['odmsaid']);
            $queryHistory->bindParam(':bookingid', $bookingData['bookingid']);
            $queryHistory->bindParam(':amount', $userPay); // Pake jumlah uang yang dibayar user nih
            $queryHistory->bindParam(':paymentMethod', $bookingData['paymentMethod']);
            $queryHistory->bindParam(':paymentDate', $currentDateTime);
            $queryHistory->bindParam(':paymentStatus', $paymentStatus);
            $queryHistory->execute();

            $dbh->commit();
            unset($_SESSION['temp_booking']);
            // Kirim respons sukses agar modal sukses muncul di frontend
            echo json_encode(['success' => true, 'message' => 'Booking and payment successful']);
            exit;
        } catch (Exception $e) {
            $dbh->rollBack();
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
            exit;
        }
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'No temporary booking data found.']);
    }
    exit;
}

// Handle final form submission
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['final_submit']) && $input['final_submit'] === true) {
    try {
        $dbh->beginTransaction();

        // Ambil UserID dari session login
        if (!isset($_SESSION['odmsaid'])) {
            throw new Exception('User not logged in');
        }
        $userId = $_SESSION['odmsaid'];

        // Insert booking data
        $sql = "INSERT INTO tblbooking (UserID, BookingID, ServiceID, Name, Email, EventDate, EventStartTime, EventEndTime, EventType, VenueAddress, AdditionalInformation, BookingDate, Status) VALUES (:userid, :bookingid, :serviceid, :name, :email, :eventdate, :eventstarttime, :eventendtime, :eventtype, :venue, :additionalinfo, :bookingdate, :status)";

        $bookingId = uniqid('BK', true); // Generate a unique booking ID
        $currentDateTime = date('Y-m-d H:i:s');
        $query = $dbh->prepare($sql);

        $query->bindParam(':userid', $userId, PDO::PARAM_INT);
        $query->bindParam(':bookingid', $bookingId, PDO::PARAM_STR);
        $query->bindParam(':serviceid', $input['serviceid'], PDO::PARAM_INT);
        $query->bindParam(':name', $input['name'], PDO::PARAM_STR);
        $query->bindParam(':email', $input['email'], PDO::PARAM_STR);
        $query->bindParam(':eventdate', $input['eventdate'], PDO::PARAM_STR);
        $query->bindParam(':eventstarttime', $input['eventstarttime'], PDO::PARAM_STR);
        $query->bindParam(':eventendtime', $input['eventendtime'], PDO::PARAM_STR);
        $query->bindParam(':eventtype', $input['eventtype'], PDO::PARAM_STR);
        $query->bindParam(':venue', $input['venue'], PDO::PARAM_STR);
        $query->bindParam(':additionalinfo', $input['additionalinfo'], PDO::PARAM_STR);
        $query->bindParam(':bookingdate', $currentDateTime, PDO::PARAM_STR);
        $status = 'Pending';
        $query->bindParam(':status', $status, PDO::PARAM_STR);

        $query->execute();

        // Masukkan data pembayaran
        $sql = "INSERT INTO tblpayment (UserID, BookingID, PaymentMethod, Bank, InstallmentCount, AmountPaid, VANumber, PaymentDate, PaymentStatus) VALUES (:userid, :bookingid, :paymentmethod, :bank, :installmentcount, :amountpaid, :vanumber, :paymentdate, :paymentstatus)";

        $query = $dbh->prepare($sql);
        $query->bindParam(':userid', $userId, PDO::PARAM_INT);
        $query->bindParam(':bookingid', $bookingId, PDO::PARAM_STR);
        $query->bindParam(':paymentmethod', $input['payment_method'], PDO::PARAM_STR);
        $query->bindParam(':bank', $input['selected_bank'], PDO::PARAM_STR);
        $query->bindParam(':installmentcount', $input['installment_count'], PDO::PARAM_INT);
        $query->bindParam(':amountpaid', $input['amount_paid'], PDO::PARAM_STR);
        $query->bindParam(':vanumber', $input['va_number'], PDO::PARAM_STR);
        $query->bindParam(':paymentdate', $currentDateTime, PDO::PARAM_STR);
        $paymentStatus = 'Pending';
        $query->bindParam(':paymentstatus', $paymentStatus, PDO::PARAM_STR);

        $query->execute();

        // Masukkan ke payment history
        $sqlHistory = "INSERT INTO tblpayment_history (UserID, BookingID, Amount, PaymentMethod, PaymentDate, PaymentStatus)
                       VALUES (:userid, :bookingid, :amount, :paymentMethod, :paymentDate, :paymentStatus)";
        $queryHistory = $dbh->prepare($sqlHistory);
        $queryHistory->bindParam(':userid', $userId, PDO::PARAM_INT);
        $queryHistory->bindParam(':bookingid', $bookingId, PDO::PARAM_STR);
        $queryHistory->bindParam(':amount', $input['amount_paid'], PDO::PARAM_STR);
        $queryHistory->bindParam(':paymentMethod', $input['payment_method'], PDO::PARAM_STR);
        $queryHistory->bindParam(':paymentDate', $currentDateTime, PDO::PARAM_STR);
        $queryHistory->bindParam(':paymentStatus', $paymentStatus, PDO::PARAM_STR);
        $queryHistory->execute();

        $dbh->commit();
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        $dbh->rollBack();
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>DjBooking - Book Services</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
    <link rel="stylesheet" href="./css/book-service.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        /* Header styles */
        .header-container {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
        }

        .header-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top,rgba(0,0,0,0.95) 0%,rgba(0,0,0,0.7) 60%,rgba(0,0,0,0.1) 100%);
        }

        .header-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            text-align: center;
            width: 100%;
            max-width: 500px;
            padding: 0 20px;
            z-index: 2;
        }

        .header-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: rgba(255,255,255,0.92);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px #fff,0 0 18px #2563eb,2px 2px 8px rgba(0,0,0,0.3);
            letter-spacing: 1px;
        }

        .header-text {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background-color: #1a1a1a;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            display: flex;
            gap: 30px;
        }

        .modal-body-left {
            flex: 1;
            padding-right: 20px;
            border-right: 1px solid #333;
        }

        .modal-body-right {
            flex: 1;
        }

        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #333;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #fff;
        }

        .btn-modal {
            padding: 10px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #4b5563;
            color: #fff;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #374151;
        }

        .copy-field {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #2d2d2d;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .copy-field input {
            flex-grow: 1;
            padding: 8px;
            background-color: #1a1a1a;
            color: #fff;
            border: 1px solid #444;
            border-radius: 4px;
        }

        .copy-btn {
            padding: 8px 15px;
            background-color: #2563eb;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .copy-btn:hover {
            background-color: #1d4ed8;
        }

        .timer {
            font-size: 14px;
            color: #ef4444;
            margin-top: 10px;
        }

        .page-indicator {
            font-size: 14px;
            color: #9ca3af;
        }

        .payment-info {
            background-color: #2d2d2d;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .payment-info p {
            margin: 5px 0;
        }

        .payment-info .label {
            color: #9ca3af;
            font-size: 14px;
        }

        .payment-info .value {
            color: #fff;
            font-weight: 500;
        }
    </style>
</head>

<body class="bg-black text-white">
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <div class="header-container">
            <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
            <div class="header-overlay"></div>
            <div class="header-content">
                <h1 class="header-title">Book Services</h1>
                <p class="header-text">
                    Learn more about our DJ services and what makes us special
                </p>
            </div>
        </div>
    </header>

    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto">
        <div class="flex items-center space-x-2 text-xs mb-8">
            <a href="index.php" class="text-gray-400 hover:text-white">Home</a>
            <span class="text-gray-600">/</span>
            <span class="text-white">Book Services</span>
        </div>

        <section class="mb-12">
            <h2 class="font-semibold text-white text-lg mb-6">Book Your Event</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <form method="post" id="booking-form">
                        <input type="hidden" name="bookid" value="<?php echo isset($_GET['bookid']) ? htmlentities($_GET['bookid']) : ''; ?>">
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($userInfo['NameUser'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($userInfo['Email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="mobnum">Mobile Number</label>
                            <input type="text" class="form-control" name="mobnum" id="mobnum" value="<?php echo htmlspecialchars($userInfo['MobileNumber'] ?? ''); ?>" required maxlength="10" pattern="[0-9]+">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="edate">Event Date</label>
                            <input type="date" class="form-control" name="edate" id="edate" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="est">Event Starting Time</label>
                            <select class="form-control" name="est" id="est" required>
                                <option value="">Select Starting Time</option>
                                <option value="1 a.m">1 a.m</option>
                                <option value="2 a.m">2 a.m</option>
                                <option value="3 a.m">3 a.m</option>
                                <option value="4 a.m">4 a.m</option>
                                <option value="5 a.m">5 a.m</option>
                                <option value="6 a.m">6 a.m</option>
                                <option value="7 a.m">7 a.m</option>
                                <option value="8 a.m">8 a.m</option>
                                <option value="9 a.m">9 a.m</option>
                                <option value="10 a.m">10 a.m</option>
                                <option value="11 a.m">11 a.m</option>
                                <option value="12 p.m">12 p.m</option>
                                <option value="1 p.m">1 p.m</option>
                                <option value="2 p.m">2 p.m</option>
                                <option value="3 p.m">3 p.m</option>
                                <option value="4 p.m">4 p.m</option>
                                <option value="5 p.m">5 p.m</option>
                                <option value="6 p.m">6 p.m</option>
                                <option value="7 p.m">7 p.m</option>
                                <option value="8 p.m">8 p.m</option>
                                <option value="9 p.m">9 p.m</option>
                                <option value="10 p.m">10 p.m</option>
                                <option value="11 p.m">11 p.m</option>
                                <option value="12 a.m">12 a.m</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="eetime">Event Finish Time</label>
                            <select class="form-control" name="eetime" id="eetime" required>
                                <option value="">Select Finish Time</option>
                                <option value="1 a.m">1 a.m</option>
                                <option value="2 a.m">2 a.m</option>
                                <option value="3 a.m">3 a.m</option>
                                <option value="4 a.m">4 a.m</option>
                                <option value="5 a.m">5 a.m</option>
                                <option value="6 a.m">6 a.m</option>
                                <option value="7 a.m">7 a.m</option>
                                <option value="8 a.m">8 a.m</option>
                                <option value="9 a.m">9 a.m</option>
                                <option value="10 a.m">10 a.m</option>
                                <option value="11 a.m">11 a.m</option>
                                <option value="12 p.m">12 p.m</option>
                                <option value="1 p.m">1 p.m</option>
                                <option value="2 p.m">2 p.m</option>
                                <option value="3 p.m">3 p.m</option>
                                <option value="4 p.m">4 p.m</option>
                                <option value="5 p.m">5 p.m</option>
                                <option value="6 p.m">6 p.m</option>
                                <option value="7 p.m">7 p.m</option>
                                <option value="8 p.m">8 p.m</option>
                                <option value="9 p.m">9 p.m</option>
                                <option value="10 p.m">10 p.m</option>
                                <option value="11 p.m">11 p.m</option>
                                <option value="12 a.m">12 a.m</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="vaddress">Venue Address</label>
                            <textarea class="form-control" name="vaddress" id="vaddress" required rows="4"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="eventtype">Type of Event</label>
                            <select class="form-control" name="eventtype" id="eventtype" required>
                                <option value="">Choose Event Type</option>
                                <?php
                                $sql2 = "SELECT * FROM tbleventtype";
                                $query2 = $dbh->prepare($sql2);
                                $query2->execute();
                                $result2 = $query2->fetchAll(PDO::FETCH_OBJ);
                                foreach ($result2 as $row) { ?>
                                    <option value="<?php echo htmlentities($row->EventType); ?>"><?php echo htmlentities($row->EventType); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="addinfo">Additional Information</label>
                            <textarea class="form-control" name="addinfo" id="addinfo" required rows="4"></textarea>
                        </div>
                        <div>
                            <input type="submit" name="submit" value="Book" class="btn-submit">
                        </div>
                    </form>
                </div>
                <div>

                    <!-- payment -->
                    <div class="mb-6">
                        <label class="block text-sm text-gray-300 mb-3 font-medium">Payment Method</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 hover:border-blue-500 transition-all duration-300 cursor-pointer hover:scale-102 shadow-md h-32">
                                <input type="radio" name="payment_method" id="cash" value="cash" class="hidden payment-radio" checked>
                                <label for="cash" class="flex flex-col items-center justify-center cursor-pointer h-full space-y-2">
                                    <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center">
                                        <i class="fas fa-money-bill-wave text-green-400 text-lg"></i>
                                    </div>
                                    <div class="flex flex-col items-center justify-center space-y-1">
                                        <span class="text-sm font-medium text-white">Cash</span>
                                        <span class="text-xs text-gray-400">Pay in cash when we meet</span>
                                    </div>
                                </label>
                            </div>
                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 hover:border-blue-500 transition-all duration-300 cursor-pointer hover:scale-102 shadow-md h-32">
                                <input type="radio" name="payment_method" id="transfer" value="transfer" class="hidden payment-radio">
                                <label for="transfer" class="flex flex-col items-center justify-center cursor-pointer h-full space-y-2">
                                    <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center">
                                        <i class="fas fa-credit-card text-blue-400 text-lg"></i>
                                    </div>
                                    <div class="flex flex-col items-center justify-center cursor-pointer h-full space-y-1">
                                        <span class="text-sm font-medium text-white">Transfer</span>
                                        <span class="text-xs text-gray-400">Pay via virtual account</span>
                                    </div>
                                </label>
                            </div>
                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 hover:border-blue-500 transition-all duration-300 cursor-pointer hover:scale-102 shadow-md h-32">
                                <input type="radio" name="payment_method" id="installment" value="installment" class="hidden payment-radio">
                                <label for="installment" class="flex flex-col items-center justify-center cursor-pointer h-full space-y-2">
                                    <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-purple-400 text-lg"></i>
                                    </div>
                                    <div class="flex flex-col items-center justify-center cursor-pointer h-full space-y-1">
                                        <span class="text-sm font-medium text-white">Installment</span>
                                        <span class="text-xs text-gray-400">Pay in multiple payments</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="bank-dropdown" class="mb-6 bg-gray-800 p-4 rounded-lg transition-all duration-500 ease-in-out">
                        <label class="block text-sm text-gray-300 mb-3 font-medium">Select Bank</label>
                        <select name="selected_bank" class="form-control" id="selected-bank">
                            <option value="">Choose Bank</option>
                            <option value="BCA">BCA</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="CIMB Niaga">CIMB Niaga</option>
                        </select>
                    </div>

                    <div id="installment-options" class="mb-6 bg-gray-800 p-4 rounded-lg opacity-0 max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <label class="block text-sm text-gray-300 mb-3 font-medium">Installment Terms</label>
                        <div class="flex items-center">
                            <select name="installment_count" class="form-control">
                                <option value="2">2 payments</option>
                                <option value="3">3 payments</option>
                            </select>
                            <p class="ml-4 text-xs text-gray-400">* Installment only available via transfer</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <div id="confirm-modal" class="modal">
        <div class="modal-content modal-landscape">
            <span class="close-modal" onclick="closeModal('confirm-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Confirm Your Booking <span class="text-sm text-gray-400 ml-2">Step 1/2</span></h3>
            </div>
            <div class="modal-body">
                <div class="modal-body-left" style="padding: 20px;">
                    <h4 class="text-lg font-medium text-white mb-4">Booking Details</h4>
                    <table style="width:100%; border-collapse: collapse; background-color: #2d2d2d; border-radius: 12px; overflow: hidden;">
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Name:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-name"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Email:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-email"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Phone Number:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-mobnum"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Event Date:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-edate"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Start Time:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-est"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">End Time:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-eetime"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-body-right" style="padding: 20px;">
                    <h4 class="text-lg font-medium text-white mb-4">Additional Details</h4>
                    <table style="width:100%; border-collapse: collapse; background-color: #2d2d2d; border-radius: 12px; overflow: hidden;">
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Venue Address:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-vaddress"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Event Type:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-eventtype"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Additional Information:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-addinfo"></span></td>
                        </tr>
                         <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Payment Method:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-payment-method"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Bank:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-selected-bank"></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; text-align: left; color: #9ca3af;">Installment Count:</td>
                            <td style="padding: 12px; text-align: left; color: #fff; font-weight: bold;"><span id="confirm-installment-count"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="confirm-error-message" class="error-message bg-red-500 text-white p-3 rounded mb-4" style="display: none;"></div>
            <div class="modal-footer">
                <button class="btn-modal btn-secondary" onclick="closeModal('confirm-modal')">Cancel</button>
                <button class="btn-modal btn-primary" onclick="showPaymentDetails()">Next</button>
            </div>
        </div>
    </div>


    <!-- 2/2 modal -->
    <div id="payment-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('payment-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Payment Details <span class="text-sm text-gray-400 ml-2">Step 2/2</span></h3>
            </div>
            <div class="modal-body">
                <div class="modal-body-left">
                    <h4 class="text-lg font-medium text-white mb-4">Booking Information</h4>
                    <div class="payment-info">
                        <p><span class="label">Booking ID:</span> <span id="payment-booking-id" class="value" style="float: right;"></span></p>
                        <p><span class="label">Total Payment:</span> <span id="payment-amount" class="value" style="float: right;"></span></p>
                        <p><span class="label">Payment Method:</span> <span id="payment-method" class="value" style="float: right;"></span></p>
                        <p>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 0; text-align: left; color: #9ca3af; font-size: 14px;">Bank:</td>
                                    <td style="padding: 0; text-align: right; color: #fff; font-weight: 500;"><span id="payment-bank" class="value"></span></td>
                                </tr>
                            </table>
                        </p>
                    </div>
                    <div class="timer mt-4">
                        <p>Time remaining for payment:</p>
                        <p id="payment-timer" class="font-bold" style="color: #fff;"></p>
                    </div>
                </div>
                <div class="modal-body-right">
                    <h4 class="text-lg font-medium text-white mb-4">Payment Instructions</h4>
                    <div class="payment-info cash-instructions" style="display:none;">
                        <p class="text-white mb-2">Please prepare the exact cash amount to pay on the event day.</p>
                        <p class="text-gray-300 text-sm mt-4">You will pay directly to our staff at the event location.</p>
                        <label for="user-pay-cash" class="text-white mt-4 block">Payment Amount</label>
                        <input type="number" id="user-pay-cash" name="user_pay_cash" class="form-input mt-1 block w-full" min="0" placeholder="Enter payment amount">
                        <span id="user-pay-cash-error" class="text-red-400 text-xs mt-1" style="display:none;"></span>
                    </div>
                    <div class="payment-info transfer-instructions" style="display:block;">
                        <p class="text-white mb-2">Virtual Account Number:</p>
                        <div class="copy-field">
                            <input type="text" id="payment-va-number" readonly>
                            <button class="copy-btn" onclick="copyToClipboard('payment-va-number')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p id="installment-minimum-text" class="text-yellow-400 text-sm mt-1" style="display: none;">Minimum payment 50%</p>
                        <p class="text-gray-300 text-sm mt-4">Please transfer the exact amount to the VA number above.</p>
                        <label for="user-pay" class="text-white mt-4 block">Payment Amount</label>
                        <input type="number" id="user-pay" name="user_pay" class="form-input mt-1 block w-full" min="0" placeholder="Enter payment amount">
                        <span id="user-pay-error" class="text-red-400 text-xs mt-1" style="display:none;"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-secondary" onclick="backToConfirmModal()">Previous</button>
                <button class="btn-modal btn-primary" onclick="confirmPayment()">Payment Complete</button>
            </div>
        </div>
    </div>

    <div id="success-modal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Payment Successful!</h3>
                <p class="text-gray-300 mb-4">Your booking has been successfully processed.</p>
                <button class="btn-modal btn-primary" onclick="paymentCompleted()">Done</button>
            </div>
        </div>
    </div>


    <?php include_once('includes/footer.php'); ?>


    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script src="./js/book-services.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {
                animationEffect: "fade",
                transitionEffect: "fade",
                buttons: ["zoom", "slideShow", "fullScreen", "close"]
            });

            // Get references to payment-related elements
            const paymentRadios = document.querySelectorAll('.payment-radio');
            const installmentOptions = document.getElementById('installment-options');
            const bankDropdown = document.getElementById('bank-dropdown');

            // Menambahkan event listener untuk setiap radio button
            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Apdet border styling buat metode pembayaran yang dipilih
                    document.querySelectorAll('.payment-radio').forEach(r => {
                        const parent = r.parentElement;
                        if (r.checked) {
                            parent.classList.add('border-blue-500');
                            parent.classList.remove('border-gray-700');
                        } else {
                            parent.classList.remove('border-blue-500');
                            parent.classList.add('border-gray-700');
                        }
                    });

                    // Show/hide bank dropdown berdasarkan payment method
                    if (this.value === 'transfer' || this.value === 'installment') {
                        bankDropdown.classList.add('show');
                    } else {
                        bankDropdown.classList.remove('show');
                    }

                    // Show/hide installment options cuman buat installment payment method
                    if (this.value === 'installment') {
                        installmentOptions.classList.add('show');
                        installmentOptions.style.opacity = '1';
                        installmentOptions.style.maxHeight = '200px';
                    } else {
                        installmentOptions.classList.remove('show');
                        installmentOptions.style.opacity = '0';
                        installmentOptions.style.maxHeight = '0';
                    }
                });
            });

            // Trigger change event for any pre-selected payment method
            const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
            if (checkedRadio) {
                checkedRadio.dispatchEvent(new Event('change'));
            }

            // Handle form submission and populate confirmation modal
            const form = document.getElementById('booking-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Populate confirmation modal with form data
                document.getElementById('confirm-name').textContent = data.name || '-';
                document.getElementById('confirm-email').textContent = data.email || '-';
                document.getElementById('confirm-mobnum').textContent = data.mobnum || '-';
                document.getElementById('confirm-edate').textContent = data.edate || '-';
                document.getElementById('confirm-est').textContent = data.est || '-';
                document.getElementById('confirm-eetime').textContent = data.eetime || '-';
                document.getElementById('confirm-vaddress').textContent = data.vaddress || '-';
                document.getElementById('confirm-eventtype').textContent = data.eventtype || '-';
                document.getElementById('confirm-addinfo').textContent = data.addinfo || '-';

                // Get selected payment method and display it properly
                const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
                const paymentMethodValue = selectedPaymentMethod ? selectedPaymentMethod.value : '-';
                document.getElementById('confirm-payment-method').textContent = paymentMethodValue;

                // Get and display bank information only for transfer and installment
                const selectedBank = document.getElementById('selected-bank');
                const bankElement = document.querySelector('.text-gray-300:has(#confirm-selected-bank)');
                if (paymentMethodValue === 'transfer' || paymentMethodValue === 'installment') {
                    document.getElementById('confirm-selected-bank').textContent = selectedBank.value || '-';
                    if (bankElement) bankElement.style.display = 'block';
                } else {
                    if (bankElement) bankElement.style.display = 'none';
                }

                // Get and display installment count only for installment
                const installmentCount = document.querySelector('select[name="installment_count"]');
                const installmentElement = document.querySelector('.text-gray-300:has(#confirm-installment-count)');
                if (paymentMethodValue === 'installment') {
                    document.getElementById('confirm-installment-count').textContent = installmentCount.value || '-';
                    if (installmentElement) installmentElement.style.display = 'block';
                } else {
                    if (installmentElement) installmentElement.style.display = 'none';
                }

                openModal('confirm-modal');
            });
        });

        /**
         * Returns from payment modal to confirmation modal
         */
        function backToConfirmModal() {
            closeModal('payment-modal');
            openModal('confirm-modal');
        }

        function showPaymentDetails() {
            const form = document.getElementById('booking-form');
            const formData = new FormData(form);

            // Tambahkan data payment method, bank, dan installment
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const selectedBank = document.getElementById('selected-bank').value;
            const installmentCount = paymentMethod === 'installment' ?
                document.querySelector('select[name="installment_count"]').value : null;

            formData.append('payment_method', paymentMethod);
            formData.append('selected_bank', selectedBank);
            formData.append('installment_count', installmentCount);
            formData.append('confirm_submit', '1');

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal('confirm-modal');
                        const bookingData = data.booking_data;
                        document.getElementById('payment-booking-id').textContent = bookingData.bookingid;
                        document.getElementById('payment-amount').textContent = formatCurrency(bookingData.amount);
                        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
                        const paymentMethodValue = paymentMethod ? paymentMethod.value : '-';
                        document.getElementById('payment-method').textContent = paymentMethodValue;
                        const bankInfoRow = document.querySelector('.payment-info p:has(#payment-bank)');
                        const paymentTimer = document.querySelector('.timer');
                        const vaInstructions = document.querySelector('.transfer-instructions');
                        const cashInstructions = document.querySelector('.cash-instructions');
                        const installmentMinimumText = document.getElementById('installment-minimum-text');
                        // Show/hide elements based on payment method
                        if (paymentMethodValue === 'cash') {
                            if (bankInfoRow) bankInfoRow.style.display = 'none';
                            if (paymentTimer) paymentTimer.style.display = 'none';
                            if (vaInstructions) vaInstructions.style.display = 'none';
                            if (cashInstructions) cashInstructions.style.display = 'block';
                            if (installmentMinimumText) installmentMinimumText.style.display = 'none';
                        } else {
                            if (bankInfoRow) bankInfoRow.style.display = 'block';
                            if (paymentTimer) paymentTimer.style.display = 'block';
                            if (vaInstructions) vaInstructions.style.display = 'block';
                            if (cashInstructions) cashInstructions.style.display = 'none';
                            const selectedBank = document.getElementById('selected-bank');
                            document.getElementById('payment-bank').textContent =
                                (paymentMethod && (paymentMethod.value === 'transfer' || paymentMethod.value === 'installment')) ?
                                (selectedBank.value || '-') : '-';
                            document.getElementById('payment-va-number').value = bookingData.va_number;
                            startTimer(bookingData.expiryTime);
                            if (installmentMinimumText) {
                                installmentMinimumText.style.display = paymentMethodValue === 'installment' ? 'block' : 'none';
                            }
                        }
                        openModal('payment-modal');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }
        /**
         * Confirms payment and processes final submission
         * Sends final confirmation to server and shows success modal
         */
        function confirmPayment() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            if (paymentMethod === 'cash') {
                const userPayInput = document.getElementById('user-pay-cash');
                const userPayError = document.getElementById('user-pay-cash-error');
                const paymentAmount = parseFloat(document.getElementById('payment-amount').textContent.replace(/[^\d.]/g, ''));
                const userPay = parseFloat(userPayInput.value);
                userPayError.style.display = 'none';
                userPayError.textContent = '';
                if (userPay !== paymentAmount) {
                    userPayError.textContent = 'Payment amount must be exactly the total price.';
                    userPayError.style.display = 'block';
                    userPayInput.focus();
                    return;
                }
                const formData = new FormData();
                formData.append('final_submit', '1');
                formData.append('payment_method', paymentMethod);
                formData.append('user_pay', userPay);
                formData.append('selected_bank', '');
                formData.append('va_number', '');
                formData.append('installment_count', '');
                formData.append('completed_date', new Date().toISOString());
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Tampilkan modal sukses
                        openModal('success-modal');
                    } else {
                        // Tampilkan error di bawah input, bukan alert
                        if (userPayError) {
                            openModal('success-modal');
                        }
                    }
                })
                .catch(error => {
                    // Tampilkan error di bawah input, bukan alert
                    if (userPayError) {
                        openModal('success-modal');
                    }
                });
                return;
            }
            const userPayInput = document.getElementById('user-pay');
            const userPayError = document.getElementById('user-pay-error');
            const paymentAmount = parseFloat(document.getElementById('payment-amount').textContent.replace(/[^\d.]/g, ''));
            const userPay = parseFloat(userPayInput.value);
            let minPay = paymentAmount;
            userPayError.style.display = 'none';
            userPayError.textContent = '';
            if (paymentMethod === 'installment') {
                minPay = paymentAmount * 0.5;
                if (userPay < minPay) {
                    userPayError.textContent = 'Minimum payment for installment is 50% of total amount.';
                    userPayError.style.display = 'block';
                    userPayInput.focus();
                    return;
                }
            } else {
                if (userPay < paymentAmount) {
                    userPayError.textContent = 'Payment amount must be at least the total price.';
                    userPayError.style.display = 'block';
                    userPayInput.focus();
                    return;
                }
            }
            const formData = new FormData();
            formData.append('final_submit', '1');
            formData.append('payment_method', paymentMethod);
            formData.append('user_pay', userPay);
            formData.append('selected_bank', document.getElementById('selected-bank').value);
            formData.append('va_number', document.getElementById('payment-va-number').value);
            formData.append('installment_count', document.querySelector('select[name="installment_count"]')?.value || '');
            if (paymentMethod === 'cash' || paymentMethod === 'transfer') {
                formData.append('completed_date', new Date().toISOString());
            }
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('payment-modal');
                    openModal('success-modal');
                } else {
                    // userPayError.textContent = data.message || 'An error occurred. Please try again.';
                    // userPayError.style.display = 'block';
                    closeModal('payment-modal');
                    openModal('success-modal');
                }
            })
            .catch(error => {
                openModal('success-modal');
            });
        }

        /**
         * Starts countdown timer for payment deadline
         * @param {string} expiryTime - ISO datetime string for payment expiration
         */
        function startTimer(expiryTime) {
            const timerElement = document.getElementById('payment-timer');
            const expiryDate = new Date(expiryTime).getTime();

            const timer = setInterval(function() {
                const now = new Date().getTime();
                const distance = expiryDate - now;

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (distance < 0) {
                    clearInterval(timer);
                    timerElement.textContent = "Payment time has expired";
                    document.querySelector('#payment-modal .btn-primary').disabled = true;
                }
            }, 1000);
        }

        /**
         * Opens a modal by ID
         * @param {string} modalId - ID of the modal to open
         */
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }

        /**
         * Closes a modal by ID
         * @param {string} modalId - ID of the modal to close
         */
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        /**
         * Copies text to clipboard
         * @param {string} elementId - ID of the element containing text to copy
         */
        function copyToClipboard(elementId) {
            const copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand('copy');
            const copyBtn = copyText.nextElementSibling;
            const originalHTML = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                copyBtn.innerHTML = originalHTML;
            }, 1500);
        }

        /**
         * Handles completion of payment process
         * Redirects user to services page after successful payment
         */
        function paymentCompleted() {
            closeModal('success-modal');
            window.location.href = 'services.php';
        }

    
 function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(0).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}


        function submitFinalForm() {
            if (!window._bookingData) {
                alert('No booking data found. Please try again.');
                return;
            }

            // Get payment details
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const selectedBank = document.getElementById('selected-bank').value;
            const installmentCount = paymentMethod === 'installment' ?
                document.querySelector('select[name="installment_count"]').value : null;
            const amountPaid = document.getElementById('amount_paid').value;
            const vaNumber = document.getElementById('va_number').value;

            const finalData = {
                ...window._bookingData,
                payment_method: paymentMethod,
                selected_bank: selectedBank,
                installment_count: installmentCount,
                amount_paid: amountPaid,
                va_number: vaNumber,
                final_submit: true
            };

            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(finalData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Booking successful!');
                        window.location.href = 'status.php';
                    } else {
                        alert(data.message || 'Something went wrong. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }
    </script>
</body>

</html>