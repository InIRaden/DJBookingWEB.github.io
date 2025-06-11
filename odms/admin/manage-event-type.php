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

// Handle event type deletion
if (isset($_GET['delid'])) {
    $rid = intval($_GET['delid']);
    try {
        $sql = "DELETE FROM tbleventtype WHERE ID = :rid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':rid', $rid, PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Event type deleted successfully.');</script>";
        echo "<script>window.location.href = 'manage-event-type.php'</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting event type: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!doctype html>
<html lang="en" class="no-focus">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online DJ Management System - Manage Event Type</title>
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
            max-width: 1000px;
            /* Widened from a narrower default for a less cramped look */
            margin: 0 auto;
            padding: 30px 24px;
            /* Increased padding for breathing room */
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
        #main-container .block-header {
            background: #3b82f6;
            padding: 16px 24px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        #main-container .block-title {
            font-family: 'Inter', sans-serif;
            font-size: 1.25rem;
            font-weight: 500;
            color: #ffffff;
            margin: 0;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #main-container .content {
                padding: 15px 12px;
            }

            #main-container .content-heading {
                font-size: 1.5rem;
            }

            #main-container .table-card {
                padding: 16px;
            }

            #main-container .js-dataTable-full-pagination thead th,
            #main-container .js-dataTable-full-pagination tbody td {
                padding: 8px 12px;
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
                <h2 class="content-heading">Manage Event Type</h2>
                <div class="table-card">
                    <div class="block-header">
                        <h3 class="block-title">Event Type List</h3>
                    </div>
                    <div class="block-content">
                        <table class="table table-striped table-vcenter js-dataTable-full-pagination">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Event Name</th>
                                    <th>Creation Date</th>
                                    <th class="d-none d-sm-table-cell" style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM tbleventtype";
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
                                                <td class="font-w600"><?php echo htmlentities($row->EventType); ?></td>
                                                <td>
                                                    <span class="badge badge-primary"><?php echo htmlentities($row->CreationDate); ?></span>
                                                </td>
                                                <td>
                                                    <a href="manage-event-type.php?delid=<?php echo $row->ID; ?>"
                                                        onclick="return confirm('Do you really want to delete?');"
                                                        class="btn btn-danger btn-sm">Delete</a>
                                                </td>
                                            </tr>
                                <?php
                                            $cnt++;
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">No event types found.</td></tr>';
                                    }
                                } catch (PDOException $e) {
                                    echo "<script>alert('Database error: " . addslashes($e->getMessage()) . "');</script>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
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