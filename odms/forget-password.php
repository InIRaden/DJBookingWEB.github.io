<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $newpassword = md5($_POST['newpassword']);

    $sql = "SELECT Email FROM tbluser_login WHERE Email=:email AND MobileNumber=:mobile";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if($query->rowCount() > 0) {
        $sql = "UPDATE tbluser_login SET Password=:newpassword WHERE Email=:email AND MobileNumber=:mobile";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $query->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        $query->execute();

        echo "<script>alert('Password berhasil diubah');</script>";
        echo "<script>window.location.href='signin.php'</script>";
    } else {
        echo "<script>alert('Email atau Nomor HP tidak valid');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - DJ Management System</title>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>    <style>
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
            --gray-color: #888888;
        }

        body {
            background-color: var(--white);
            color: var(--dark-text);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            margin: 0;
        }

        .reset-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        .reset-image {
            flex: 1;
            background: url('images/partyDj.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
            padding: 2rem;
            border-top-left-radius: 50px;
            border-bottom-left-radius: 50px;
            overflow: hidden;
        }

        .reset-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 100%);
            z-index: 1;
        }

        .reset-form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
        }

        .reset-form-box {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px var(--shadow-color);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            animation: formSlideIn 0.6s ease-out forwards;
        }

        @keyframes formSlideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .reset-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .reset-logo i {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .reset-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .reset-subtitle {
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

        .form-control {
            width: 100%;
            height: 45px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0 1rem 0 2.5rem;
            font-size: 0.95rem;
            color: var(--dark-text);
            background-color: var(--light-bg);
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.1rem rgba(34, 34, 34, 0.15);
            outline: none;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
            cursor: pointer;
            z-index: 2;
        }

        .reset-btn {
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
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }

        .reset-btn:hover {
            background: linear-gradient(45deg, #333333, #1a1a1a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .reset-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .back-to-login {
            text-align: center;
            margin-top: 1rem;
            color: var(--light-text);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .back-to-login:hover {
            color: var(--primary-color);
        }

        @media (max-width: 992px) {
            .reset-container {
                flex-direction: column;
            }

            .reset-image {
                min-height: 30vh;
            }

            .reset-form-container {
                padding: 2rem 1rem;
            }
        }

        @media (max-width: 768px) {
            .reset-container {
                flex-direction: column;
            }
            
            .reset-image {
                min-height: 200px;
                border-top-right-radius: 50px;
                border-bottom-left-radius: 0;
            }

            .reset-form-box {
                padding: 2rem;
                max-width: 100%;
            }

            .system-name {
                font-size: 2rem;
            }
            
            .copyright {
                font-size: 0.9rem;
            }
        }

        @media (max-height: 600px) {
            .reset-form-box {
                padding: 1.5rem;
            }
            
            .form-group {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-form-container">
            <div class="reset-form-box">
                <div class="reset-logo">
                    <i class="fas fa-headphones-alt"></i>
                </div>
                <h2 class="reset-title">Reset Password</h2>
                <p class="reset-subtitle">Enter your email and mobile number to reset your password</p>
                
                <form method="post" action="" id="reset-form">
                    <div class="form-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="text" class="form-control" name="mobile" placeholder="Mobile Number" required>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-lock input-icon"></i>
                        <div class="password-container">
                            <input type="password" class="form-control" name="newpassword" id="newpassword" placeholder="New Password" required minlength="6">
                            <span class="toggle-password" onclick="togglePassword('newpassword')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <i class="fas fa-lock input-icon"></i>
                        <div class="password-container">
                            <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="Confirm Password" required minlength="6">
                            <span class="toggle-password" onclick="togglePassword('confirmpassword')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit" class="reset-btn" id="reset-btn">Reset Password</button>
                    
                    <div style="text-align: center;">
                        <a href="signin.php" class="back-to-login">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="reset-image"></div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.parentElement.querySelector('.toggle-password i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.getElementById('reset-form').addEventListener('submit', function(event) {
            const newPassword = document.getElementById('newpassword').value;
            const confirmPassword = document.getElementById('confirmpassword').value;

            if (newPassword !== confirmPassword) {
                event.preventDefault();
                Toastify({
                    text: "Passwords do not match!",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "#dc3545",
                }).showToast();
            } else if (newPassword.length < 6) {
                event.preventDefault();
                Toastify({
                    text: "Password must be at least 6 characters long!",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "center",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        });
    </script>
</body>
</html>
