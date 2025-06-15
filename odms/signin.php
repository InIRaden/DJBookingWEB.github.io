<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = md5($_POST['password']);

  $sql = "SELECT ID FROM tbluser_login WHERE UserName = :username AND Password = :password";
  $query = $dbh->prepare($sql);
  $query->bindParam(':username', $username, PDO::PARAM_STR);
  $query->bindParam(':password', $password, PDO::PARAM_STR);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);

  if ($query->rowCount() > 0) {
    foreach ($results as $result) {
      $_SESSION['odmsaid'] = $result->ID;
    }

    if (!empty($_POST["remember"])) {
      setcookie("user_login", $_POST["username"], time() + (10 * 365 * 24 * 60 * 60));
      setcookie("userpassword", $_POST["password"], time() + (10 * 365 * 24 * 60 * 60));
    } else {
      setcookie("user_login", "");
      setcookie("userpassword", "");
    }

    $_SESSION['login'] = $_POST['username'];
    echo "<script>
      window.onload = function() {
        Toastify({
          text: 'Login successful! Welcome back.',
          duration: 3000,
          gravity: 'top',
          position: 'right',
          backgroundColor: '#28a745',
        }).showToast();
        setTimeout(function() {
          window.location.href = 'index.php';
        }, 1000);
      }
    </script>";
  } else {
    echo "<script>
      window.onload = function() {
        Toastify({
          text: 'Invalid username or password!',
          duration: 3000,
          gravity: 'top',
          position: 'right',
          backgroundColor: '#dc3545',
        }).showToast();
      }
    </script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - DJ Management System</title>
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

        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        .login-image {
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
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
            overflow: hidden;
        }

        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 100%);
            z-index: 1;
        }

        .login-form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
        }

        .login-form-box {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px var(--shadow-color);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            animation: fadeIn 0.6s ease-out forwards;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-logo i {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .login-subtitle {
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

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .remember-me input {
            margin-right: 0.5rem;
            width: 16px;
            height: 16px;
        }

        .login-btn {
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

        .login-btn:hover {
            background: linear-gradient(45deg, #333333, #1a1a1a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .signup-link {
            text-align: center;
            margin-bottom: 1.2rem;
            font-size: 0.93rem;
            font-weight: 500;
        }

        .signup-link a {
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

        .signup-link a i {
            font-size: 1.1em;
            margin-right: 0.2em;
        }

        .signup-link a:hover {
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

        .system-name {
            position: relative;
            z-index: 2;
            color: var(--white);
            font-size: 2.5rem;
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
            color: #ffd700;
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
            .login-container {
                flex-direction: column;
            }

            .login-image {
                min-height: 30vh;
            }

            .login-form-container {
                padding: 2rem 1rem;
            }

            .login-form-box {
                padding: 2.5rem;
            }

            .form-group {
                margin-bottom: 1.8rem;
            }
        }

        @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
        }
        
        .login-image {
            min-height: 200px;
            border-top-right-radius: 50px;
            border-bottom-left-radius: 0;
        }

        .form-box {
            padding: 2rem;
            max-width: 100%;
        }

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
        .form-box {
            padding: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
    }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Image -->
    <div class="login-image">
        <div class="system-name animated">
            <i class="fas fa-music"></i> Online DJ Management System
        </div>
        <div class="copyright animated delay-1">
            <p>&copy; <span id="current-year"></span> All Rights Reserved • Made with <i class="fas fa-heart"></i></p>
        </div>
    </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <div class="login-form-box animated delay-2">
                <div class="login-logo">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h2 class="login-title animated delay-2">Welcome Back</h2>
                <p class="login-subtitle animated delay-2">Sign in to your account to continue</p>

                <form method="post" class="space-y-4">
                    <div class="form-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" id="username" required
                            value="<?php if (isset($_COOKIE["user_login"])) echo $_COOKIE["user_login"]; ?>"
                            class="form-control" placeholder=" ">
                        <label for="username" class="form-label">Username</label>
                    </div>
                    <div class="form-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" required
                            value="<?php if (isset($_COOKIE["userpassword"])) echo $_COOKIE["userpassword"]; ?>"
                            class="form-control" placeholder=" ">
                        <label for="password" class="form-label">Password</label>
                        <button type="button" class="password-toggle" onclick="togglePassword()" tabindex="-1" aria-label="Show/Hide Password">
                            <i id="password-toggle-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember"
                            class="form-checkbox" <?php if (isset($_COOKIE["user_login"])) echo 'checked'; ?>>
                        <label for="remember">Remember me</label>
                    </div>
                    <button type="submit" name="login" class="login-btn">
                        <i class="fas fa-sign-in-alt" style="margin-right:0.5em;"></i> Sign In
                    </button>
                    <div class="signup-link">
                        <a href="signup.php"><i class="fas fa-user-plus"></i> Don't have an account? Sign up now</a>
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