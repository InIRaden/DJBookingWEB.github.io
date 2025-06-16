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
    <title>DjBooking - Payment History</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Inter"x, sans-serif;
        }

        main {
            flex: 1;
        }

        .payment-history-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
            background: transparent;
        }

        .payment-history-table th {
            background: #30343b;
            color: #fff;
            font-weight: 500;
            padding: 12px 15px;
            text-align: left;
            font-size: 0.875rem;
            border: 1.5px solid #393939;
        }

        .payment-history-table td {
            padding: 12px 15px;
            border: 1.5px solid #393939;
            color: #e0e0e0;
            font-size: 0.875rem;
            background: #30343b;
        }

        .payment-history-table tr:hover td {
            background: #363a42;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            display: inline-block;
        }

        .status-success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .status-pending {
            background: rgba(234, 179, 8, 0.1);
            color: #eab308;
        }

        .status-failed {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            margin: 20px auto;
            max-width: 400px;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: inline-block;
            color: rgba(239, 68, 68, 0.7);
        }

        .empty-state h3 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .empty-state p {
            color: #9ca3af;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .empty-state a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 20px;
            font-size: 0.875rem;
            background: rgba(185, 28, 28, 0.5);
            color: rgba(255, 255, 255, 0.9);
            border-radius: 24px;
            transition: all 0.3s ease;
            border: 1px solid rgba(185, 28, 28, 0.3);
        }

        .empty-state a:hover {
            background: rgba(185, 28, 28, 0.6);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(185, 28, 28, 0.2);
        }

        .empty-state a i {
            font-size: 0.875rem;
            margin-right: 8px;
            color: rgba(255, 255, 255, 0.9);
        }

        .header-container {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
        }

        .header-image {
            width: 100%;
            height: 300px;
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
            padding: 0;
            line-height: 1.2;
        }

        .header-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .breadcrumb a {
            color: #888;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb a:hover {
            color: #fff;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-black text-white">
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <div class="header-container" style="position:relative;width:100%;height:300px;overflow:hidden;">
            <img src="images/abt.jpg" alt="DJ performing at event" class="w-full h-[300px] object-cover header-image" />
            <div class="header-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(to top,rgba(0,0,0,0.95) 0%,rgba(0,0,0,0.7) 60%,rgba(0,0,0,0.1) 100%);"></div>
            <div class="header-content" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;width:100%;max-width:500px;padding:0 20px;z-index:2;">
                <h1 class="header-title">Payment History</h1>
                <p class="header-text">Track all your booking transactions in one place</p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-xs mb-8">
            <a href="index.php" class="text-gray-400 hover:text-white">Home</a>
            <span class="text-gray-600">/</span>
            <span class="text-white">Payment History</span>
        </div>
        
        <section class="mb-12">
            <?php if(isset($_SESSION['odmsaid'])) { 
                $userId = $_SESSION['odmsaid'];
                
                // Fetch payment history for the logged-in user
                $sql = "SELECT ph.*, b.ServiceID 
                        FROM tblpayment_history ph 
                        JOIN tblbooking b ON ph.BookingID = b.BookingID 
                        WHERE ph.UserID = :userid 
                        ORDER BY ph.PaymentDate DESC";
                $query = $dbh->prepare($sql);
                $query->bindParam(':userid', $userId, PDO::PARAM_INT);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                
                if($query->rowCount() > 0) { ?>
                    <div class="p-6 rounded-md" style="background:#212121;">
                        <h3 class="text-white text-sm font-semibold mb-4">Payment Records</h3>
                        <div class="overflow-x-auto">
                            <table class="payment-history-table">
                                <thead>
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($results as $row) { ?>
                                        <tr>
                                            <td><?php echo htmlentities($row->BookingID); ?></td>
                                            <td>Rp <?php echo number_format($row->Amount, 0, ',', '.'); ?></td>
                                            <td><?php echo htmlentities($row->PaymentMethod); ?></td>
                                            <td><?php echo date('d M Y H:i', strtotime($row->PaymentDate)); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($row->PaymentStatus); ?>">
                                                    <?php echo htmlentities($row->PaymentStatus); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <h3>Your Payment History is Empty</h3>
                        <p>Looks like you haven't made any bookings yet.<br>Start exploring our amazing DJ services!</p>
                        <a href="services.php">
                            <i class="fas fa-music"></i>Browse Services
                        </a>
                    </div>
                <?php }
            } else { ?>
                <div class="empty-state">
                    <i class="fas fa-lock"></i>
                    <h3>Access Required</h3>
                    <p>Please sign in to view your payment history</p>
                    <div class="mt-4 flex justify-center gap-3">
                        <a href="signin.php">
                            <i class="fas fa-sign-in-alt"></i>Sign In
                        </a>
                        <a href="signup.php" class="bg-opacity-50 bg-gray-800 hover:bg-opacity-60">
                            <i class="fas fa-user-plus"></i>Sign Up
                        </a>
                    </div>
                </div>
            <?php } ?>
        </section>
    </main>

    <?php include_once('includes/footer.php'); ?>
</body>

</html>
