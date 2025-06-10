<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// First step - Store booking data in session but don't insert into database yet
if (isset($_POST['confirm_submit'])) {
    $bid = $_POST['bookid'];
    $name = $_POST['name'];
    $mobnum = $_POST['mobnum'];
    $email = $_POST['email'];
    $edate = $_POST['edate'];
    $est = $_POST['est'];
    $eetime = $_POST['eetime'];
    $vaddress = $_POST['vaddress'];
    $eventtype = $_POST['eventtype'];
    $addinfo = $_POST['addinfo'];
    $bookingid = mt_rand(100000000, 999999999);
    $paymentMethod = $_POST['payment_method'];
    $selectedBank = isset($_POST['selected_bank']) ? $_POST['selected_bank'] : '';
    $expiryTime = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Get service price
    $sqlPrice = "SELECT ServicePrice FROM tblservice WHERE ID = :bid";
    $queryPrice = $dbh->prepare($sqlPrice);
    $queryPrice->bindParam(':bid', $bid, PDO::PARAM_STR);
    $queryPrice->execute();
    $serviceData = $queryPrice->fetch(PDO::FETCH_ASSOC);
    $amount = $serviceData['ServicePrice'];

    // Generate Virtual Account number for transfer or installment
    $vaNumber = ($paymentMethod === 'cash') ? null : mt_rand(1000000000000000, 9999999999999999);

    // Store booking data in session
    $_SESSION['temp_booking'] = [
        'bookingid' => $bookingid,
        'bid' => $bid,
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
        'expiryTime' => $expiryTime,
        'amount' => $amount,
        'va_number' => $vaNumber
    ];

    echo json_encode(['success' => true, 'booking_data' => $_SESSION['temp_booking']]);
    exit;
}

