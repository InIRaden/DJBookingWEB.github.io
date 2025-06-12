<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['odmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $sername = $_POST['sername'];
        $serdes = $_POST['serdes'];
        $serprice = $_POST['serprice'];

        $sql = "INSERT INTO tblservice(ServiceName, SerDes, ServicePrice) VALUES (:sername, :serdes, :serprice)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':sername', $sername, PDO::PARAM_STR);
        $query->bindParam(':serdes', $serdes, PDO::PARAM_STR);
        $query->bindParam(':serprice', $serprice, PDO::PARAM_STR);
        $query->execute();

        $LastInsertId = $dbh->lastInsertId();
        if ($LastInsertId > 0) {
            echo '<script>alert("Services has been added.")</script>';
            echo "<script>window.location.href ='add-services.php'</script>";
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
    }
?>
    <!doctype html>
    <html lang="en" class="no-focus">

    <head>
        <title>Online DJ Management System - Add Services</title>
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
                    <h2 class="content-heading">Add Services</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block block-themed">
                                <div class="block-header">
                                    <h3 class="block-title">Add Services</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                            <i class="si si-refresh"></i>
                                        </button>
                                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle">
                                            <i class="si si-arrow-up"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <form method="post">
                                        <div class="form-group row">
                                            <label class="col-12">Service Name:</label>
                                            <div class="col-12">
                                                <input type="text" class="form-control" name="sername" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12">Service Description:</label>
                                            <div class="col-12">
                                                <textarea class="form-control" name="serdes" required></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12">Service Price:</label>
                                            <div class="col-12">
                                                <input type="text" class="form-control" name="serprice" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-alt-success" name="submit">
                                                    <i class="fa fa-plus mr-5"></i> Add
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