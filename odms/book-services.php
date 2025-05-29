<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['submit'])) {
    $bid = $_GET['bookid'];
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
        $sqlPayment = "INSERT INTO tblpayment(BookingID, PaymentMethod, Amount) VALUES (:bookingid, :paymentMethod, :amount)";
        $queryPayment = $dbh->prepare($sqlPayment);
        $queryPayment->bindParam(':bookingid', $bookingid, PDO::PARAM_STR);
        $queryPayment->bindParam(':paymentMethod', $paymentMethod, PDO::PARAM_STR);
        $queryPayment->bindParam(':amount', $amount, PDO::PARAM_STR);
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

        // Redirect based on payment method
        if ($paymentMethod == 'cash') {
            echo '<script>alert("Your Booking Request Has Been Sent. We Will Contact You Soon")</script>';
            echo "<script>window.location.href ='services.php'</script>";
        } else if ($paymentMethod == 'transfer') {
            // Redirect to virtual account payment page
            echo "<script>window.location.href ='payment-virtual-account.php?bookid=$bookingid'</script>";
        } else if ($paymentMethod == 'installment') {
            // Redirect to installment payment page
            echo "<script>window.location.href ='payment-installment.php?bookid=$bookingid'</script>";
        }
    } else {
        echo '<script>alert("Something Went Wrong. Please try again")</script>';
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
    <style>
        body {
            font-family: "Inter", sans-serif;
        }

        .form-control {
            background-color: #1a1a1a;
            border: 1px solid #4a4a4a;
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
            width: 100%;
        }

        .form-control:focus {
            outline: none;
            border-color: #ffffff;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
        }

        .btn-submit {
            background-color: #4a4a4a;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #6b6b6b;
        }

        /* Enhanced Payment Method Styles */
        .payment-radio:checked+label .bg-gray-700 {
            background-color: #3b82f6;
            /* Blue background for selected icon */
        }

        .payment-radio:checked+label {
            border-color: #3b82f6;
        }

        #installment-options {
            transition: opacity 0.5s ease-in-out, max-height 0.5s ease-in-out, transform 0.5s ease-in-out;
            transform: translateY(-10px);
        }

        #installment-options.show {
            opacity: 1;
            max-height: 200px;
            /* Adjust based on content height */
            transform: translateY(0);
        }

        .payment-radio+label:hover .bg-gray-700 {
            background-color: #4b5563;
            /* Subtle hover effect for icon background */
        }

        body {
            font-family: "Inter", sans-serif;
            background-color:rgb(0, 0, 0);
            color: white;
            margin: 0;
            padding: 20px;
        }

        .booking-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
        }

        .form-container {
            background-color: #E8E8E8;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            color:rgb(0, 0, 0);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #CCCCCC;
            border-radius: 5px;
            background-color: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
        }

        .payment-option {
            text-align: center;
        }

        .payment-option input[type="radio"] {
            display: none;
        }

        .payment-option label {
            display: block;
            cursor: pointer;
        }

        .payment-option img {
            max-width: 60px;
            height: auto;
        }

        .btn-send {
            background-color: #DC2626;
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }

        .btn-send:hover {
            background-color: #B91C1C;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .payment-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
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

      
    <div class="booking-section">
        <form method="post">
            <!-- Detail Event Section -->
            <h2 class="section-title">Detail Event</h2>
            <div class="form-container">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Name Event</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type Event</label>
                        <select class="form-control" name="eventtype" required>
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
                    <div class="form-group">
                        <label class="form-label">Date Event</label>
                        <input type="date" class="form-control" name="edate" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">DJ Name</label>
                        <input type="text" class="form-control" name="djname" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Event Address</label>
                        <textarea class="form-control" name="vaddress" rows="3" required></textarea>
                    </div>
                </div>
            </div>

            <!-- Detail Pemesan Section -->
            <h2 class="section-title">Detail Pemesan</h2>
            <div class="form-container">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="customername" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="mobnum" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Poin/Voucher</label>
                        <input type="text" class="form-control" name="voucher">
                    </div>
                </div>
            </div>

            <!-- Detail Pembayaran Section -->
            <h2 class="section-title">Detail Pembayaran</h2>
            <div class="form-container">
                <div class="payment-grid">
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="shopeepay" id="shopeepay">
                        <label for="shopeepay">
                            <img src="images/shopeepay.png" alt="ShopeePay">
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="bca" id="bca">
                        <label for="bca">
                            <img src="images/bca.png" alt="BCA">
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="dana" id="dana">
                        <label for="dana">
                            <img src="images/dana.png" alt="DANA">
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="ovo" id="ovo">
                        <label for="ovo">
                            <img src="images/ovo.png" alt="OVO">
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="linkaja" id="linkaja">
                        <label for="linkaja">
                            <img src="images/linkaja.png" alt="LinkAja">
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="mandiri" id="mandiri">
                        <label for="mandiri">
                            <img src="images/mandiri.png" alt="Mandiri">
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="bni" id="bni">
                        <label for="bni">
                            <img src="images/bni.png" alt="BNI">
                        </label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="bri" id="bri">
                        <label for="bri">
                            <img src="images/bri.png" alt="BRI">
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn-send">Send</button>
        </form>
    </div>

    <?php include_once('includes/footer.php'); ?>
    
</body>
</html>