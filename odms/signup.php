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
        echo "<script>alert('Username atau Email sudah digunakan.');</script>";
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

        echo "<script>alert('Registrasi berhasil! Silakan login.');document.location ='signin.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register - DJ Management System</title>
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

  <!-- Kanan - Form -->
  <div class="flex w-full md:w-1/2 justify-center items-center p-8 bg-white">
    <div class="w-full max-w-md space-y-6">
      <div class="text-center">
        <i class="fas fa-user-plus text-blue-600 text-4xl mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h2>
        <p class="text-sm text-gray-500">Silakan lengkapi formulir untuk mendaftar</p>
      </div>

      <form method="post" class="space-y-4">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
          <input type="text" name="username" id="username" required
                 class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400">
        </div>

        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
          <input type="text" name="name" id="name" required
                 class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400">
        </div>

        <div>
          <label for="mobile" class="block text-sm font-medium text-gray-700">Nomor HP</label>
          <input type="text" name="mobile" id="mobile" required pattern="[0-9]{10,13}"
                 class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400">
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" id="email" required
                 class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400">
        </div>

        <div class="relative">
          <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
          <input type="password" name="password" id="password" required minlength="6"
                 class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-400">
          <div class="absolute inset-y-0 right-3 flex items-center cursor-pointer" onclick="togglePassword()">
            <i id="password-toggle-icon" class="fas fa-eye text-gray-400"></i>
          </div>
        </div>

        <button type="submit" name="register"
                class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
          Daftar
        </button>

        <div class="text-sm text-center mt-4">
          <a href="signin.php" class="text-blue-600 hover:underline">Sudah punya akun? Masuk di sini</a>
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

