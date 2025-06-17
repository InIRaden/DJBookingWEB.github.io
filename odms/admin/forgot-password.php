<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $newpassword = md5($_POST['newpassword']);
    $sql = "SELECT Email FROM tbladmin WHERE Email=:email and MobileNumber=:mobile";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
        $con = "UPDATE tbladmin SET Password=:newpassword WHERE Email=:email AND MobileNumber=:mobile";
        $chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
        $chngpwd1->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        $chngpwd1->execute();
        echo "<script>alert('Your Password successfully changed');</script>";
    } else {
        echo "<script>alert('Email id or Mobile no is invalid');</script>";
    }
}
?>

<!doctype html>
<html lang="en" class="no-focus">

<head>
    <title>Online DJ Management System - Forgot Password</title>
    <link rel="stylesheet" href="assets/css/codebase.min.css">
    <link rel="stylesheet" href="assets/css/custom-dark.css">
    <link rel="stylesheet" href="assets/css/login-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <div id="page-container">
        <div class="login-container">
            <!-- Left Side - Image -->
            <div class="login-image" style="background-image: url('assets/img/photos/photo34@2x.jpg');">
                <div class="system-name animated">
                    Online DJ Management System
                </div>
                <div class="copyright animated delay-1">
                    <p>© <span id="current-year"></span> All Rights Reserved</p>
                </div>
            </div>

            <!-- Right Side - Forgot Password Form -->
            <div class="login-form-container">
                <div class="login-form-box animated delay-2">
                    <div class="login-logo">
                        <i class="si si-fire" style="font-size: 3rem; color: var(--primary-color);"></i>
                    </div>

                    <h1 class="login-title animated delay-2">Reset Password</h1>
                    <p class="login-subtitle animated delay-2">Enter your details to reset your password</p>

                    <form method="post" class="animated delay-3" name="chngpwd" onsubmit="return valid();">
                        <div class="form-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder=" " required>
                            <label for="email" class="form-label">Email Address</label>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder=" " required maxlength="10" pattern="[0-9]+">
                            <label for="mobile" class="form-label">Mobile Number</label>
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder=" " required>
                            <label for="newpassword" class="form-label">New Password</label>
                            <span class="password-toggle" onclick="togglePassword('newpassword', 'newpassword-toggle-icon')">
                                <i class="fa fa-eye" id="newpassword-toggle-icon"></i>
                            </span>
                        </div>

                        <div class="form-group">
                            <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder=" " required>
                            <label for="confirmpassword" class="form-label">Confirm Password</label>
                            <span class="password-toggle" onclick="togglePassword('confirmpassword', 'confirmpassword-toggle-icon')">
                                <i class="fa fa-eye" id="confirmpassword-toggle-icon"></i>
                            </span>
                        </div>

                        <button type="submit" class="login-btn" name="submit">
                            Reset Password
                        </button>

                        <div class="back-home animated delay-4">
                            <a href="login.php">
                                <i class="fa fa-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="assets/js/core/jquery.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/core/jquery.slimscroll.min.js"></script>
    <script src="assets/js/core/jquery.scrollLock.min.js"></script>
    <script src="assets/js/core/jquery.appear.min.js"></script>
    <script src="assets/js/core/jquery.countTo.min.js"></script>
    <script src="assets/js/core/js.cookie.min.js"></script>
    <script src="assets/js/codebase.js"></script>

    <!-- Plugins -->
    <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>

    <script>
        // Update tahun saat ini
        document.getElementById('current-year').textContent = new Date().getFullYear();

        // Toggle visibilitas password
        function togglePassword(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validasi Password
        function valid() {
            if (document.chngpwd.newpassword.value !== document.chngpwd.confirmpassword.value) {
                alert("New Password and Confirm Password do not match!");
                document.chngpwd.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
</body>

</html>