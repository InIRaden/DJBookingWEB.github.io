<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['odmsaid'] == 0)) {
    header('location:logout.php');
} else {
?>
    <!doctype html>
    <html lang="en" class="no-focus">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Online DJ Management System - Approved Booking</title>
        <link rel="stylesheet" href="assets/js/plugins/datatables/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        <style>
            /* Modernized styles scoped to main container */
            body {
                background-color: #f4f7fc;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            #main-container {
                max-width: 1400px;
                /* Wider container */
                margin: 0 auto;
                padding: 20px;
            }

            .content {
                padding: 40px 32px;
                /* Match reference padding */
                width: 100%;
            }

            .content-heading {
                font-size: 1.8rem;
                font-weight: 600;
                color: #1e3a8a;
                text-align: center;
                margin-bottom: 24px;
                border-bottom: 2px solid #e2e8f0;
                padding-bottom: 0.5rem;
            }

            .table-card {
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                transition: transform 0.3s ease;
            }

            .table-card:hover {
                transform: translateY(-4px);
            }

            .table-header {
                background: linear-gradient(135deg, #3b82f6, #60a5fa);
                padding: 16px 24px;
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .table-title {
                font-size: 1.25rem;
                font-weight: 500;
                color: #ffffff;
                margin: 0;
            }

            .dataTables_filter {
                margin: 0;
                padding: 8px 0;
            }

            .dataTables_filter input {
                width: 200px;
                padding: 8px 12px;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-size: 0.9rem;
                color: #374151;
                background: #f9fafb;
                transition: border-color 0.3s ease, box-shadow 0.3s ease;
            }

            .dataTables_filter input:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
                background: #ffffff;
            }

            .dataTables_filter label {
                font-size: 0.9rem;
                color: #ffffff;
                margin-right: 10px;
            }

            .dataTables_length {
                padding: 8px 0;
                margin-right: 20px;
            }

            .dataTables_length select {
                padding: 6px 12px;
                border-radius: 8px;
                border: 1px solid #d1d5db;
                font-size: 0.9rem;
                color: #374151;
                background: #f9fafb;
                transition: border-color 0.3s ease;
            }

            .dataTables_length select:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            }

            .js-dataTable-full-pagination {
                margin: 0;
                border: none;
                width: 100%;
                font-size: 0.9rem;
                color: #374151;
                table-layout: auto;
                /* Allow columns to adjust dynamically */
            }

            .js-dataTable-full-pagination thead {
                background: #f9fafb;
            }

            .js-dataTable-full-pagination thead th {
                font-weight: 500;
                color: #1e3a8a;
                padding: 12px 16px;
                border-bottom: 1px solid #e5e7eb;
                text-align: left;
                white-space: nowrap;
                /* Prevent header wrapping */
                min-width: 0;
                /* Allow shrinking */
            }

            .js-dataTable-full-pagination tbody tr {
                transition: background 0.2s ease;
            }

            .js-dataTable-full-pagination tbody tr:hover {
                background: #f1f5f9;
            }

            .js-dataTable-full-pagination tbody td {
                padding: 12px 16px;
                border-bottom: 1px solid #e5e7eb;
                vertical-align: middle;
                white-space: nowrap;
                /* Prevent wrapping */
                overflow: hidden;
                text-overflow: ellipsis;
                /* Truncate long text */
                min-width: 0;
                /* Allow shrinking */
            }

            .badge-primary {
                background: #3b82f6;
                color: #ffffff;
                padding: 6px 12px;
                border-radius: 12px;
                font-size: 0.85rem;
                font-weight: 500;
                display: inline-block;
                vertical-align: middle;
            }

            .badge-warning {
                background: #fef3c7;
                color: #d97706;
                padding: 6px 12px;
                border-radius: 12px;
                font-size: 0.85rem;
                font-weight: 500;
                display: inline-block;
                vertical-align: middle;
            }

            .badge-success {
                background: #22c55e;
                /* Vibrant green for Approved status */
                color: #ffffff;
                padding: 6px 12px;
                border-radius: 12px;
                font-size: 0.85rem;
                font-weight: 500;
                display: inline-block;
                vertical-align: middle;
            }

            .badge-danger {
                background: #fee2e2;
                color: #991b1b;
                padding: 6px 12px;
                border-radius: 12px;
                font-size: 0.85rem;
                font-weight: 500;
                display: inline-block;
                vertical-align: middle;
            }

            .btn-info {
                background: #3b82f6;
                border: none;
                padding: 8px 16px;
                border-radius: 8px;
                font-size: 0.85rem;
                font-weight: 500;
                color: #ffffff;
                transition: background 0.3s ease, transform 0.2s ease;
                margin-right: 8px;
                display: inline-block;
                vertical-align: middle;
                min-width: 70px;
                /* Minimum width to prevent shrinking too much */
                text-align: center;
            }

            .btn-info:hover {
                background: #2563eb;
                transform: translateY(-2px);
            }

            .btn-secondary {
                background: #6b7280;
                border: none;
                padding: 8px 16px;
                border-radius: 8px;
                font-size: 0.85rem;
                font-weight: 500;
                color: #ffffff;
                transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
                display: inline-block;
                vertical-align: middle;
                min-width: 70px;
                /* Minimum width to prevent shrinking too much */
                text-align: center;
            }

            .btn-secondary:hover {
                background: #4b5563;
                transform: translateY(-2px);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                /* Enhanced hover effect */
            }

            .dataTables_paginate {
                padding: 8px 0;
            }

            .paginate_button {
                padding: 6px 12px;
                margin: 0 4px;
                border-radius: 8px;
                background: #ffffff;
                border: 1px solid #d1d5db;
                color: #374151;
                transition: background 0.3s ease, color 0.3s ease;
                display: inline-block;
                vertical-align: middle;
            }

            .paginate_button:hover {
                background: #3b82f6;
                color: #ffffff;
                border-color: #3b82f6;
            }

            .paginate_button.current {
                background: #3b82f6;
                color: #ffffff;
                border-color: #3b82f6;
            }

            /* Responsive adjustments with horizontal scroll */
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
                /* Disable vertical scroll */
                -webkit-overflow-scrolling: touch;
            }

            .js-dataTable-full-pagination {
                min-width: 800px;
                /* Minimum width to ensure all columns are scrollable */
            }

            @media (max-width: 1400px) {
                .content {
                    padding: 20px 16px;
                }

                .js-dataTable-full-pagination thead th,
                .js-dataTable-full-pagination tbody td {
                    padding: 10px 12px;
                    font-size: 0.85rem;
                }

                .btn-info,
                .btn-secondary {
                    padding: 6px 12px;
                    font-size: 0.8rem;
                }

                .badge-primary,
                .badge-warning,
                .badge-success,
                .badge-danger {
                    padding: 4px 8px;
                    font-size: 0.75rem;
                }
            }

            @media (max-width: 1024px) {
                .js-dataTable-full-pagination {
                    min-width: 700px;
                    /* Adjust minimum width */
                }

                .js-dataTable-full-pagination thead th,
                .js-dataTable-full-pagination tbody td {
                    padding: 8px 10px;
                    font-size: 0.8rem;
                }

                .btn-info,
                .btn-secondary {
                    padding: 5px 10px;
                    font-size: 0.75rem;
                }

                .badge-primary,
                .badge-warning,
                .badge-success,
                .badge-danger {
                    padding: 3px 6px;
                    font-size: 0.7rem;
                }
            }

            @media (max-width: 768px) {
                .content {
                    padding: 15px 12px;
                }

                .content-heading {
                    font-size: 1.5rem;
                }

                .js-dataTable-full-pagination {
                    min-width: 600px;
                    /* Further adjust minimum width */
                }

                .js-dataTable-full-pagination thead th,
                .js-dataTable-full-pagination tbody td {
                    padding: 6px 8px;
                    font-size: 0.75rem;
                }

                .dataTables_filter input {
                    width: 120px;
                }

                .btn-info,
                .btn-secondary {
                    padding: 4px 8px;
                    font-size: 0.7rem;
                    min-width: 60px;
                }

                .badge-primary,
                .badge-warning,
                .badge-success,
                .badge-danger {
                    padding: 2px 5px;
                    font-size: 0.65rem;
                }
            }

            @media (max-width: 576px) {
                .js-dataTable-full-pagination {
                    min-width: 500px;
                    /* Minimum width for small screens */
                }

                .js-dataTable-full-pagination thead th,
                .js-dataTable-full-pagination tbody td {
                    padding: 4px 6px;
                    font-size: 0.7rem;
                }

                .dataTables_filter input {
                    width: 100px;
                }

                .btn-info,
                .btn-secondary {
                    padding: 3px 6px;
                    font-size: 0.65rem;
                    min-width: 50px;
                }

                .badge-primary,
                .badge-warning,
                .badge-success,
                .badge-danger {
                    padding: 2px 4px;
                    font-size: 0.6rem;
                }
            }
        </style>
    </head>

    <body>
        <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
            <?php include_once('includes/sidebar.php'); ?>
            <?php include_once('includes/header.php'); ?>
            <!-- Main Container -->
            <main id="main-container">
                <div class="content">
                    <h2 class="content-heading">Approved Booking</h2>
                    <div class="table-card">
                        <div class="table-header">
                            <h3 class="table-title">Approved Booking</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Booking ID</th>
                                        <th>Customer Name</th>
                                        <th>Mobile Number</th>
                                        <th>Email</th>
                                        <th>Booking Date</th>
                                        <th>Status</th>
                                        <th style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * from tblbooking where Status='Approved'";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;
                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $row) { ?>
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
                                                    if ($bstatus == ''): ?>
                                                        <span class="badge badge-warning">Not Processed Yet</span>
                                                    <?php elseif ($bstatus == 'Approved'): ?>
                                                        <span class="badge badge-success"><?php echo htmlentities($bstatus); ?></span>
                                                    <?php elseif ($bstatus == 'Cancelled'): ?>
                                                        <span class="badge badge-danger"><?php echo htmlentities($bstatus); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="view-booking-detail.php?editid=<?php echo htmlentities($row->ID); ?>" class="btn btn-info btn-sm" target="_blank">View</a>
                                                    <a href="invoice-generating.php?invid=<?php echo htmlentities($row->ID); ?>" target="_blank" class="btn btn-secondary btn-sm">Invoice</a>
                                                </td>
                                            </tr>
                                    <?php $cnt = $cnt + 1;
                                        }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <!-- END Main Container -->
            <?php include_once('includes/footer.php'); ?>
        </div>
        <!-- END Page Container -->
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
<?php } ?>