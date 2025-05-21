<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT ID FROM tbladmin WHERE UserName = :username AND Password = :password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $_SESSION['odmsaid'] = $result->ID;
        }

        // Remember me functionality
        if (!empty($_POST["remember"])) {
            setcookie("user_login", $_POST["username"], time() + (10 * 365 * 24 * 60 * 60));
            setcookie("userpassword", $_POST["password"], time() + (10 * 365 * 24 * 60 * 60));
        } else {
            if (isset($_COOKIE["user_login"])) {
                setcookie("user_login", "");
            }
            if (isset($_COOKIE["userpassword"])) {
                setcookie("userpassword", "");
            }
        }

        $_SESSION['login'] = $_POST['username'];
        echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
    } else {
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>
<!doctype html>
<html lang="en" class="no-focus">
<head>
    <title>Online DJ Management System - Login Page</title>
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
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
                    <p>&copy; <span id="current-year"></span> All Rights Reserved</p>
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="login-form-container">
                <div class="login-form-box animated delay-2">
                    <div class="login-logo">
                        <i class="si si-fire" style="font-size: 3rem; color: var(--primary-color);"></i>
                    </div>
                    
                    <h1 class="login-title animated delay-2">Selamat Datang Kembali</h1>
                    <p class="login-subtitle animated delay-2">Masuk ke akun Anda untuk melanjutkan</p>
                    
                    <form method="post" class="animated delay-3">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" name="username" placeholder=" " required value="<?php if (isset($_COOKIE["user_login"])) echo $_COOKIE["user_login"]; ?>">
                            <label for="username" class="form-label">Username</label>
                        </div>
                        
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder=" " required value="<?php if (isset($_COOKIE["userpassword"])) echo $_COOKIE["userpassword"]; ?>">
                            <label for="password" class="form-label">Password</label>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fa fa-eye" id="password-toggle-icon"></i>
                            </span>
                        </div>
                        
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember" <?php if (isset($_COOKIE["user_login"])) echo 'checked'; ?>>
                            <label for="remember">Ingat saya</label>
                        </div>
                        
                        <button type="submit" class="login-btn" name="login">
                            Masuk
                        </button>
                        
                        <div class="forgot-password">
                            <a href="forgot-password.php">Lupa password?</a>
                        </div>
                        
                        <div class="back-home animated delay-4">
                            <a href="../index.php">
                                <i class="fa fa-arrow-left"></i> Kembali ke Website
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
        // Update current year
        document.getElementById('current-year').textContent = new Date().getFullYear();
        
        // Password visibility toggle
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const icon = document.getElementById('password-toggle-icon');
            
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
    </script>
</body>
</html>
