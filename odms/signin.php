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
        echo "<script>location.href='index.php';</script>";
    } else {
        echo "<script>alert('Username atau password salah');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - DJ Management System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="min-h-screen flex">
  <!-- Kiri - Gambar -->
  <div class="hidden md:flex md:w-1/2 bg-cover bg-center relative" style="background-image: url('assets/img/photos/photo34@2x.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center items-center text-white text-center p-6">
      <h1 class="text-3xl font-bold mb-2">Online DJ Management System</h1>
      <p class="text-sm">&copy; <span id="year"></span> All Rights Reserved</p>
    </div>
  </div>

  <!-- Kanan - Form Login -->
  <div class="flex w-full md:w-1/2 justify-center items-center p-8 bg-white">
    <div class="w-full max-w-md space-y-6">
      <div class="text-center">
        <i class="fas fa-sign-in-alt text-blue-600 text-4xl mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali</h2>
        <p class="text-sm text-gray-500">Masuk ke akun Anda untuk melanjutkan</p>
      </div>

      <form method="post" class="space-y-4">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
          <input type="text" name="username" id="username" required
                 value="<?php if (isset($_COOKIE["user_login"])) echo $_COOKIE["user_login"]; ?>"
                 class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400">
        </div>

        <div class="relative">
          <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
          <input type="password" name="password" id="password" required
                 value="<?php if (isset($_COOKIE["userpassword"])) echo $_COOKIE["userpassword"]; ?>"
                 class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400">
          <div class="absolute inset-y-0 right-3 flex items-center cursor-pointer" onclick="togglePassword()">
            <i id="password-toggle-icon" class="fas fa-eye text-gray-400"></i>
          </div>
        </div>

        <div class="flex items-center justify-between text-sm text-gray-600">
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="remember" id="remember"
                   class="form-checkbox text-blue-600" <?php if (isset($_COOKIE["user_login"])) echo 'checked'; ?>>
            <span>Ingat saya</span>
          </label>
          <a href="forgot-password.php" class="text-blue-600 hover:underline">Lupa password?</a>
        </div>

        <button type="submit" name="login"
                class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
          Masuk
        </button>

        <div class="text-sm text-center mt-4">
          <a href="signup.php" class="text-blue-600 hover:underline">Belum punya akun? Daftar sekarang</a>
        </div>

        <div class="text-center mt-4">
          <a href="index.php" class="text-gray-500 hover:underline">
            <i class="fas fa-arrow-left"></i> Kembali ke Website
          </a>
        </div>
      </form>
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
