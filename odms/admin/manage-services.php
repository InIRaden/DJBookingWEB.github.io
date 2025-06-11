<?php
// Start session to track user login status
session_start();

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection configuration
include('includes/dbconnection.php');

// Check if user is logged in, redirect to logout if not
if (empty($_SESSION['odmsaid'])) {
    header('location:logout.php');
    exit;
}

// Handle service deletion
if (isset($_GET['delid'])) {
    $rid = intval($_GET['delid']);
    try {
        $sql = "DELETE FROM tblservice WHERE ID=:rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Service deleted successfully.');</script>";
        echo "<script>window.location.href='manage-services.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting service: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!doctype html>
<html lang="en" class="no-focus">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online DJ Management System - Manage Services</title>
    <!-- Retain original stylesheets for sidebar and header -->
    <link rel="stylesheet" href="assets/js/plugins/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
    <!-- Google Fonts for main container typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Scope styles to main-container */
        #main-container {
            background-color: #f4f7fc;
            padding: 20px;
            min-height: 100vh;
        }

        #main-container .content {
            max-width: 1400px;
            /* Increased from 1280px for a wider container */
            margin: 0 auto;
            padding: 40px 32px;
            /* Increased padding for more breathing room */
        }

        #main-container .content-heading {
            font-family: 'Inter', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 24px;
        }

        /* Card-like container for the table */
        #main-container .table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        #main-container .table-card:hover {
            transform: translateY(-4px);
        }

        /* Table header styling */
        #main-container .table-header {
            background: #3b82f6;
            padding: 16px 24px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #main-container .table-title {
            font-family: 'Inter', sans-serif;
            font-size: 1.25rem;
            font-weight: 500;
            color: #ffffff;
            margin: 0;
        }

        /* Enhanced search input */
        #main-container .dataTables_filter {
            margin: 0;
            padding: 8px 0;
        }

        #main-container .dataTables_filter input {
            width: 200px;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #374151;
            background: #f9fafb;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        #main-container .dataTables_filter input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            background: #ffffff;
        }

        #main-container .dataTables_filter label {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #ffffff;
            margin-right: 10px;
        }

        /* Spacing for "Show" and "entries" */
        #main-container .dataTables_length {
            padding: 8px 0;
            margin-right: 20px;
            /* Added margin to prevent closeness to edges */
        }

        #main-container .dataTables_length select {
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #374151;
            background: #f9fafb;
            transition: border-color 0.3s ease;
        }

        #main-container .dataTables_length select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Table styling */
        #main-container .js-dataTable-full-pagination {
            margin: 0;
            border: none;
            width: 100%;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            color: #374151;
        }

        #main-container .js-dataTable-full-pagination thead {
            background: #f9fafb;
        }

        #main-container .js-dataTable-full-pagination thead th {
            font-weight: 500;
            color: #1e3a8a;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        #main-container .js-dataTable-full-pagination tbody tr {
            transition: background 0.2s ease;
        }

        #main-container .js-dataTable-full-pagination tbody tr:hover {
            background: #f1f5f9;
        }

        #main-container .js-dataTable-full-pagination tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        #main-container .js-dataTable-full-pagination .text-center {
            text-align: center;
        }

        #main-container .js-dataTable-full-pagination .font-w600 {
            font-weight: 500;
        }

        /* Badge styling */
        #main-container .badge-primary {
            background: #3b82f6;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Delete button styling */
        #main-container .btn-danger {
            background: #ef4444;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            color: #ffffff;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        #main-container .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        /* Pagination styling */
        #main-container .dataTables_paginate {
            padding: 8px 0;
        }

        #main-container .paginate_button {
            padding: 6px 12px;
            margin: 0 4px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            color: #374151;
            transition: background 0.3s ease, color 0.3s ease;
        }

        #main-container .paginate_button:hover {
            background: #3b82f6;
            color: #ffffff;
            border-color: #3b82f6;
        }

        #main-container .paginate_button.current {
            background: #3b82f6;
            color: #ffffff;
            border-color: #3b82f6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #main-container .content {
                padding: 15px 12px;
            }

            #main-container .content-heading {
                font-size: 1.5rem;
            }

            #main-container .js-dataTable-full-pagination {
                font-size: 0.85rem;
            }

            #main-container .js-dataTable-full-pagination thead th,
            #main-container .js-dataTable-full-pagination tbody td {
                padding: 8px 12px;
            }

            #main-container .dataTables_filter input {
                width: 150px;
            }

            #main-container .dataTables_length {
                margin-right: 10px;
            }

            #main-container .d-none {
                display: none !important;
            }
        }

        /* Alert styling */
        #main-container .alert {
            font-family: 'Inter', sans-serif;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.9rem;
            text-align: center;
        }

        #main-container .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        #main-container .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
        <!-- Keep original sidebar and header -->
        <?php include_once('includes/sidebar.php'); ?>
        <?php include_once('includes/header.php'); ?>

        <!-- Main Container -->
        <main id="main-container">
            <div class="content">
                <h2 class="content-heading">Manage Services</h2>
                <div class="table-card">
                    <div class="table-header">
                        <h3 class="table-title">Service List</h3>
                    </div>
                    <table class="table table-striped table-vcenter js-dataTable-full-pagination">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th>Service Name</th>
                                <th class="d-none d-sm-table-cell">Service Price</th>
                                <th class="d-none d-sm-table-cell">Creation Date</th>
                                <th class="d-none d-sm-table-cell" style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM tblservice";
                            try {
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $cnt = 1;

                                if ($query->rowCount() > 0) {
                                    foreach ($results as $row) {
                            ?>
                                        <tr>
                                            <td class="text-center"><?php echo htmlentities($cnt); ?></td>
                                            <td class="font-w600"><?php echo htmlentities($row->ServiceName); ?></td>
                                            <td class="d-none d-sm-table-cell">$<?php echo number_format($row->ServicePrice, 2); ?></td>
                                            <td class="d-none d-sm-table-cell">
                                                <span class="badge badge-primary"><?php echo htmlentities($row->CreationDate); ?></span>
                                            </td>
                                            <td class="d-none d-sm-table-cell">
                                                <a href="manage-services.php?delid=<?php echo $row->ID; ?>" onclick="return confirm('Do you really want to delete this service?');" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                            <?php
                                        $cnt++;
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center">No services found.</td></tr>';
                                }
                            } catch (PDOException $e) {
                                echo "<script>alert('Database error: " . addslashes($e->getMessage()) . "');</script>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Keep original footer -->
        <?php include_once('includes/footer.php'); ?>
    </div>

    <!-- Retain original JavaScript dependencies -->
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

    <script>
        // Fade out alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>

</html>