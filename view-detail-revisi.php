<?php
// Memulai sesi untuk melacak status login pengguna
session_start();

// Menyembunyikan semua pesan error untuk produksi
error_reporting(0);

// Menyertakan file konfigurasi koneksi database
include('includes/dbconnection.php');

// Memeriksa apakah pengguna sudah login, jika tidak, arahkan ke halaman logout
if (strlen($_SESSION['odmsaid'] == 0)) {
    header('location:logout.php');
} else {
    // Menangani pembaruan status pemesanan
    if (isset($_POST['submit'])) {
        // Mengambil parameter dari URL dan form
        $eid = $_GET['editid'];
        $bookingid = $_GET['bookingid'];
        $status = $_POST['status'];
        $remark = $_POST['remark'];

        // Query untuk memperbarui status dan catatan pemesanan di tabel tblbooking
        $sql = "update tblbooking set Status=:status,Remark=:remark where ID=:eid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':remark', $remark, PDO::PARAM_STR);
        $query->bindParam(':eid', $eid, PDO::PARAM_STR);

        // Menjalankan query
        $query->execute();

        // Menampilkan notifikasi dan mengarahkan ke halaman new-booking.php
        echo '<script>alert("Remark has been updated")</script>';
        echo "<script>window.location.href ='new-booking.php'</script>";
    }

    // Menangani pembaruan status pembayaran
    if (isset($_POST['update_payment'])) {
        // Mengambil parameter dari URL dan form
        $eid = $_GET['editid'];
        $paymentStatus = $_POST['payment_status'];
        $paymentRemark = $_POST['payment_remark'];

        // Mengambil BookingID dari tabel tblbooking berdasarkan ID
        $getBookingSQL = "SELECT BookingID FROM tblbooking WHERE ID=:eid";
        $getBookingQuery = $dbh->prepare($getBookingSQL);
        $getBookingQuery->bindParam(':eid', $eid, PDO::PARAM_STR);
        $getBookingQuery->execute();
        $bookingData = $getBookingQuery->fetch(PDO::FETCH_OBJ);

        // Jika data pemesanan ditemukan
        if ($bookingData) {
            // Membuat query dasar untuk memperbarui status dan catatan pembayaran
            $updatePaymentSQL = "UPDATE tblpayment SET PaymentStatus=:status, Remark=:remark";

            // Jika status pembayaran adalah 'Paid', tambahkan tanggal selesai
            if ($paymentStatus == 'Paid') {
                $updatePaymentSQL .= ", CompletedDate=NOW()";
            }

            // Menambahkan kondisi WHERE untuk BookingID
            $updatePaymentSQL .= " WHERE BookingID=:bookingid";

            // Menyiapkan dan menjalankan query pembaruan pembayaran
            $updatePaymentQuery = $dbh->prepare($updatePaymentSQL);
            $updatePaymentQuery->bindParam(':status', $paymentStatus, PDO::PARAM_STR);
            $updatePaymentQuery->bindParam(':remark', $paymentRemark, PDO::PARAM_STR);
            $updatePaymentQuery->bindParam(':bookingid', $bookingData->BookingID, PDO::PARAM_STR);

            $updatePaymentQuery->execute();

            // Menampilkan notifikasi dan mengarahkan kembali ke halaman detail pemesanan
            echo '<script>alert("Payment status has been updated")</script>';
            echo "<script>window.location.href ='view-details-booking.php?editid=" . $eid . "'</script>";
        }
    }
?>

    <!doctype html>
    <html lang="en" class="no-focus">

    <head>
        <!-- Menentukan judul halaman -->
        <title>Online DJ Management System - View Booking</title>
        <!-- Menyertakan stylesheet utama -->
        <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
        <style>
            /* Styling untuk bagian informasi pembayaran */
            .payment-info-section {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 20px;
            }

            /* Styling untuk status pembayaran */
            .payment-status-pending {
                color: #856404;
                background-color: #fff3cd;
            }

            .payment-status-paid {
                color: #155724;
                background-color: #d4edda;
            }

            .payment-status-failed {
                color: #721c24;
                background-color: #f8d7da;
            }

            /* Styling untuk badge kustom */
            .badge-custom {
                padding: 8px 12px;
                border-radius: 20px;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 11px;
            }

            /* Styling untuk tabel detail pembayaran */
            .table-payment-details th {
                background-color: #e9ecef;
                font-weight: 600;
                width: 25%;
            }
        </style>
    </head>

    <body>
        <!-- Container utama halaman -->
        <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
            <!-- Menyertakan sidebar dan header -->
            <?php include_once('includes/sidebar.php'); ?>
            <?php include_once('includes/header.php'); ?>

            <!-- Konten utama -->
            <main id="main-container">
                <div class="content">
                    <!-- Judul halaman -->
                    <h2 class="content-heading">View Booking Details</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Blok untuk menampilkan informasi pemesanan -->
                            <div class="block block-themed">
                                <div class="block-header bg-gd-emerald">
                                    <h3 class="block-title">Complete Booking Information</h3>
                                    <div class="block-options">
                                        <!-- Tombol untuk refresh dan toggle konten -->
                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                            <i class="si si-refresh"></i>
                                        </button>
                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <?php
                                    // Mengambil ID pemesanan dari URL
                                    $eid = $_GET['editid'];

                                    // Query untuk mengambil semua informasi pemesanan, layanan, dan pembayaran
                                    $sql = "SELECT 
                                    tblbooking.BookingID,
                                    tblbooking.Name,
                                    tblbooking.MobileNumber,
                                    tblbooking.Email,
                                    tblbooking.EventDate,
                                    tblbooking.EventStartingtime,
                                    tblbooking.EventEndingtime,
                                    tblbooking.VenueAddress,
                                    tblbooking.EventType,
                                    tblbooking.AdditionalInformation,
                                    tblbooking.BookingDate,
                                    tblbooking.Remark,
                                    tblbooking.Status,
                                    tblbooking.UpdationDate,
                                    tblservice.ServiceName,
                                    tblservice.SerDes,
                                    tblservice.ServicePrice,
                                    tblpayment.PaymentMethod,
                                    tblpayment.PaymentStatus,
                                    tblpayment.PaymentDate,
                                    tblpayment.CompletedDate,
                                    tblpayment.TransferBank,
                                    tblpayment.VirtualAccountNumber,
                                    tblpayment.Amount,
                                    tblpayment.InstallmentCount,
                                    tblpayment.Remark as PaymentRemark
                                FROM tblbooking 
                                JOIN tblservice ON tblbooking.ServiceID=tblservice.ID 
                                LEFT JOIN tblpayment ON tblbooking.BookingID=tblpayment.BookingID 
                                WHERE tblbooking.ID=:eid";

                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                                    $cnt = 1;
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $row) { ?>
                                            <!-- Bagian Informasi Pemesanan -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4 class="text-primary mb-3"><i class="fa fa-calendar"></i> Booking Information</h4>
                                                    <table class="table table-bordered table-striped table-vcenter">
                                                        <tr>
                                                            <th style="width: 20%;">Booking Number</th>
                                                            <td style="width: 30%;"><?php echo htmlentities($row->BookingID); ?></td>
                                                            <th style="width: 20%;">Client Name</th>
                                                            <td style="width: 30%;"><?php echo htmlentities($row->Name); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Mobile Number</th>
                                                            <td><?php echo htmlentities($row->MobileNumber); ?></td>
                                                            <th>Email</th>
                                                            <td><?php echo htmlentities($row->Email); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Event Date</th>
                                                            <td><?php echo date('d-m-Y', strtotime($row->EventDate)); ?></td>
                                                            <th>Event Starting Time</th>
                                                            <td><?php echo htmlentities($row->EventStartingtime); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Event Ending Time</th>
                                                            <td><?php echo htmlentities($row->EventEndingtime); ?></td>
                                                            <th>Venue Address</th>
                                                            <td><?php echo htmlentities($row->VenueAddress); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Event Type</th>
                                                            <td><?php echo htmlentities($row->EventType); ?></td>
                                                            <th>Additional Information</th>
                                                            <td><?php echo htmlentities($row->AdditionalInformation); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Booking Date</th>
                                                            <td><?php echo date('d-m-Y H:i:s', strtotime($row->BookingDate)); ?></td>
                                                            <th>Booking Status</th>
                                                            <td>
                                                                <?php $bstatus = $row->Status;
                                                                if ($bstatus == ''): ?>
                                                                    <span class="badge badge-warning badge-custom">Not Processed Yet</span>
                                                                <?php elseif ($bstatus == 'Approved'): ?>
                                                                    <span class="badge badge-success badge-custom"><?php echo htmlentities($bstatus); ?></span>
                                                                <?php elseif ($bstatus == 'Cancelled'): ?>
                                                                    <span class="badge badge-danger badge-custom"><?php echo htmlentities($bstatus); ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Admin Remark</th>
                                                            <td colspan="3">
                                                                <?php if ($row->Remark == "") { ?>
                                                                    <span class="text-muted">Not Updated Yet</span>
                                                                <?php } else { ?>
                                                                    <?php echo htmlentities($row->Remark); ?>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Bagian Informasi Layanan -->
                                            <div class="row mt-4">
                                                <div class="col-md-12">
                                                    <h4 class="text-primary mb-3"><i class="fa fa-music"></i> Service Information</h4>
                                                    <table class="table table-bordered table-striped">
                                                        <tr>
                                                            <th style="width: 20%;">Service Name</th>
                                                            <td style="width: 30%;"><?php echo htmlentities($row->ServiceName); ?></td>
                                                            <th style="width: 20%;">Service Price</th>
                                                            <td style="width: 30%;"><strong>$<?php echo number_format($row->ServicePrice, 2); ?></strong></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Service Description</th>
                                                            <td colspan="3"><?php echo htmlentities($row->SerDes); ?></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Bagian Informasi Pembayaran -->
                                            <div class="row mt-4">
                                                <div class="col-md-12">
                                                    <h4 class="text-primary mb-3"><i class="fa fa-credit-card"></i> Payment Information</h4>
                                                    <div class="payment-info-section">
                                                        <?php if ($row->PaymentMethod): ?>
                                                            <table class="table table-bordered table-payment-details mb-0">
                                                                <tr>
                                                                    <th>Payment Method</th>
                                                                    <td>
                                                                        <span class="badge badge-info badge-custom">
                                                                            <?php echo ucfirst(htmlentities($row->PaymentMethod)); ?>
                                                                        </span>
                                                                    </td>
                                                                    <th>Payment Amount</th>
                                                                    <td><strong>$<?php echo number_format($row->Amount, 2); ?></strong></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Payment Status</th>
                                                                    <td>
                                                                        <?php
                                                                        $statusClass = '';
                                                                        $statusText = '';
                                                                        switch ($row->PaymentStatus) {
                                                                            case 'Paid':
                                                                                $statusClass = 'badge-success';
                                                                                $statusText = 'PAID';
                                                                                break;
                                                                            case 'Pending':
                                                                                $statusClass = 'badge-warning';
                                                                                $statusText = 'PENDING';
                                                                                break;
                                                                            case 'Failed':
                                                                                $statusClass = 'badge-danger';
                                                                                $statusText = 'FAILED';
                                                                                break;
                                                                            default:
                                                                                $statusClass = 'badge-secondary';
                                                                                $statusText = 'UNKNOWN';
                                                                        }
                                                                        ?>
                                                                        <span class="badge <?php echo $statusClass; ?> badge-custom"><?php echo $statusText; ?></span>
                                                                    </td>
                                                                    <th>Payment Date</th>
                                                                    <td><?php echo $row->PaymentDate ? date('d-m-Y H:i:s', strtotime($row->PaymentDate)) : '<span class="text-muted">Not set</span>'; ?></td>
                                                                </tr>

                                                                <?php if ($row->PaymentMethod == 'transfer' || $row->PaymentMethod == 'installment'): ?>
                                                                    <tr>
                                                                        <th>Transfer Bank</th>
                                                                        <td><?php echo htmlentities($row->TransferBank); ?></td>
                                                                        <th>Virtual Account</th>
                                                                        <td>
                                                                            <code><?php echo htmlentities($row->VirtualAccountNumber); ?></code>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>

                                                                <?php if ($row->PaymentMethod == 'installment' && $row->InstallmentCount): ?>
                                                                    <tr>
                                                                        <th>Installment Plan</th>
                                                                        <td><?php echo htmlentities($row->InstallmentCount); ?> payments</td>
                                                                        <th>Per Installment</th>
                                                                        <td><strong>$<?php echo number_format($row->Amount / $row->InstallmentCount, 2); ?></strong></td>
                                                                    </tr>
                                                                <?php endif; ?>

                                                                <?php if ($row->CompletedDate): ?>
                                                                    <tr>
                                                                        <th>Completed Date</th>
                                                                        <td><?php echo date('d-m-Y H:i:s', strtotime($row->CompletedDate)); ?></td>
                                                                        <th>Processing Time</th>
                                                                        <td>
                                                                            <?php
                                                                            $paymentDate = new DateTime($row->PaymentDate);
                                                                            $completedDate = new DateTime($row->CompletedDate);
                                                                            $interval = $paymentDate->diff($completedDate);
                                                                            echo $interval->format('%h hours %i minutes');
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>

                                                                <?php if ($row->PaymentRemark): ?>
                                                                    <tr>
                                                                        <th>Payment Remark</th>
                                                                        <td colspan="3"><?php echo htmlentities($row->PaymentRemark); ?></td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                            </table>
                                                        <?php else: ?>
                                                            <div class="alert alert-warning">
                                                                <i class="fa fa-exclamation-triangle"></i>
                                                                <strong>No Payment Information Available</strong>
                                                                <p class="mb-0">This booking doesn't have associated payment information.</p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Bagian Tombol Aksi -->
                                            <div class="row mt-4">
                                                <div class="col-md-12">
                                                    <div class="text-center">
                                                        <?php if ($bstatus == ""): ?>
                                                            <button class="btn btn-success btn-lg mr-2" data-toggle="modal" data-target="#bookingModal">
                                                                <i class="fa fa-check"></i> Update Booking Status
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if ($row->PaymentMethod && $row->PaymentStatus != 'Paid'): ?>
                                                            <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#paymentModal">
                                                                <i class="fa fa-credit-card"></i> Update Payment Status
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                    <?php $cnt = $cnt + 1;
                                        }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Menyertakan footer -->
            <?php include_once('includes/footer.php'); ?>
        </div>

        <!-- Modal untuk Memperbarui Status Pemesanan -->
        <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="bookingModalLabel">
                            <i class="fa fa-edit"></i> Update Booking Status
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="post" name="submit">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="status" class="font-weight-bold">Booking Status:</label>
                                <select name="status" id="status" class="form-control form-control-lg" required="true">
                                    <option value="">Select Status</option>
                                    <option value="Approved">✅ Approved</option>
                                    <option value="Cancelled">❌ Cancelled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="remark" class="font-weight-bold">Admin Remark:</label>
                                <textarea name="remark" id="remark" placeholder="Enter your remark here..." rows="4" class="form-control" required="true"></textarea>
                                <small class="text-muted">This remark will be visible to the customer.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal untuk Memperbarui Status Pembayaran -->
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="paymentModalLabel">
                            <i class="fa fa-credit-card"></i> Update Payment Status
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="post" name="update_payment">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Current Status:</strong>
                                <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText ?? 'Unknown'; ?></span>
                            </div>
                            <div class="form-group">
                                <label for="payment_status" class="font-weight-bold">New Payment Status:</label>
                                <select name="payment_status" id="payment_status" class="form-control form-control-lg" required="true">
                                    <option value="">Select Payment Status</option>
                                    <option value="Pending">⏳ Pending</option>
                                    <option value="Paid">✅ Paid</option>
                                    <option value="Failed">❌ Failed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="payment_remark" class="font-weight-bold">Payment Remark:</label>
                                <textarea name="payment_remark" id="payment_remark" placeholder="Enter payment remark or notes..." rows="3" class="form-control"></textarea>
                                <small class="text-muted">Optional: Add any notes about this payment status change.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button type="submit" name="update_payment" class="btn btn-success btn-lg">
                                <i class="fa fa-save"></i> Update Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Menyertakan file JavaScript untuk fungsionalitas tambahan -->
        <script src="assets/js/core/jquery.min.js"></script>
        <script src="assets/js/core/popper.min.js"></script>
        <script src="assets/js/core/bootstrap.min.js"></script>
        <script src="assets/js/core/jquery.slimscroll.min.js"></script>
        <script src="assets/js/core/jquery.scrollLock.min.js"></script>
        <script src="assets/js/core/jquery.appear.min.js"></script>
        <script src="assets/js/core/jquery.countTo.min.js"></script>
        <script src="assets/js/core/js.cookie.min.js"></script>
        <script src="assets/js/codebase.js"></script>

        <script>
            // Menyembunyikan notifikasi setelah 5 detik
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Validasi form untuk status pemesanan
            document.getElementById('status').addEventListener('change', function() {
                const remarkField = document.getElementById('remark');
                if (this.value === 'Cancelled') {
                    remarkField.placeholder = 'Please provide reason for cancellation...';
                    remarkField.setAttribute('required', 'true');
                } else if (this.value === 'Approved') {
                    remarkField.placeholder = 'Booking approved. Add any additional notes...';
                }
            });
        </script>
    </body>

    </html>
<?php } ?>