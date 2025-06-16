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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Inter", sans-serif;
        }
        
        /* Styling untuk notifikasi */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            z-index: 1000;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(120%);
            transition: transform 0.3s ease-in-out;
            max-width: 350px;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification-success {
            background-color: #218838;
            border-left: 5px solid #43e97b;
        }
        
        .notification-error {
            background-color: #d32f2f;
            border-left: 5px solid #b71c1c;
        }
        
        .notification-icon {
            margin-right: 12px;
            font-size: 18px;
        }
        
        .notification-close {
            margin-left: 12px;
            cursor: pointer;
            opacity: 0.7;
        }
        
        .notification-close:hover {
            opacity: 1;
        }

        .header-container {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
        }

        .header-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .header-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.7) 60%, rgba(0, 0, 0, 0.1) 100%);
        }

        .header-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 100%;
            max-width: 500px;
            padding: 0 20px;
            z-index: 2;
        }

        .header-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.92);
            margin-bottom: 1rem;
            text-shadow: 0 0 10px #fff, 0 0 18px #2563eb, 2px 2px 8px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }

        .header-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .bg-card {
            background: #212121 !important;
        }

        .soft-input {
            background: #30343b;
            border: 1.5px solid #393939;
            border-radius: 8px;
            padding: 11px 15px;
            color: #fff;
            font-size: 14px;
            transition: border-color 0.18s, box-shadow 0.18s;
            outline: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        .soft-input:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 2px rgba(220,38,38,0.10);
            background: #363a42;
        }

        textarea.soft-input {
            min-height: 90px;
            resize: vertical;
        }

        .modern-table {
            background: #30343b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .modern-table th, .modern-table td {
            border: 1px solid #393939;
            padding: 0.75rem;
            text-align: left;
        }

        .modern-table th {
            background-color: #30343b;
            color: #fff;
            font-weight: 600;
        }

        .modern-table tr:nth-child(even) {
            background-color: #232323;
        }

        .modern-table tr:nth-child(odd) {
            background-color: #30343b;
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <div class="header-container" style="position:relative;width:100%;height:300px;overflow:hidden;">
            <img src="images/8.jpg" alt="DJ performing at event" class="w-full h-[300px] object-cover header-image" />
            <div class="header-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(to top,rgba(0,0,0,0.95) 0%,rgba(0,0,0,0.7) 60%,rgba(0,0,0,0.1) 100%);"></div>
            <div class="header-content" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;width:100%;max-width:500px;padding:0 20px;z-index:2;">
                <h1 class="header-title" style="font-size:2.2rem;font-weight:800;color:rgba(255,255,255,0.92);margin-bottom:1rem;text-shadow:0 0 10px #fff,0 0 18px #2563eb,2px 2px 8px rgba(0,0,0,0.3);letter-spacing:1px;">Request Status</h1>
                <p class="header-text" style="color:rgba(255,255,255,0.8);font-size:1.1rem;text-shadow:1px 1px 2px rgba(0,0,0,0.3);">Learn more about our DJ services and what makes us special</p>
            </div>
        </div>  
    </header>

    <!-- Main Content -->
    <main class="px-10 md:px-13 lg:px-10 xl:px-10 py-10 max-w-[1280px] mx-auto">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-xs mb-8">
            <a href="index.php" class="text-gray-400 hover:text-white">Home</a>
            <span class="text-gray-600">/</span>
            <span class="text-white">Request Status</span>
        </div>

        <!-- Request Status Content -->
        <section class="mb-12">
            <h2 class="font-semibold text-white text-lg mb-6">Check Your Booking Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-card p-6 rounded-md">
                    <h3 class="text-white text-sm font-semibold mb-4">Enter Your Details</h3>
                    <p class="text-gray-400 text-xs mb-4">Please provide your name and mobile number to check your booking status.</p>

                    <form method="post" class="space-y-4">
                        <div>
                            <label class="block text-gray-300 text-xs mb-1">Name</label>
                            <input type="text" name="name" required="true" class="w-full soft-input">
                        </div>

                        <div>
                            <label class="block text-gray-300 text-xs mb-1">Mobile Number</label>
                            <input type="text" name="mobnum" required="true" maxlength="10" pattern="[0-9]+" class="w-full soft-input" onkeypress="return isNumberKey(event)" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        <button type="submit" name="submit" class="bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded hover:bg-red-600 transition cursor-pointer">
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
                            <script>document.addEventListener('DOMContentLoaded',function(){showSuccessNotification('Booking data found!');});</script>
                            <div class="bg-card p-6 rounded-md">
                                <h3 class="text-white text-sm font-semibold mb-4">Your Booking Results</h3>
                                <div class="overflow-x-auto">
                                    <table class="modern-table min-w-full rounded-md overflow-hidden">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-xs text-left">Booking Number</th>
                                                <th class="px-4 py-2 text-xs text-left">Client Name</th>
                                                <th class="px-4 py-2 text-xs text-left">Mobile Number</th>
                                                <th class="px-4 py-2 text-xs text-left">Email</th>
                                                <th class="px-4 py-2 text-xs text-left">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($results as $row) { ?>
                                                <tr class="hover:bg-[#232323]">
                                                    <td class="px-4 py-3 text-xs text-gray-300"><?php echo $row->BookingID; ?></td>
                                                    <td class="px-4 py-3 text-xs text-gray-300"><?php echo $row->Name; ?></td>
                                                    <td class="px-4 py-3 text-xs text-gray-300"><?php echo $row->MobileNumber; ?></td>
                                                    <td class="px-4 py-3 text-xs text-gray-300"><?php echo $row->Email; ?></td>
                                                    <td class="px-4 py-3 text-xs">
                                                        <a href="request-details.php?bid=<?php echo htmlentities($row->ID); ?>&&bookingid=<?php echo htmlentities($row->BookingID); ?>" class="bg-red-700 text-white px-3 py-1 rounded text-xs hover:bg-red-600 transition">
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
                            <script>document.addEventListener('DOMContentLoaded',function(){showErrorNotification('No booking data found for the provided details.');});</script>
                            <div class="bg-card p-6 rounded-md flex flex-col items-center justify-center h-full w-full text-center gap-2">
                                <div>
                                    <i class="fas fa-search text-gray-500 text-5xl mb-3"></i>
                                </div>
                                <h3 class="text-red-500 text-base font-semibold mb-1">No Records Found</h3>
                                <p class="text-gray-400 text-sm mt-0 text-center max-w-md mx-auto">We couldn't find any bookings matching your details. Please check your information and try again.</p>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="bg-card p-6 rounded-md h-full w-full">
                            <div class="text-center">
                                <img src="images/1hr.png" alt="DJ Booking" class="w-32 h-32 object-contain mb-4 mx-auto">
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