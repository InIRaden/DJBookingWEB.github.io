<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['submit'])) {
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
        .payment-radio:checked + label .bg-gray-700 {
            background-color: #3b82f6; /* Blue background for selected icon */
        }
        .payment-radio:checked + label {
            border-color: #3b82f6;
        }
        #installment-options {
            transition: opacity 0.5s ease-in-out, max-height 0.5s ease-in-out, transform 0.5s ease-in-out;
            transform: translateY(-10px);
        }
        #installment-options.show {
            opacity: 1;
            max-height: 200px; /* Adjust based on content height */
            transform: translateY(0);
        }
        .payment-radio + label:hover .bg-gray-700 {
            background-color: #4b5563; /* Subtle hover effect for icon background */
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
    <header class="relative">
        <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
        <nav class="absolute top-0 left-0 w-full flex items-center justify-between px-6 py-4 text-white bg-black bg-opacity-60">
            <div class="flex items-center space-x-2 text-sm font-semibold">
                <i class="fas fa-compact-disc"></i>
                <span>DjBooking</span>
            </div>
            <ul class="hidden md:flex space-x-8 text-sm font-normal">
                <li><a class="hover:underline" href="index.php">Home</a></li>
                <li><a class="hover:underline" href="services.php">Services</a></li>
                <li><a class="hover:underline" href="status.php">Request Status</a></li>
                <li><a class="hover:underline" href="about.php">About</a></li>
                <li><a class="hover:underline" href="contact.php">Contact</a></li>
                <li><a class="hover:underline" href="admin/login.php">Admin</a></li>
            </ul>
            <a href="signup.php" class="hidden md:inline-block bg-gray-700 bg-opacity-60 rounded px-3 py-1 text-xs font-semibold hover:bg-gray-600 transition">
                Sign Up
            </a>
        </nav>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center max-w-md px-4">
            <h1 class="text-white font-bold text-lg md:text-xl leading-tight">Book Services</h1>
            <p class="text-xs md:text-sm mt-2 text-white">
                Book your DJ event now and make it unforgettable
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
                    <form method="post">
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
                                foreach($result2 as $row) { ?>  
                                    <option value="<?php echo htmlentities($row->EventType);?>"><?php echo htmlentities($row->EventType);?></option>
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
                    <a href="images/431427.jpg" data-fancybox="booking-image">
                        <img src="images/431427.jpg" alt="DJ Event" class="w-full h-auto rounded-md shadow-lg mb-6" />
                    </a>
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
                    <!-- Installment Options (Hidden by default) -->
                    <div id="installment-options" class="mb-6 bg-gray-800 p-4 rounded-lg opacity-0 max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <label class="block text-sm text-gray-300 mb-3 font-medium">Installment Terms</label>
                        <div class="flex items-center">
                            <select name="installment_count" class="form-control">
                                <option value="2">2 payments</option>
                                <option value="3">3 payments</option>
                                <option value="4">4 payments</option>
                            </select>
                            <p class="ml-4 text-xs text-gray-400">* Installment only available via transfer</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once('includes/footer.php'); ?>

    <!-- Fancybox Script -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind("[data-fancybox]", {
                animationEffect: "fade",
                transitionEffect: "fade",
                buttons: [
                    "zoom",
                    "slideShow",
                    "fullScreen",
                    "close"
                ]
            });

            // Payment method selection
            const paymentRadios = document.querySelectorAll('.payment-radio');
            const installmentOptions = document.getElementById('installment-options');

            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Update border for selected payment method
                    document.querySelectorAll('.payment-radio').forEach(r => {
                        const parent = r.parentElement.parentElement; // Adjust to target the outer div
                        if (r.checked) {
                            parent.classList.add('border-blue-500');
                            parent.classList.remove('border-gray-700');
                        } else {
                            parent.classList.remove('border-blue-500');
                            parent.classList.add('border-gray-700');
                        }
                    });

                    // Show/hide installment options with transition
                    if (this.value === 'installment') {
                        installmentOptions.classList.add('show');
                        installmentOptions.classList.remove('hidden');
                    } else {
                        installmentOptions.classList.remove('show');
                        setTimeout(() => {
                            installmentOptions.classList.add('hidden');
                        }, 500); // Match transition duration
                    }
                });
            });

            // Trigger change event on page load to set initial state
            const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
            if (checkedRadio) {
                checkedRadio.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>