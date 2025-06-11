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

// Handle form submission
if (isset($_POST['submit'])) {
    $sername = $_POST['sername'] ?? '';
    $serdes = $_POST['serdes'] ?? '';
    $serprice = $_POST['serprice'] ?? '';

    // Basic validation
    if (empty($sername) || empty($serdes) || !is_numeric($serprice) || $serprice < 0) {
        echo '<script>alert("Please fill all fields correctly. Price must be a positive number.")</script>';
    } else {
        try {
            $sql = "INSERT INTO tblservice(ServiceName, SerDes, ServicePrice) VALUES (:sername, :serdes, :serprice)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':sername', $sername, PDO::PARAM_STR);
            $query->bindParam(':serdes', $serdes, PDO::PARAM_STR);
            $query->bindParam(':serprice', $serprice, PDO::PARAM_STR);
            $query->execute();

            $LastInsertId = $dbh->lastInsertId();
            if ($LastInsertId > 0) {
                echo '<script>alert("Service has been added successfully.")</script>';
                echo "<script>window.location.href='add-services.php'</script>";
            } else {
                echo '<script>alert("Something went wrong. Please try again.")</script>';
            }
        } catch (PDOException $e) {
            echo '<script>alert("Database error: ' . addslashes($e->getMessage()) . '")</script>';
        }
    }
}
?>

<!doctype html>
<html lang="en" class="no-focus">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online DJ Management System - Add Services</title>
    <!-- Retain original stylesheet for sidebar and header -->
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
    <!-- Google Fonts for modern typography in main container -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Scope styles to main-container and its children */
        #main-container {
            background-color: #f4f7fc;
            padding: 20px;
            min-height: 100vh;
        }

        #main-container .content {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        #main-container .content-heading {
            font-family: 'Inter', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Card-like container for the form */
        #main-container .form-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 24px;
            transition: transform 0.3s ease;
        }

        #main-container .form-card:hover {
            transform: translateY(-4px);
        }

        /* Form group styling */
        #main-container .form-group {
            margin-bottom: 20px;
        }

        #main-container .form-group label {
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: #1e3a8a;
            margin-bottom: 8px;
            display: block;
        }

        #main-container .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            color: #374151;
            background: #f9fafb;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        #main-container .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            background: #ffffff;
        }

        #main-container textarea.form-control {
            resize: vertical;
            min-height: 80px;
            max-height: 200px;
        }

        /* Button styling */
        #main-container .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            background: #3b82f6;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        #main-container .btn-submit:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        #main-container .btn-submit i {
            font-size: 1.1rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #main-container .content {
                padding: 15px;
            }

            #main-container .content-heading {
                font-size: 1.5rem;
            }

            #main-container .form-card {
                padding: 16px;
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
                <h2 class="content-heading">Add New Service</h2>
                <div class="form-card">
                    <form method="post" onsubmit="return validateForm()">
                        <div class="form-group">
                            <label for="sername">Service Name</label>
                            <input type="text" class="form-control" id="sername" name="sername" required placeholder="e.g., DJ Night Package">
                        </div>
                        <div class="form-group">
                            <label for="serdes">Service Description</label>
                            <textarea class="form-control" id="serdes" name="serdes" required placeholder="Describe the service details"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="serprice">Service Price ($)</label>
                            <input type="number" class="form-control" id="serprice" name="serprice" required placeholder="e.g., 500.00" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-submit" name="submit">
                                <i class="fa fa-plus"></i> Add Service
                            </button>
                        </div>
                    </form>
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

    <script>
        // Client-side form validation
        function validateForm() {
            const sername = document.getElementById('sername').value.trim();
            const serdes = document.getElementById('serdes').value.trim();
            const serprice = parseFloat(document.getElementById('serprice').value);

            if (!sername || !serdes || isNaN(serprice) || serprice < 0) {
                alert('Please fill all fields correctly. Price must be a positive number.');
                return false;
            }
            return true;
        }

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