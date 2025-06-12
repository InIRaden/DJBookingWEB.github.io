<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>DjBooking - Request Status</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
        }
        
        /* Styling untuk notifikasi */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            z-index: 1000;
            display: flex;
            align-items: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            transform: translateX(120%);
            transition: transform 0.4s ease-in-out, background 0.3s ease;
            max-width: 350px;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-success {
            background: linear-gradient(135deg, #822b2b, #a52a2a);
            border-left: 6px solid #ff5252;
        }
        
        .notification-error {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            border-left: 6px solid #b71c1c;
        }
        
        .notification-icon {
            margin-right: 12px;
            font-size: 18px;
        }
        
        .notification-close {
            margin-left: 12px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .notification-close:hover {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center max-w-md px-4">
            <h1 class="text-white font-bold text-lg md:text-xl leading-tight text-shadow">Request Status</h1>
            <p class="text-xs md:text-sm mt-2 text-white opacity-90">Learn more about our DJ services and what makes us special</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto bg-gray-900 rounded-lg shadow-xl transition-all duration-300 hover:shadow-red-600/30">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-xs mb-8 text-gray-400">
            <a href="index.php" class="hover:text-white transition-colors duration-200">Home</a>
            <span class="text-gray-600">/</span>
            <span class="text-white">Request Status</span>
        </div>

        <!-- Request Status Content -->
        <section class="mb-12">
            <h2 class="font-playfair text-white text-xl mb-6 text-center tracking-wide">Check Your Booking Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <h3 class="text-white text-sm font-semibold mb-4">Enter Your Details</h3>
                    <p class="text-gray-400 text-xs mb-4">Please provide your name and mobile number to check your booking status.</p>

                    <form method="post" class="space-y-4">
                        <div>
                            <label class="block text-gray-400 text-xs mb-1">Name</label>
                            <input type="text" name="name" required="true" class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500 transition-colors duration-200">
                        </div>

                        <div>
                            <label class="block text-gray-400 text-xs mb-1">Mobile Number</label>
                            <input type="text" name="mobnum" required="true" maxlength="10" pattern="[0-9]+" 
                                class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500 transition-colors duration-200"
                                onkeypress="return isNumberKey(event)" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        <button type="submit" name="submit" class="bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded-md hover:bg-red-600 transition-all duration-200 cursor-pointer">
                            Check Status
                        </button>
                    </form>
                </div>

                <div>
                    <?php if(isset($_POST['submit'])) {
                        $mno=$_POST['mobnum'];
                        $fname=$_POST['name'];
                        $sql="SELECT tblbooking.ID,tblbooking.BookingID,tblbooking.Name,tblbooking.MobileNumber,tblbooking.Email,tblbooking.EventDate,tblbooking.EventStartingtime,tblbooking.EventEndingtime,tblbooking.VenueAddress,tblbooking.EventType,tblbooking.AdditionalInformation,tblbooking.BookingDate,tblbooking.Remark,tblbooking.Status,tblbooking.UpdationDate,tblservice.ServiceName,tblservice.SerDes,tblservice.ServicePrice from tblbooking join tblservice on tblbooking.ServiceID=tblservice.ID  where tblbooking.MobileNumber=:mno and tblbooking.Name=:fname";
                        $query = $dbh -> prepare($sql);
                        $query-> bindParam(':mno', $mno, PDO::PARAM_STR);
                        $query-> bindParam(':fname', $fname, PDO::PARAM_STR);
                        $query->execute();
                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                        $cnt=1;
                        if($query->rowCount() > 0) { ?>
                            <div class="bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                                <h3 class="text-white text-sm font-semibold mb-4">Your Booking Results</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-gray-900 rounded-md overflow-hidden">
                                        <thead class="bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-2 text-xs text-left text-gray-300">Booking Number</th>
                                                <th class="px-4 py-2 text-xs text-left text-gray-300">Client Name</th>
                                                <th class="px-4 py-2 text-xs text-left text-gray-300">Mobile Number</th>
                                                <th class="px-4 py-2 text-xs text-left text-gray-300">Email</th>
                                                <th class="px-4 py-2 text-xs text-left text-gray-300">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-800">
                                            <?php foreach($results as $row) { ?>
                                                <tr class="hover:bg-gray-800 transition-colors duration-200">
                                                    <td class="px-4 py-3 text-xs text-gray-300"><?php echo $row->BookingID; ?></td>
                                                    <td class="px-4 py-3 text-xs text-gray-300"><?php echo $row->Name; ?></td>
                                                    <td class="px-4 py-3 text-xs text-gray-300"><?php echo $row->MobileNumber; ?></td>
                                                    <td class="px-4 py-3 text-xs text-gray-300"><?php echo $row->Email; ?></td>
                                                    <td class="px-4 py-3 text-xs">
                                                        <a href="request-details.php?bid=<?php echo htmlentities($row->ID); ?>&&bookingid=<?php echo htmlentities($row->BookingID); ?>" 
                                                           class="bg-red-700 text-white px-3 py-1 rounded-md text-xs hover:bg-red-600 transition-all duration-200">
                                                            View Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 flex flex-col items-center justify-center h-full w-full">
                                <i class="fas fa-search text-gray-600 text-4xl mb-4 opacity-50"></i>
                                <h3 class="text-red-500 text-sm font-semibold mb-2">No Records Found</h3>
                                <p class="text-gray-400 text-xs mt-2 text-center max-w-md mx-auto">We couldn't find any bookings matching your details. Please check your information and try again.</p>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 h-full w-full">
                            <div class="text-center">
                                <img src="images/1hr.png" alt="DJ Booking" class="w-32 h-32 object-contain mb-4 mx-auto opacity-90 transition-opacity duration-300 hover:opacity-100">
                                <h3 class="text-white text-sm font-semibold mb-2">Check Your Booking Status</h3>
                                <p class="text-gray-400 text-xs mt-2 mx-auto">Enter your details on the left to check the status of your booking request.</p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Notification Container -->
    <div id="notification-container"></div>

    <?php include_once('includes/footer.php'); ?>

    <!-- Script untuk notifikasi dan validasi -->
    <script>
        function showSuccessNotification(message) {
            createNotification(message, 'success');
        }

        function showErrorNotification(message) {
            createNotification(message, 'error');
        }

        function createNotification(message, type) {
            // Hapus notifikasi yang sudah ada
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Buat elemen notifikasi baru
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            
            // Ikon berdasarkan tipe notifikasi
            const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
            
            // Isi HTML notifikasi
            notification.innerHTML = `
                <span class="notification-icon"><i class="fas fa-${icon}"></i></span>
                <span>${message}</span>
                <span class="notification-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></span>
            `;
            
            // Tambahkan ke container
            document.body.appendChild(notification);
            
            // Tampilkan notifikasi dengan animasi
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Hapus notifikasi setelah 5 detik
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 5000);
        }
    
        // Fungsi untuk memvalidasi input angka
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
    </script>
</body>

</html>