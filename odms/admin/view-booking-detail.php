<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['odmsaid'] == 0)) {
  header('location:logout.php');
} else {
  if (isset($_POST['submit'])) {
    $eid = $_GET['editid'];
    $bookingid = $_GET['bookingid'];
    $status = $_POST['status'];
    $remark = $_POST['remark'];

    $sql = "update tblbooking set Status=:status,Remark=:remark where ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':remark', $remark, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);

    $query->execute();

    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            let toastClass = 'modern-toast-success';
            let icon = 'fas fa-check';
            let title = 'Success!';
            let message = 'Remark has been successfully updated';

            if ('" . $status . "' === 'Cancelled') {
                toastClass = 'modern-toast-danger';
                icon = 'fas fa-times';
                title = 'Booking Cancelled';
                message = 'The booking has been cancelled';
            }

            Toastify({
                node: createCustomToast(title, message, icon),
                duration: 3000,
                gravity: 'top',
                position: 'right',
                className: toastClass,
                stopOnFocus: true
            }).showToast();
            
            setTimeout(function() {
                window.location.href = 'new-booking.php';
            }, 2000);
        });
    </script>";
  }
?>

<!doctype html>
<html lang="en" class="no-focus">
<head>
    <title>Online DJ Management System - View Booking</title>
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        body {
            background: #ffffff;
            color: #333;
        }

        #page-container {
            background: transparent;
        }

        .content {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .content-heading {
            color: #1e3c72;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .block {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .block-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-bottom: none;
            padding: 15px 20px;
        }

        .block-title {
            color: #ffffff;
            font-weight: bold;
        }

        .table {
            background: #ffffff;
            border-radius: 8px;
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8f9fa;
            color: #1e3c72;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 0.9rem;
        }


        .badge-primary {
                background: #007BFF;
                color: #ffffff;
                font-weight: bold;
            }

        .badge-success {
                background: #E0FFE0;
                color: #28A745;
            }

        .badge-warning {
            background: #FFF9DB;
            color: #FFD700;
        }

        .badge-danger {
            background: #FFE5E5;
            color: #FF4D4D;
        }

        .badge-secondary {
            background: #6C757D;
            color: #ffffff;
        }

        .btn-primary {
            background: #007BFF;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6C757D;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .payment-info {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .payment-info div {
            flex: 1;
            min-width: 200px;
        }

        .payment-info label {
            font-weight: 720;
            color: #1e3c72;
            margin-bottom: 5px;
            display: block;
        }

        .payment-info span, .payment-info p {
            color: #333;
            font-size: 0.95rem;
        }

        /* Toast Styles */
        .modern-toast-success {
            background: linear-gradient(to right, #28a745, #20c997) !important;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2) !important;
        }
        
        .modern-toast-danger {
            background: linear-gradient(to right, #dc3545, #ff4444) !important;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2) !important;
        }

        .toast-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toast-content {
            display: flex;
            flex-direction: column;
        }

        .toast-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 14px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
        <?php include_once('includes/sidebar.php'); ?>
        <?php include_once('includes/header.php'); ?>
        <main id="main-container">
            <div class="content">
                <h2 class="content-heading">View Booking</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="block block-themed">
                            <div class="block-header">
                                <h3 class="block-title">View Booking</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo" aria-label="Refresh content">
                                        <i class="si si-refresh"></i>
                                    </button>
                                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle" aria-label="Toggle content"></button>
                                </div>
                            </div>
                            <div class="block-content">
                                <?php
                                $eid = $_GET['editid'];
                                $sql = "SELECT tblbooking.BookingID,tblbooking.Name,tblbooking.MobileNumber,tblbooking.Email,tblbooking.EventDate,tblbooking.EventStartingtime,tblbooking.EventEndingtime,tblbooking.VenueAddress,tblbooking.EventType,tblbooking.AdditionalInformation,tblbooking.BookingDate,tblbooking.Remark,tblbooking.Status,tblbooking.UpdationDate,tblservice.ServiceName,tblservice.SerDes,tblservice.ServicePrice,tblpayment.PaymentMethod,tblpayment.PaymentStatus,tblpayment.PaymentDate,tblpayment.CompletedDate,tblpayment.TransferBank,tblpayment.Amount,tblpayment.InstallmentCount from tblbooking join tblservice on tblbooking.ServiceID=tblservice.ID left join tblpayment on tblbooking.BookingID=tblpayment.BookingID where tblbooking.ID=:eid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                $cnt = 1;
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $row) { ?>
                                        <table border="1" class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                            <tr>
                                                <th>Booking Number</th>
                                                <td><?php echo htmlentities($row->BookingID); ?></td>
                                                <th>Client Name</th>
                                                <td><?php echo htmlentities($row->Name); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Mobile Number</th>
                                                <td><?php echo htmlentities($row->MobileNumber); ?></td>
                                                <th>Email</th>
                                                <td><?php echo htmlentities($row->Email); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Event Date</th>
                                                <td><?php echo htmlentities($row->EventDate); ?></td>
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
                                                <th>Service Name</th>
                                                <td><?php echo htmlentities($row->ServiceName); ?></td>
                                                <th>Service Description</th>
                                                <td><?php echo htmlentities($row->SerDes); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Service Price</th>
                                                <td>$<?php echo htmlentities($row->ServicePrice); ?></td>
                                                <th>Apply Date</th>
                                                <td><?php echo htmlentities($row->BookingDate); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Order Final Status</th>
                                                <td>
                                                    <?php
                                                    $bstatus = $row->Status;
                                                    if ($bstatus == ''): ?>
                                                        <span class="badge badge-warning">Not Processed Yet</span>
                                                    <?php elseif ($bstatus == 'Approved'): ?>
                                                        <span class="badge badge-success"><?php echo htmlentities($bstatus); ?></span>
                                                    <?php elseif ($bstatus == 'Cancelled'): ?>
                                                        <span class="badge badge-danger"><?php echo htmlentities($bstatus); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <th>Admin Remark</th>
                                                <?php if ($row->Status == "") { ?>
                                                    <td><?php echo "Not Updated Yet"; ?></td>
                                                <?php } else { ?>
                                                    <td><?php echo htmlentities($row->Remark); ?></td>
                                                <?php } ?>
                                            </tr>
                                            <tr>
                                                <th>Payment Information</th>
                                                <td colspan="3">
                                                    <div class="payment-info">
                                                        <div>
                                                            <label>Payment Method</label>
                                                            <p><?php echo htmlentities($row->PaymentMethod) ?: 'N/A'; ?></p>
                                                        </div>
                                                        <div>
                                                            <label>Amount</label>
                                                            <p>$<?php echo htmlentities(number_format($row->Amount, 2)) ?: 'N/A'; ?></p>
                                                        </div>
                                                        <div>
                                                            <label>Payment Status</label>
                                                            <span class="badge <?php
                                                            $statusClass = '';
                                                            switch ($row->PaymentStatus) {
                                                                case 'Paid':
                                                                    $statusClass = 'badge-success';
                                                                    break;
                                                                case 'Pending':
                                                                    $statusClass = 'badge-warning';
                                                                    break;
                                                                case 'Failed':
                                                                    $statusClass = 'badge-danger';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'badge-secondary';
                                                                    break;
                                                            }
                                                            echo $statusClass;
                                                            ?>"><?php echo htmlentities($row->PaymentStatus) ?: 'N/A'; ?></span>
                                                        </div>
                                                        <div>
                                                            <label>Bank</label>
                                                            <p><?php echo htmlentities($row->TransferBank) ?: 'N/A'; ?></p>
                                                        </div>
                                                        <div>
                                                            <label>Payment Date</label>
                                                            <p><?php echo $row->PaymentDate ? date('d-m-Y H:i:s', strtotime($row->PaymentDate)) : 'N/A'; ?></p>
                                                        </div>
                                                        <div>
                                                            <label>Completed Date</label>
                                                            <p><?php
                                                            if (strtolower($row->PaymentMethod) == 'installment') {
                                                                echo '-';
                                                            } else {
                                                                echo $row->CompletedDate ? date('d-m-Y H:i:s', strtotime($row->CompletedDate)) : 'N/A';
                                                            }
                                                            ?></p>
                                                        </div>
                                                        <?php if ($row->PaymentMethod == 'Installment'): ?>
                                                            <div>
                                                                <label>Installment Count</label>
                                                                <p><?php echo htmlentities($row->InstallmentCount) ?: 'N/A'; ?> times</p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    <?php
                                    $cnt = $cnt + 1;
                                    }
                                } ?>
                                <?php if ($bstatus == "") { ?>
                                    <p align="center" style="padding-top: 20px">
                                        <button class="btn btn-primary waves-effect waves-light w-lg" data-toggle="modal" data-target="#myModal" aria-label="Take action on booking">Take Action</button>
                                    </p>
                                <?php } ?>
                                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Take Action</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="post" name="submit">
                                                <div class="modal-body">
                                                    <table class="table table-bordered table-hover data-tables">
                                                        <tr>
                                                            <th>Remark :</th>
                                                            <td>
                                                                <textarea name="remark" placeholder="Remark" rows="6" cols="14" class="form-control wd-450" required="true" aria-label="Enter remark"></textarea>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status :</th>
                                                            <td>
                                                                <select name="status" class="form-control wd-450" required="true" aria-label="Select status">
                                                                    <option value="">Select</option>
                                                                    <option value="Approved">Approved</option>
                                                                    <option value="Cancelled">Cancelled</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include_once('includes/footer.php'); ?>
    </div>
    <script src="assets/js/core/jquery.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        function createCustomToast(title, message, icon) {
            const container = document.createElement('div');
            container.style.display = 'flex';
            container.style.alignItems = 'center';
            container.style.gap = '12px';
            
            const iconDiv = document.createElement('div');
            iconDiv.className = 'toast-icon';
            iconDiv.innerHTML = `<i class="${icon}" style="color: white"></i>`;
            
            const content = document.createElement('div');
            content.className = 'toast-content';
            
            const titleDiv = document.createElement('div');
            titleDiv.className = 'toast-title';
            titleDiv.textContent = title;
            
            const messageDiv = document.createElement('div');
            messageDiv.className = 'toast-message';
            messageDiv.textContent = message;
            
            content.appendChild(titleDiv);
            content.appendChild(messageDiv);
            
            container.appendChild(iconDiv);
            container.appendChild(content);
            
            return container;
        }
    </script>
</body>
</html>
<?php } ?>