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

    echo '<script>alert("Remark has been updated")</script>';
    echo "<script>window.location.href ='new-booking.php'</script>";
  }
?>

<!doctype html>
<html lang="en" class="no-focus">
<head>
    <title>Online DJ Management System - View Booking</title>
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
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

        .badge-primary, .badge-success {
            background: #007BFF;
            color: #ffffff;
        }

        .badge-warning {
            background: #FFC107;
            color: #333;
        }

        .badge-danger {
            background: #FF4D4D;
            color: #ffffff;
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
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 5px;
            display: block;
        }

        .payment-info span, .payment-info p {
            color: #333;
            font-size: 0.95rem;
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
    <script src="assets/js/core/jquery.slimscroll.min.js"></script>
    <script src="assets/js/core/jquery.scrollLock.min.js"></script>
    <script src="assets/js/core/jquery.appear.min.js"></script>
    <script src="assets/js/core/jquery.countTo.min.js"></script>
    <script src="assets/js/core/js.cookie.min.js"></script>
    <script src="assets/js/codebase.js"></script>
</body>
</html>
<?php } ?>