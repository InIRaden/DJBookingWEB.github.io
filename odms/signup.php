<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $regdate = date('Y-m-d H:i:s');

    $sql_check = "SELECT * FROM tbluser_login WHERE UserName = :username OR Email = :email";
    $query_check = $dbh->prepare($sql_check);
    $query_check->bindParam(':username', $username, PDO::PARAM_STR);
    $query_check->bindParam(':email', $email, PDO::PARAM_STR);
    $query_check->execute();

    if ($query_check->rowCount() > 0) {
        echo "<script>
            window.onload = function() {
                Toastify({
                    text: 'Username or Email already exists!',
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                    backgroundColor: '#dc3545',
                }).showToast();
            }
        </script>";
    } else {
        $sql = "INSERT INTO tbluser_login (UserName, NameUser, MobileNumber, Email, Password, AdminRegdate) 
                VALUES (:username, :name, :mobile, :email, :password, :regdate)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);
        $query->bindParam(':regdate', $regdate, PDO::PARAM_STR);
        $query->execute();

        echo "<script>
            window.onload = function() {
                Toastify({
                    text: 'Registration successful! Please login.',
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                    backgroundColor: '#28a745',
                }).showToast();
                setTimeout(function() {
                    window.location.href = 'signin.php';
                }, 1000);
            }
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - DJ Management System</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <style>
        :root {
            --primary-color: #222222;
            --secondary-color: #444444;
            --dark-text: #333;
            --light-text: #777;
            --very-light-text: #999;
            --white: #ffffff;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--white);
            color: var(--dark-text);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            margin: 0;
        }

        .register-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        .register-image {
            flex: 1;
            background: url('images/photobooth.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
            padding: 2rem;
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
            overflow: hidden;
            z-index: -99;
        }

        .register-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 100%);
            z-index: 1;
        }

        .system-name {
            position: relative;
            z-index: 2;
            color: var(--white);
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            text-align: left;
            width: 100%;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .system-name i {
            margin-right: 0.5rem;
            color: var(--white);
        }

        .copyright {
            position: relative;
            z-index: 2;
            color: var(--white);
            font-size: 1rem;
            text-align: left;
            width: 100%;
            font-weight: 500;
            opacity: 0.9;
        }

        .copyright i {
            color: #ff4444;
            margin: 0 0.25rem;
        }

        .register-form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
        }

        .register-form-box {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px var(--shadow-color);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            animation: fadeIn 0.6s ease-out forwards;
        }

        .register-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .register-logo i {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .register-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .register-subtitle {
            font-size: 0.9rem;
            color: var(--light-text);
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.2rem;
            position: relative;
            border: none;
        }

        .form-group i.input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
            font-size: 1.1rem;
            z-index: 1;
        }

        .form-group .form-control {
            padding-left: 3rem;
        }

        .form-control {
            height: 45px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0 1rem 0 2.5rem;
            font-size: 0.95rem;
            color: var(--dark-text);
            background-color: var(--light-bg);
            transition: all 0.2s ease;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            align-items: center;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.1rem rgba(34, 34, 34, 0.15);
            outline: none;
        }

        .form-label {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 1rem;
            color: var(--light-text);
            transition: all 0.2s ease;
            pointer-events: none;
            background-color: var(--light-bg);
            padding: 0 0.5rem;
            font-size: 1rem;
        }

        .form-control:focus + .form-label,
        .form-control:not(:placeholder-shown) + .form-label {
            top: 0;
            transform: translateY(-50%);
            font-size: 0.85rem;
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
            cursor: pointer;
            z-index: 10;
            font-size: 1.15rem;
            background: none;
            border: none;
            outline: none;
            padding: 0;
            transition: color 0.2s;
        }
        .password-toggle:hover {
            color: var(--primary-color);
        }

        .register-btn {
            display: block;
            width: 100%;
            height: 45px;
            border-radius: 8px;
            background: linear-gradient(45deg, #1a1a1a, #333333);
            color: var(--white);
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .register-btn:hover {
            background: linear-gradient(45deg, #333333, #1a1a1a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-bottom: 1.2rem;
            font-size: 0.93rem;
            font-weight: 500;
        }
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4em;
            background: rgba(0,0,0,0.04);
            padding: 0.4em 1em;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }
        .login-link a i {
            font-size: 1.1em;
            margin-right: 0.2em;
        }
        .login-link a:hover {
            color: #007bff;
            background: rgba(0,0,0,0.08);
        }

        .back-home {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .back-home a {
            display: inline-flex;
            align-items: center;
            color: var(--light-text);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.05);
        }

        .back-home a i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .back-home a:hover {
            color: var(--primary-color);
            background: rgba(0, 0, 0, 0.08);
        }

        .back-home a:hover i {
            transform: translateX(-4px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 992px) {
            .register-container {
                flex-direction: column;
            }

            .register-image {
                min-height: 30vh;
            }

            .register-form-container {
                padding: 2rem 1rem;
            }

            .register-form-box {
                padding: 2.5rem;
            }

            .form-group {
                margin-bottom: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .system-name {
                font-size: 2rem;
            }
            
            .copyright {
                font-size: 0.9rem;
            }
            
            .back-home {
                margin-top: 1rem;
                padding-top: 1rem;
            }
        }

        @media (max-height: 600px) {
            .register-form-box {
                padding: 1.5rem;
            }
            
            .form-group {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Left Side - Image -->
    <div class="register-image">
        <div class="system-name animated">
            <i class="fas fa-music">Online Event Booking With DJs System</i> 
        </div>
        <div class="copyright animated delay-1">
            <p>&copy; <span id="current-year"></span> All Rights Reserved • Made with ❤️<i class="fas fa-heart"></i></p>
        </div>
    </div>

        <!-- Right Side - Registration Form -->
        <div class="register-form-container">
            <div class="register-form-box animated delay-2">
                <div class="register-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2 class="register-title animated delay-2" style="font-weight: 700;">Create Account</h2>
                <p class="register-subtitle animated delay-2">Please fill in the form to register</p>

                <form method="post" class="space-y-4">
                    <div class="form-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" id="username" required class="form-control" placeholder=" ">
                        <label for="username" class="form-label">Username</label>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-id-card input-icon"></i>
                        <input type="text" name="name" id="name" required class="form-control" placeholder=" ">
                        <label for="name" class="form-label">Full Name</label>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="text" name="mobile" id="mobile" required pattern="[0-9]{10,13}" class="form-control" placeholder=" ">
                        <label for="mobile" class="form-label">Phone Number</label>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" id="email" required class="form-control" placeholder=" ">
                        <label for="email" class="form-label">Email</label>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" required minlength="6" class="form-control" placeholder=" ">
                        <label for="password" class="form-label">Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword()" tabindex="-1" aria-label="Show/Hide Password">
                            <i id="password-toggle-icon" class="fas fa-eye"></i>
                        </button>
                    </div>

                    <button type="submit" name="register" class="register-btn">Sign Up</button>

                    <div class="login-link">
                        <a href="signin.php"><i class="fas fa-sign-in-alt"></i> Already have an account?<b>Sign in here</b> </a>
                    </div>

                    <div class="back-home">
                        <a href="index.php">
                            <i class="fas fa-arrow-left"></i> Back to Website
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();

        function togglePassword() {
            const field = document.getElementById("password");
            const icon = document.getElementById("password-toggle-icon");
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>