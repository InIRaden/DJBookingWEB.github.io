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
        <title>Online DJ Management System - Between Dates Report</title>
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

            .form-control {
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 10px;
                font-size: 0.9rem;
                transition: all 0.3s ease;
            }

            .form-control:focus {
                border-color: #007BFF;
                box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
                outline: none;
            }

            .form-control::placeholder {
                color: #999;
            }

            .btn-alt-success {
                background: #ffffff;
                border: 2px solid #28A745;
                color: #28A745;
                border-radius: 8px;
                padding: 8px 20px;
                font-weight: bold;
                transition: all 0.3s ease;
            }

            .btn-alt-success:hover {
                background: #28A745;
                color: #ffffff;
                transform: translateY(-1px);
            }

            .btn-block-option {
                background: transparent;
                color: #ffffff;
                border: none;
                font-size: 1rem;
                transition: all 0.3s ease;
            }

            .btn-block-option:hover {
                color: #DBF0FF;
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
                    <h2 class="content-heading">Between Dates Report</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block block-themed">
                                <div class="block-header">
                                    <h3 class="block-title">Between Dates Report</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle">
                                            <i class="si si-refresh"></i>
                                        </button>
                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle">
                                            <i class="si si-arrow-up"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <form method="post" name="bwdatesreport" action="booking-bwdates-reports-details.php">
                                        <div class="form-group row">
                                            <label class="col-12">From Date:</label>
                                            <div class="col-12">
                                                <input type="date" class="form-control" id="fromdate" name="fromdate" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12">To Date:</label>
                                            <div class="col-12">
                                                <input type="date" class="form-control" id="todate" name="todate" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-alt-success" name="submit">
                                                    <i class="fa fa-plus mr-5"></i> Submit
                                                </button>
                                            </div>
                                        </div>
                                    </form>
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