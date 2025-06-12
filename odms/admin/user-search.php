<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['odmsaid']) == 0) {
    header('location:logout.php');
} else {
?>
    <!doctype html>
    <html lang="en" class="no-focus">

    <head>
        <title>Online DJ Management System - Search User Queries</title>
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

            .btn-info,
            .btn-primary {
                background: #ffffff;
                border: 2px solid #007BFF;
                color: #007BFF;
                border-radius: 8px;
                padding: 5px 15px;
                font-weight: bold;
                transition: all 0.3s ease;
            }

            .btn-danger {
                background: #ffffff;
                border: 2px solid #FF4D4D;
                color: #FF4D4D;
                border-radius: 8px;
                padding: 5px 15px;
                font-weight: bold;
                transition: all 0.3s ease;
            }

            .btn-info:hover,
            .btn-primary:hover {
                background: #007BFF;
                color: #ffffff;
                transform: translateY(-1px);
            }

            .btn-danger:hover {
                background: #FF4D4D;
                color: #ffffff;
                transform: translateY(-1px);
            }

            .form-control {
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 8px;
            }

            .form-group label {
                color: #1e3c72;
                font-weight: 600;
            }

            .form-group {
                margin-bottom: 20px;
            }

            h4 {
                color: #1e3c72;
                font-weight: bold;
                margin-bottom: 20px;
            }
        </style>
    </head>

    <body>
        <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
            <?php include_once('includes/sidebar.php'); ?>
            <?php include_once('includes/header.php'); ?>
            <main id="main-container">
                <div class="content">
                    <h2 class="content-heading">Search User Queries</h2>
                    <div class="block">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Search User Queries</h3>
                        </div>
                        <div class="block-content block-content-full">
                            <form id="basic-form" method="post">
                                <div class="form-group">
                                    <label>Search by Name/Mobile No.</label>
                                    <input id="searchdata" type="text" name="searchdata" required="true" class="form-control" placeholder="Name/Mobile No.">
                                </div>
                                <button type="submit" class="btn btn-primary" name="search" id="submit">Search</button>
                            </form>
                            <?php
                            if (isset($_POST['search'])) {
                                $sdata = $_POST['searchdata'];
                            ?>
                                <h4 align="center">Result against "<?php echo htmlentities($sdata); ?>" keyword</h4>
                                <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                    <thead>
                                        <tr>
                                            <th class="text-center"></th>
                                            <th class="d-none d-sm-table-cell">Name</th>
                                            <th class="d-none d-sm-table-cell">Mobile Number</th>
                                            <th class="d-none d-sm-table-cell">Email</th>
                                            <th class="d-none d-sm-table-cell">Send Message Date</th>
                                            <th class="d-none d-sm-table-cell" style="width: 15%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * from tbluser where Name like '$sdata%' || MobileNumber like '$sdata%'";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $row) {
                                        ?>
                                                <tr>
                                                    <td class="text-center"><?php echo htmlentities($cnt); ?></td>
                                                    <td class="font-w600"><?php echo htmlentities($row->Name); ?></td>
                                                    <td class="font-w600"><?php echo htmlentities($row->MobileNumber); ?></td>
                                                    <td class="font-w600"><?php echo htmlentities($row->Email); ?></td>
                                                    <td class="font-w600">
                                                        <span class="badge badge-primary"><?php echo htmlentities($row->MsgDate); ?></span>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell">
                                                        <a href="view-user-queries.php?viewid=<?php echo htmlentities($row->ID); ?>" class="btn btn-info btn-sm" target="_blank">View</a>
                                                        <a href="unread-queries.php?delid=<?php echo htmlentities($row->ID); ?>" onclick="return confirm('Do you really want to Delete ?');" class="btn btn-danger btn-sm">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php
                                                $cnt++;
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="6">No record found against this search</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
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
<?php } ?>