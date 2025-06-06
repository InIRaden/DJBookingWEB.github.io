<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

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

    $sql = "INSERT INTO tblbooking(BookingID, ServiceID, Name, MobileNumber, Email, EventDate, EventStartingtime, EventEndingtime, VenueAddress, EventType, AdditionalInformation) 
            VALUES (:bookingid, :bid, :name, :mobnum, :email, :edate, :est, :eetime, :vaddress, :eventtype, :addinfo)";

    $query = $dbh->prepare($sql);
    $query->bindParam(':bookingid', $bookingid, PDO::PARAM_STR);
    $query->bindParam(':bid', $bid, PDO::PARAM_STR);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':mobnum', $mobnum, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':edate', $edate, PDO::PARAM_STR);
    $query->bindParam(':est', $est, PDO::PARAM_STR);
    $query->bindParam(':eetime', $eetime, PDO::PARAM_STR);
    $query->bindParam(':vaddress', $vaddress, PDO::PARAM_STR);
    $query->bindParam(':eventtype', $eventtype, PDO::PARAM_STR);
    $query->bindParam(':addinfo', $addinfo, PDO::PARAM_STR);

    $query->execute();
    $LastInsertId = $dbh->lastInsertId();

    if ($LastInsertId > 0) {
        // Get service price
        $sqlPrice = "SELECT ServicePrice FROM tblservice WHERE ID = :bid";
        $queryPrice = $dbh->prepare($sqlPrice);
        $queryPrice->bindParam(':bid', $bid, PDO::PARAM_STR);
        $queryPrice->execute();
        $serviceData = $queryPrice->fetch(PDO::FETCH_ASSOC);
        $amount = $serviceData['ServicePrice'];

        // Insert payment record
        $sqlPayment = "INSERT INTO tblpayment(BookingID, PaymentMethod, Amount, TransferBank) VALUES (:bookingid, :paymentMethod, :amount, :transferBank)";
        $queryPayment = $dbh->prepare($sqlPayment);
        $queryPayment->bindParam(':bookingid', $bookingid, PDO::PARAM_STR);
        $queryPayment->bindParam(':paymentMethod', $paymentMethod, PDO::PARAM_STR);
        $queryPayment->bindParam(':amount', $amount, PDO::PARAM_STR);
        $queryPayment->bindParam(':transferBank', $selectedBank, PDO::PARAM_STR);
        $queryPayment->execute();
        $paymentId = $dbh->lastInsertId();

        // If installment payment, create installment records
        if ($paymentMethod == 'installment') {
            $installmentCount = $_POST['installment_count'];
            $installmentAmount = $amount / $installmentCount;

            for ($i = 1; $i <= $installmentCount; $i++) {
                $dueDate = date('Y-m-d', strtotime("+$i month"));
                $sqlInstallment = "INSERT INTO tblpaymentinstallment(PaymentID, InstallmentNumber, Amount, DueDate) 
                                  VALUES (:paymentId, :installmentNumber, :amount, :dueDate)";
                $queryInstallment = $dbh->prepare($sqlInstallment);
                $queryInstallment->bindParam(':paymentId', $paymentId, PDO::PARAM_STR);
                $queryInstallment->bindParam(':installmentNumber', $i, PDO::PARAM_INT);
                $queryInstallment->bindParam(':amount', $installmentAmount, PDO::PARAM_STR);
                $queryInstallment->bindParam(':dueDate', $dueDate, PDO::PARAM_STR);
                $queryInstallment->execute();
            }
        }

        // Store payment info in session for modal display
        $_SESSION['payment_info'] = [
            'bookingid' => $bookingid,
            'method' => $paymentMethod,
            'amount' => $amount,
            'name' => $name,
            'bank' => $selectedBank,
            'installment_count' => isset($_POST['installment_count']) ? $_POST['installment_count'] : 0,
            'installment_amount' => isset($installmentAmount) ? $installmentAmount : 0,
            'va_number' => mt_rand(1000000000000000, 9999999999999999)
        ];

        echo json_encode(['success' => true, 'payment_info' => $_SESSION['payment_info']]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
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

        .modal.show {
            display: flex;
        }

        .modal-content {
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #fff;
        }

        .modal-header {
            padding-bottom: 10px;
            border-bottom: 1px solid #333;
            margin-bottom: 15px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-modal {
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #fff;
            border: none;
        }

        .btn-secondary {
            background-color: #4b5563;
            color: #fff;
            border: none;
        }

        .copy-field {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .copy-field input {
            flex-grow: 1;
            padding: 8px;
            background-color: #2d2d2d;
            color: #fff;
            border: 1px solid #444;
            border-radius: 4px;
        }

        .copy-btn {
            padding: 8px;
            background-color: #2563eb;
            border: none;
            border-radius: 4px;
            color: #fff;
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
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

    <!-- Main Content -->
    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-xs mb-8">
            <a href="index.php" class="text-gray-400 hover:text-white">Home</a>
            <span class="text-gray-600">/</span>
            <span class="text-white">Book Services</span>
        </div>

        <!-- Booking Form Section -->
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
                <div>
                    <!-- Payment Method Section -->
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
                                    <div class="flex flex-col items-center justify-center space-y-1">
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
                                    <div class="flex flex-col items-center justify-center space-y-1">
                                        <span class="text-sm font-medium text-white">Installment</span>
                                        <span class="text-xs text-gray-400">Pay in multiple payments</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Dropdown (Hidden by default) -->
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

                    <!-- Installment Options (Hidden by default) -->
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

    <?php include_once('includes/footer.php'); ?>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('confirm-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Confirm Your Booking</h3>
            </div>
            <div class="p-4">
                <p class="text-gray-300 mb-2">Please review your booking details:</p>
                <div class="mb-4">
                    <p class="text-gray-300 mb-1">Name: <span id="confirm-name" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Email: <span id="confirm-email" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Mobile Number: <span id="confirm-mobnum" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Event Date: <span id="confirm-edate" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Event Starting Time: <span id="confirm-est" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Event Finish Time: <span id="confirm-eetime" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Venue Address: <span id="confirm-vaddress" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Event Type: <span id="confirm-eventtype" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Additional Information: <span id="confirm-addinfo" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Payment Method: <span id="confirm-payment-method" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Bank: <span id="confirm-selected-bank" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-1">Installment Count: <span id="confirm-installment-count" class="text-white font-semibold"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-secondary" onclick="closeModal('confirm-modal')">Cancel</button>
                <button class="btn-modal btn-primary" onclick="confirmBooking()">Selesai</button>
            </div>
        </div>
    </div>

    <!-- Cash Payment Modal -->
    <div id="cash-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('cash-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Booking Confirmation</h3>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-gray-300 mb-2">Booking ID: <span id="cash-booking-id" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-2">Amount: <span id="cash-amount" class="text-white font-semibold"></span></p>
                </div>
                <div class="bg-gray-700 p-4 rounded-lg mb-4">
                    <p class="text-white text-center">Your order is being processed</p>
                    <p class="text-gray-300 text-sm text-center mt-2">Our team will contact you soon to confirm your booking.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-primary" onclick="closeModal('cash-modal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Transfer Payment Modal -->
    <div id="transfer-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('transfer-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Virtual Account Payment</h3>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-gray-300 mb-2">Booking ID: <span id="transfer-booking-id" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-2">Bank: <span id="transfer-bank" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-2">Amount: <span id="transfer-amount" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-2">Name: <span id="transfer-name" class="text-white font-semibold"></span></p>
                </div>
                <div class="bg-gray-700 p-4 rounded-lg mb-4">
                    <p class="text-white mb-2">Virtual Account Number:</p>
                    <div class="copy-field">
                        <input type="text" id="transfer-va-number" readonly>
                        <button class="copy-btn" onclick="copyToClipboard('transfer-va-number')"><i class="fas fa-copy"></i></button>
                    </div>
                    <p class="text-gray-300 text-sm mt-2">Please transfer the exact amount to the virtual account number above.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-secondary" onclick="closeModal('transfer-modal')">Cancel</button>
                <button class="btn-modal btn-primary" onclick="simulatePayment('transfer')">I've Paid</button>
            </div>
        </div>
    </div>

    <!-- Installment Payment Modal -->
    <div id="installment-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('installment-modal')">×</span>
            <div class="modal-header">
                <h3 class="text-xl font-semibold text-white">Installment Payment</h3>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-gray-300 mb-2">Booking ID: <span id="installment-booking-id" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-2">Bank: <span id="installment-bank" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-2">Total Amount: <span id="installment-total" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-2">First Payment (50%): <span id="installment-amount" class="text-white font-semibold"></span></p>
                    <p class="text-gray-300 mb-2">Name: <span id="installment-name" class="text-white font-semibold"></span></p>
                </div>
                <div class="bg-gray-700 p-4 rounded-lg mb-4">
                    <p class="text-white mb-2">Virtual Account Number:</p>
                    <div class="copy-field">
                        <input type="text" id="installment-va-number" readonly>
                        <button class="copy-btn" onclick="copyToClipboard('installment-va-number')"><i class="fas fa-copy"></i></button>
                    </div>
                    <p class="text-gray-300 text-sm mt-2">Please transfer the first payment amount to the virtual account number above.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-secondary" onclick="closeModal('installment-modal')">Cancel</button>
                <button class="btn-modal btn-primary" onclick="simulatePayment('installment')">I've Paid</button>
            </div>
        </div>
    </div>

    <!-- Payment Success Modal -->
    <div id="payment-success-modal" class="modal">
        <div class="modal-content">
            <div class="p-4 text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Payment Successful!</h3>
                <p class="text-gray-300 mb-4">Your payment has been processed successfully.</p>
                <button class="btn-modal btn-primary" onclick="paymentCompleted()">Continue</button>
            </div>
        </div>
    </div>

    <!-- Fancybox Script -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {
                animationEffect: "fade",
                transitionEffect: "fade",
                buttons: ["zoom", "slideShow", "fullScreen", "close"]
            });

            // Payment method selection
            const paymentRadios = document.querySelectorAll('.payment-radio');
            const installmentOptions = document.getElementById('installment-options');
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

            const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
            if (checkedRadio) {
                checkedRadio.dispatchEvent(new Event('change'));
            }

            // Handle form submission
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
                document.getElementById('confirm-payment-method').textContent = data.payment_method || 'N/A';
                document.getElementById('confirm-selected-bank').textContent = data.selected_bank || 'N/A';
                document.getElementById('confirm-installment-count').textContent = data.installment_count || 'N/A';

                openModal('confirm-modal');
            });
        });

        // Confirm booking and send to server
        function confirmBooking() {
            const form = document.getElementById('booking-form');
            const formData = new FormData(form);
            formData.append('confirm_submit', '1');

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    closeModal('confirm-modal');
                    if (data.success) {
                        showPaymentModalByMethod(data.payment_info);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }

        // Show payment modal based on method
        function showPaymentModalByMethod(paymentInfo) {
            if (paymentInfo.method === 'cash') {
                document.getElementById('cash-booking-id').textContent = paymentInfo.bookingid;
                document.getElementById('cash-amount').textContent = formatCurrency(paymentInfo.amount);
                openModal('cash-modal');
            } else if (paymentInfo.method === 'transfer') {
                document.getElementById('transfer-booking-id').textContent = paymentInfo.bookingid;
                document.getElementById('transfer-bank').textContent = paymentInfo.bank || 'Not selected';
                document.getElementById('transfer-amount').textContent = formatCurrency(paymentInfo.amount);
                document.getElementById('transfer-name').textContent = paymentInfo.name;
                document.getElementById('transfer-va-number').value = paymentInfo.va_number;
                openModal('transfer-modal');
            } else if (paymentInfo.method === 'installment') {
                document.getElementById('installment-booking-id').textContent = paymentInfo.bookingid;
                document.getElementById('installment-bank').textContent = paymentInfo.bank || 'Not selected';
                document.getElementById('installment-total').textContent = formatCurrency(paymentInfo.amount);
                document.getElementById('installment-amount').textContent = formatCurrency(paymentInfo.installment_amount);
                document.getElementById('installment-name').textContent = paymentInfo.name;
                document.getElementById('installment-va-number').value = paymentInfo.va_number;
                openModal('installment-modal');
            }
        }

        // Open modal
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }

        // Close modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        // Copy to clipboard
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

        // Simulate payment
        function simulatePayment(method) {
            closeModal(method + '-modal');
            setTimeout(() => {
                openModal('payment-success-modal');
            }, 300);
        }

        // Payment completed
        function paymentCompleted() {
            closeModal('payment-success-modal');
            window.location.href = 'services.php';
        }

        // Format currency
        function formatCurrency(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
        }
    </script>
</body>

</html>