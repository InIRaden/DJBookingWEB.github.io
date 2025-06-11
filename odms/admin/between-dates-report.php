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
        <title>Online DJ Management System - Between Dates Report</title>
        <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        <style>
            body {
                background-color: #f9fafb;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            #main-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            .content {
                padding: 40px 32px;
                width: 100%;
            }

            .content-heading {
                font-size: 2rem;
                font-weight: 600;
                color: #1e40af;
                text-align: center;
                margin-bottom: 24px;
                border-bottom: 2px solid #e0e7ff;
                padding-bottom: 0.5rem;
            }

            .report-card {
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                transition: transform 0.3s ease;
            }

            .report-card:hover {
                transform: translateY(-4px);
            }

            .card-header {
                background: linear-gradient(135deg, #3b82f6, #60a5fa);
                padding: 20px 24px;
                border-top-left-radius: 16px;
                border-top-right-radius: 16px;
            }

            .card-title {
                font-size: 1.5rem;
                font-weight: 500;
                color: #ffffff;
                margin: 0;
            }

            .card-content {
                padding: 24px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                font-size: 1rem;
                font-weight: 500;
                color: #1e3a8a;
                margin-bottom: 8px;
                display: block;
            }

            .form-control {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid #e0e7ff;
                border-radius: 8px;
                font-size: 1rem;
                color: #374151;
                background: #f9fafb;
                transition: border-color 0.3s ease, box-shadow 0.3s ease;
            }

            .form-control:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
                background: #ffffff;
            }

            .btn-alt-success {
                background: #3b82f6;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 500;
                color: #ffffff;
                transition: background 0.3s ease, transform 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-alt-success:hover {
                background: #2563eb;
                transform: translateY(-2px);
                color: #ffffff;
            }

            .btn-alt-success i {
                margin-right: 0;
            }

            @media (max-width: 1400px) {
                .content {
                    padding: 20px 16px;
                }

                .content-heading {
                    font-size: 1.8rem;
                }

                .card-title {
                    font-size: 1.3rem;
                }

                .form-control {
                    padding: 10px 14px;
                    font-size: 0.95rem;
                }

                .btn-alt-success {
                    padding: 10px 20px;
                    font-size: 0.95rem;
                }
            }

            @media (max-width: 1024px) {
                .content {
                    padding: 15px 12px;
                }

                .content-heading {
                    font-size: 1.6rem;
                }

                .card-title {
                    font-size: 1.2rem;
                }

                .form-control {
                    padding: 8px 12px;
                    font-size: 0.9rem;
                }

                .btn-alt-success {
                    padding: 8px 16px;
                    font-size: 0.9rem;
                }
            }

            @media (max-width: 768px) {
                .content {
                    padding: 10px 8px;
                }

                .content-heading {
                    font-size: 1.4rem;
                }

                .card-title {
                    font-size: 1.1rem;
                }

                .form-control {
                    padding: 6px 10px;
                    font-size: 0.85rem;
                }

                .btn-alt-success {
                    padding: 6px 12px;
                    font-size: 0.85rem;
                }
            }
        </style>
    </head>

    <body>
        <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
            <?php include_once('includes/sidebar.php'); ?>
            <?php include_once('includes/header.php'); ?>
            <main id="main-container">
                <div class="content">
                    <h2 class="content-heading">Between Dates Report</h2>
                    <div class="report-card">
                        <div class="card-header">
                            <h3 class="card-title">Between Dates Report</h3>
                        </div>
                        <div class="card-content">
                            <form method="post" name="bwdatesreport" action="booking-bwdates-reports-details.php">
                                <div class="form-group">
                                    <label for="fromdate">From Date:</label>
                                    <input type="date" class="form-control" id="fromdate" name="fromdate" value="" required='true'>
                                </div>
                                <div class="form-group">
                                    <label for="todate">To Date:</label>
                                    <input type="date" class="form-control" id="todate" name="todate" value="" required='true'>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-alt-success" name="submit">
                                        <i class="fa fa-plus"></i> Submit
                                    </button>
                                </div>
                            </form>
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