// Final step - Insert data into database after payment confirmation using stored procedure
if (isset($_POST['final_submit'])) {
    if (isset($_SESSION['temp_booking'])) {
        $bookingData = $_SESSION['temp_booking'];

        try {
            // Call the stored procedure using named placeholders
            $sql = "CALL sp_create_booking_and_payment(
                        :bookingid, :serviceid, :name, :mobilenumber, :email, :eventdate,
                        :eventstartingtime, :eventendingtime, :venueaddress, :eventtype,
                        :additionalinformation, :paymentmethod, :amount, :transferbank,
                        :virtualaccountnumber, @p_success, @p_message
                    )";
            $query = $dbh->prepare($sql);

            // Bind values to named placeholders
            $query->execute([
                ':bookingid' => $bookingData['bookingid'],
                ':serviceid' => $bookingData['bid'],
                ':name' => $bookingData['name'],
                ':mobilenumber' => $bookingData['mobnum'],
                ':email' => $bookingData['email'],
                ':eventdate' => $bookingData['edate'],
                ':eventstartingtime' => $bookingData['est'],
                ':eventendingtime' => $bookingData['eetime'],
                ':venueaddress' => $bookingData['vaddress'],
                ':eventtype' => $bookingData['eventtype'],
                ':additionalinformation' => $bookingData['addinfo'],
                ':paymentmethod' => $bookingData['paymentMethod'],
                ':amount' => $bookingData['amount'],
                ':transferbank' => $bookingData['selectedBank'] ?: null,
                ':virtualaccountnumber' => $bookingData['va_number']
            ]);

            // Fetch the output parameters
            $result = $dbh->query("SELECT @p_success AS success, @p_message AS message")->fetch(PDO::FETCH_ASSOC);

            if ($result['success']) {
                unset($_SESSION['temp_booking']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => $result['message']]);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No temporary booking data found.']);
    }
    exit;
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
        .modal {
            display: none;
            position: fixed;    
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #1a1a1a;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 700px;
            border-radius: 8px;
            position: relative;
        }

        .modal-landscape {
            max-width: 900px;
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-modal:hover {
            color: #fff;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-body {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .modal-body-left,
        .modal-body-right {
            flex: 1;
            min-width: 250px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-modal {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #2563eb;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #4b5563;
            color: white;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #374151;
        }

        .payment-info p {
            margin-bottom: 10px;
        }

        .payment-info .label {
            color: #9ca3af;
            margin-right: 10px;
        }

        .payment-info .value {
            color: #fff;
            font-weight: 600;
        }

        .copy-field {
            display: flex;
            align-items: center;
            background-color: #2d2d2d;
            border-radius: 5px;
            overflow: hidden;
        }

        .copy-field input {
            flex: 1;
            background: none;
            border: none;
            padding: 10px;
            color: #fff;
            font-size: 14px;
        }

        .copy-btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .copy-btn:hover {
            background-color: #1d4ed8;
        }

        .error-message {
            display: none;
            background-color: #ef4444;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        #bank-dropdown.show {
            opacity: 1;
            max-height: 200px;
        }

        #bank-dropdown {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.5s ease-in-out;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #4b5563;
            background-color: #2d2d2d;
            color: #fff;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
        }

        .btn-submit {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
        }

        .page-indicator {
            color: #9ca3af;
            font-size: 14px;
        }
    </style>
</head>

<body class="bg-black text-white">
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center max-w-md px-4">
            <h1 class="text-white font-bold text-lg md:text-xl leading-tight">Contact</h1>
            <p class="text-xs md:text-sm mt-2 text-white">
                Learn more about our DJ services and what makes us special
            </p>
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
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-300 mb-2" for="mobnum">Mobile Number</label>
                            <input type="text" class="form-control" name="mobnum" id="mobnum" required maxlength="10" pattern="[0-9]+">
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

                <!-- Payment Control -->
                <div>
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
                </div>
            </div>
        </section>
    </main>

    <?php include_once('includes/footer.php'); ?>

    <!-- Confirm Modal -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content modal-landscape">
            <span class="close-modal" onclick="closeModal('confirm-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Confirm Your Booking <span class="text-sm text-gray-400 ml-2">Step 1/2</span></h3>
            </div>
            <div class="modal-body">
                <div class="modal-body-left">
                    <h4 class="text-lg font-medium text-white mb-4">Booking Details</h4>
                    <div class="space-y-3">
                        <p class="text-gray-300">Name: <span id="confirm-name" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">Email: <span id="confirm-email" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">Phone Number: <span id="confirm-mobnum" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">Event Date: <span id="confirm-edate" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">Start Time: <span id="confirm-est" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">End Time: <span id="confirm-eetime" class="text-white font-semibold"></span></p>
                    </div>
                </div>
                <div class="modal-body-right">
                    <h4 class="text-lg font-medium text-white mb-4">Additional Details</h4>
                    <div class="space-y-3">
                        <p class="text-gray-300">Venue Address: <span id="confirm-vaddress" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">Event Type: <span id="confirm-eventtype" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">Additional Information: <span id="confirm-addinfo" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">Payment Method: <span id="confirm-payment-method" class="text-white font-semibold"></span></p>
                        <p class="text-gray-300">Bank: <span id="confirm-selected-bank" class="text-white font-semibold"></span></p>
                    </div>
                </div>
            </div>
            <div id="confirm-error-message" class="error-message bg-red-500 text-white p-3 rounded mb-4" style="display: none;"></div>
            <div class="modal-footer">
                <button class="btn-modal btn-secondary" onclick="closeModal('confirm-modal')">Cancel</button>
                <button class="btn-modal btn-primary" onclick="showPaymentDetails()">Next</button>
            </div>
        </div>
    </div>

    <!-- Payment Modal (for Transfer and Installment) -->
    <div id="payment-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('payment-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Detail Pembayaran</h3>
                <span class="page-indicator">2/2</span>
            </div>
            <div class="modal-body">
                <div class="modal-body-left">
                    <h4 class="text-lg font-medium text-white mb-4">Informasi Booking</h4>
                    <div class="payment-info">
                        <p><span class="label">Booking ID:</span> <span id="payment-booking-id" class="value"></span></p>
                        <p><span class="label">Total Pembayaran:</span> <span id="payment-amount" class="value"></span></p>
                        <p><span class="text-gray-300">Payment Method:</span> <span id="payment-payment-method" class="text-white font-semibold"></span></p>
                        <p><span class="text-gray-300">Bank:</span> <span id="payment-selected-bank" class="text-white font-semibold"></span></p>
                    </div>
                    <div class="timer mt-4">
                        <p>Waktu tersisa untuk pembayaran:</p>
                        <p id="payment-timer" class="font-bold"></p>
                    </div>
                </div>
                <div class="modal-body-right">
                    <h4 class="text-lg font-medium text-white mb-4">Instruksi Pembayaran</h4>
                    <div class="payment-info">
                        <p class="text-white mb-2">Nomor Virtual Account:</p>
                        <div class="copy-field">
                            <input type="text" id="payment-va-number" readonly>
                            <button class="copy-btn" onclick="copyToClipboard('payment-va-number')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p id="installment-note" class="text-sm text-yellow-400 font-semibold mt-2" style="display: none;">Minimal pembayaran 50% dari total.</p>
                        <p class="text-gray-300 text-sm mt-4">Silakan transfer sesuai nominal yang tertera ke nomor VA di atas.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-secondary" onclick="closeModal('payment-modal')">Batal</button>
                <button class="btn-modal btn-primary" onclick="confirmPayment()">Pembayaran Selesai</button>
            </div>
        </div>
    </div>

    <!-- Cash Modal -->
    <div id="cash-modal" class="modal">
        <div class="modal-content modal-landscape">
            <span class="close-modal" onclick="closeModal('cash-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Konfirmasi Pembayaran Cash</h3>
            </div>
            <div class="modal-body">
                <div class="modal-body-left">
                    <h4 class="text-lg font-medium text-white mb-4">Informasi Booking</h4>
                    <div class="payment-info">
                        <p><span class="label">Booking ID:</span> <span id="cash-booking-id" class="value"></span></p>
                        <p><span class="label">Total Pembayaran:</span> <span id="cash-amount" class="value"></span></p>
                        <p><span class="text-gray-300">Payment Method:</span> <span id="cash-payment-method" class="text-white font-semibold"></span></p>
                    </div>
                </div>
                <div class="modal-body-right">
                    <h4 class="text-lg font-medium text-white mb-4">Instruksi Pembayaran</h4>
                    <div class="payment-info">
                        <p class="text-gray-300">Pembayaran akan dilakukan secara tunai saat acara berlangsung.</p>
                        <p class="text-gray-300 mt-2">Harap siapkan uang tunai sesuai dengan jumlah yang tertera.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-secondary" onclick="closeModal('cash-modal')">Batal</button>
                <button class="btn-modal btn-primary" onclick="confirmPayment()">Pembayaran Selesai</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Pembayaran Berhasil!</h3>
                <p class="text-gray-300 mb-4">Booking Anda telah berhasil diproses.</p>
                <button class="btn-modal btn-primary" onclick="paymentCompleted()">Selesai</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function copyToClipboard(elementId) {
            const input = document.getElementById(elementId);
            input.select();
            document.execCommand('copy');
            alert('Virtual Account Number copied to clipboard!');
        }

        function paymentCompleted() {
            window.location.href = 'index.php';
        }

        function confirmPayment() {
            const form = document.getElementById('booking-form');
            const formData = new FormData(form);
            formData.append('final_submit', '1');

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal('payment-modal');
                        closeModal('cash-modal');
                        openModal('success-modal');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {
                animationEffect: "fade",
                transitionEffect: "fade",
                buttons: ["zoom", "slideShow", "fullScreen", "close"]
            });

            const paymentRadios = document.querySelectorAll('.payment-radio');
            const bankDropdown = document.getElementById('bank-dropdown');

            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
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

                    if (this.value === 'transfer' || this.value === 'installment') {
                        bankDropdown.classList.add('show');
                    } else {
                        bankDropdown.classList.remove('show');
                    }
                });
            });

            const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
            if (checkedRadio) {
                checkedRadio.dispatchEvent(new Event('change'));
            }

            const form = document.getElementById('booking-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Populate confirmation modal
                document.getElementById('confirm-name').textContent = data.name || 'N/A';
                document.getElementById('confirm-email').textContent = data.email || 'N/A';
                document.getElementById('confirm-mobnum').textContent = data.mobnum || 'N/A';
                document.getElementById('confirm-edate').textContent = data.edate || 'N/A';
                document.getElementById('confirm-est').textContent = data.est || 'N/A';
                document.getElementById('confirm-eetime').textContent = data.eetime || 'N/A';
                document.getElementById('confirm-vaddress').textContent = data.vaddress || 'N/A';
                document.getElementById('confirm-eventtype').textContent = data.eventtype || 'N/A';
                document.getElementById('confirm-addinfo').textContent = data.addinfo || 'N/A';

                const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                let paymentMethodText = paymentMethod === 'cash' ? 'Cash' : paymentMethod === 'transfer' ? 'Transfer' : 'Installment';
                document.getElementById('confirm-payment-method').textContent = paymentMethodText;

                const selectedBank = document.getElementById('selected-bank').value;
                document.getElementById('confirm-selected-bank').textContent = selectedBank || '-';

                openModal('confirm-modal');
            });
        });

        function showPaymentDetails() {
            const form = document.getElementById('booking-form');
            const formData = new FormData(form);
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
                        const paymentMethod = bookingData.paymentMethod;

                        if (paymentMethod === 'cash') {
                            document.getElementById('cash-booking-id').textContent = bookingData.bookingid;
                            document.getElementById('cash-amount').textContent = formatCurrency(bookingData.amount);
                            document.getElementById('cash-payment-method').textContent = 'Cash';
                            openModal('cash-modal');
                        } else {
                            document.getElementById('payment-booking-id').textContent = bookingData.bookingid;
                            document.getElementById('payment-amount').textContent = formatCurrency(bookingData.amount);
                            document.getElementById('payment-payment-method').textContent = paymentMethod === 'transfer' ? 'Transfer' : 'Installment';
                            document.getElementById('payment-selected-bank').textContent = bookingData.selectedBank || '-';
                            document.getElementById('payment-va-number').value = bookingData.va_number;

                            const installmentNote = document.getElementById('installment-note');
                            if (paymentMethod === 'installment') {
                                installmentNote.style.display = 'block';
                            } else {
                                installmentNote.style.display = 'none';
                            }

                            startTimer(bookingData.expiryTime, 'payment-timer');
                            openModal('payment-modal');
                        }
                    } else {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
        }

        function startTimer(expiryTime, timerElementId) {
            const timerElement = document.getElementById(timerElementId);
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
                    timerElement.textContent = "Waktu pembayaran telah habis";
                    document.querySelector(`#${timerElementId.split('-')[0]}-modal .btn-primary`).disabled = true;
                }
            }, 1000);
        }

        function formatCurrency(amount) {
            return '$ ' + parseFloat(amount).toLocaleString('en-US');
        }
    </script>
</body>

</html>