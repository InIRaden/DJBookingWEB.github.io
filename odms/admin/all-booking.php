<!doctype html>
<html lang="en" class="no-focus">

<head>
    <title>Online DJ Management System - Total Booking</title>
    <link rel="stylesheet" href="assets/js/plugins/datatables/dataTables.bootstrap4.min.css">
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
            background: #1e3c72;
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

        .btn-info {
            background: #ffffff;
            border: 2px solid #007BFF;
            color: #007BFF;
            border-radius: 8px;
            padding: 5px 15px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-info:hover {
            background: #007BFF;
            color: #ffffff;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
        <?php include_once('includes/sidebar.php'); ?>
        <?php include_once('includes/header.php'); ?>
        <main id="main-container">
            <div class="content">
                <h2 class="content-heading">Total Booking</h2>
                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Total Booking</h3>
                    </div>
                    <div class="block-content block-content-full">
                        <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                            <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th>Booking ID</th>
                                    <th class="d-none d-sm-table-cell">Customer Name</th>
                                    <th class="d-none d-sm-table-cell">Mobile Number</th>
                                    <th class="d-none d-sm-table-cell">Email</th>
                                    <th class="d-none d-sm-table-cell">Booking Date</th>
                                    <th class="d-none d-sm-table-cell">Status</th>
                                    <th class="d-none d-sm-table-cell" style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM tblbooking";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $cnt = 1;
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $row) {
                                ?>
                                        <tr>
                                            <td class="text-center"><?php echo htmlentities($cnt); ?></td>
                                            <td class="font-w600"><?php echo htmlentities($row->BookingID); ?></td>
                                            <td class="font-w600"><?php echo htmlentities($row->Name); ?></td>
                                            <td class="font-w600"><?php echo htmlentities($row->MobileNumber); ?></td>
                                            <td class="font-w600"><?php echo htmlentities($row->Email); ?></td>
                                            <td class="font-w600">
                                                <span class="badge badge-primary"><?php echo htmlentities($row->BookingDate); ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $bstatus = $row->Status;
                                                if ($bstatus == '') { ?>
                                                    <span class="badge badge-warning">Not Processed Yet</span>
                                                <?php } elseif ($bstatus == 'Approved') { ?>
                                                    <span class="badge badge-success"><?php echo htmlentities($bstatus); ?></span>
                                                <?php } elseif ($bstatus == 'Rejected') { ?>
                                                    <span class="badge badge-danger"><?php echo htmlentities($bstatus); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="d-none d-sm-table-cell">
                                                <a href="view-booking-detail.php?editid=<?php echo htmlentities($row->ID); ?>" class="btn btn-info btn-sm" target="_blank">View</a>
                                            </td>
                                        </tr>
                                <?php
                                        $cnt++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
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
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/pages/be_tables_datatables.js"></script>
</body>

</html>