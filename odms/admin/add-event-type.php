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
    $etype = $_POST['eventtype'] ?? '';

    // Basic validation
    if (empty($etype)) {
        echo '<script>alert("Please enter an event type.")</script>';
    } else {
        try {
            $sql = "INSERT INTO tbleventtype(EventType) VALUES (:etype)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':etype', $etype, PDO::PARAM_STR);
            $query->execute();

            $LastInsertId = $dbh->lastInsertId();
            if ($LastInsertId > 0) {
                echo '<script>alert("Event Type has been added successfully.")</script>';
                echo "<script>window.location.href ='add-event-type.php'</script>";
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
    <title>Online DJ Management System - Add Event Type</title>
    <!-- Retain original stylesheet for sidebar and header -->
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
    <!-- Google Fonts for modern typography in main container -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Scope styles to main-container */
        #main-container {
            background-color: #f4f7fc;
            padding: 20px;
            min-height: 100vh;
        }

        #main-container .content {
            max-width: 800px;
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
                padding: 15px 12px;
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
                <h2 class="content-heading">Add Event Type</h2>
                <div class="form-card">
                    <form method="post" onsubmit="return validateForm()">
                        <div class="form-group">
                            <label for="eventtype">Event Type</label>
                            <input type="text" class="form-control" id="eventtype" name="eventtype" required placeholder="e.g., Wedding, Party">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-submit" name="submit">
                                <i class="fa fa-plus"></i> Add Event Type
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
            const eventtype = document.getElementById('eventtype').value.trim();
            if (!eventtype) {
                alert('Please enter an event type.');
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