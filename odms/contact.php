<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['submit'])) {
	$name = $_POST['name'];
	$mobnum = $_POST['mobnum'];
	$email = $_POST['email'];
	$msg = $_POST['message'];
	$sql = "insert into tbluser(Name,MobileNumber,Email,Message)values(:name,:mobnum,:email,:msg)";
	$query = $dbh->prepare($sql);
	$query->bindParam(':name', $name, PDO::PARAM_STR);
	$query->bindParam(':mobnum', $mobnum, PDO::PARAM_STR);
	$query->bindParam(':email', $email, PDO::PARAM_STR);
	$query->bindParam(':msg', $msg, PDO::PARAM_STR);
	$query->execute();
	$LastInsertId = $dbh->lastInsertId();
	if ($LastInsertId > 0) {
		// Ganti alert standar dengan notifikasi yang lebih menarik
		echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				showSuccessNotification('Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
			});
		</script>";
	} else {
		// Ganti alert error standar dengan notifikasi yang lebih menarik
		echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				showErrorNotification('Terjadi kesalahan. Silakan coba lagi.');
			});
		</script>";
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<title>DjBooking - Contact Us</title>
	<link rel="stylesheet" href="../src/output.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
	<style>
		body {
			font-family: "Inter", sans-serif;
		}

		/* Styling untuk notifikasi */
		.notification {
			position: fixed;
			top: 20px;
			right: 20px;
			padding: 15px 20px;
			border-radius: 5px;
			color: white;
			font-size: 14px;
			z-index: 1000;
			display: flex;
			align-items: center;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
			transform: translateX(120%);
			transition: transform 0.3s ease-in-out;
			max-width: 350px;
		}

		.notification.show {
			transform: translateX(0);
		}

		.notification-success {
			background-color: #822b2b;
			/* Sesuai dengan tema */
			border-left: 5px solid #ff5252;
		}

		.notification-error {
			background-color: #d32f2f;
			border-left: 5px solid #b71c1c;
		}

		.notification-icon {
			margin-right: 12px;
			font-size: 18px;
		}

		.notification-close {
			margin-left: 12px;
			cursor: pointer;
			opacity: 0.7;
		}

		.notification-close:hover {
			opacity: 1;
		}
	</style>
</head>

<body class="bg-black text-white">
<header class="relative">
        <?php include_once('includes/header.php'); ?>
        <div class="header-container" style="position:relative;width:100%;height:300px;overflow:hidden;">
            <img src="images/abt.jpg" alt="DJ performing at event" class="w-full h-[300px] object-cover header-image" />
            <div class="header-overlay" style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(to top,rgba(0,0,0,0.95) 0%,rgba(0,0,0,0.7) 60%,rgba(0,0,0,0.1) 100%);"></div>
            <div class="header-content" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;width:100%;max-width:500px;padding:0 20px;z-index:2;">
                <h1 class="header-title" style="font-size:2.2rem;font-weight:800;color:rgba(255,255,255,0.92);margin-bottom:1rem;text-shadow:0 0 10px #fff,0 0 18px #2563eb,2px 2px 8px rgba(0,0,0,0.3);letter-spacing:1px;">Contact</h1>
                <p class="header-text" style="color:rgba(255,255,255,0.8);font-size:1.1rem;text-shadow:1px 1px 2px rgba(0,0,0,0.3);">Learn more about our DJ services and what makes us special</p>
            </div>
        </div>
    </header>
	<!-- Main Content -->
	<main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto">
		<!-- Breadcrumb -->
		<div class="flex items-center space-x-2 text-xs mb-8">
			<a href="index.php" class="text-gray-400 hover:text-white">Home</a>
			<span class="text-gray-600">/</span>
			<span class="text-white">Contact</span>
		</div>

		<!-- Contact Content -->
		<section class="mb-12">
			<?php
			$sql = "SELECT * from tblpage where PageType='contactus'";
			$query = $dbh->prepare($sql);
			$query->execute();
			$results = $query->fetchAll(PDO::FETCH_OBJ);

			$cnt = 1;
			if ($query->rowCount() > 0) {
				foreach ($results as $row) { ?>
					<h2 class="font-semibold text-white text-lg mb-6"><?php echo htmlentities($row->PageTitle); ?></h2>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
						<div>
							<div class="bg-gray-800 p-6 rounded-md">
								<h3 class="text-white text-sm font-semibold mb-4">Contact Information</h3>
								<div class="space-y-4 text-gray-300 text-xs">
									<p class="flex items-center">
										<span class="mr-2"><i class="fas fa-map-marker-alt"></i></span>
										<?php echo htmlentities($row->PageDescription); ?>
									</p>
									<p class="flex items-center">
										<span class="mr-2"><i class="fas fa-envelope"></i></span>
										<?php echo htmlentities($row->Email); ?>
									</p>
									<p class="flex items-center">
										<span class="mr-2"><i class="fas fa-phone"></i></span>
										<?php echo htmlentities($row->MobileNumber); ?>
									</p>
								</div>
							</div>

							<!-- Map -->
							<div class="mt-6 h-[250px] rounded-md overflow-hidden">
								<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15842.5451974668!2d107.72809224999999!3d-6.9339996500000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68c323777ca3a1%3A0x355eff6734ed9167!2sUniversitas%20Pendidikan%20Indonesia%20(UPI)%20Kampus%20Cibiru!5e0!3m2!1sid!2sid!4v1747864079973!5m2!1sid!2sid"
									class="w-full h-full"
									style="border:0;"
									allowfullscreen=""
									loading="lazy">
								</iframe>
							</div>
						</div>

						<div class="bg-gray-800 p-6 rounded-md">
							<h3 class="text-white text-sm font-semibold mb-4">Send Us a Message</h3>
							<p class="text-gray-400 text-xs mb-4">Drop us a message and we'll get back to you soon.</p>

							<form method="post" class="space-y-4">
								<div>
									<label class="block text-gray-400 text-xs mb-1">Name</label>
									<input type="text" name="name" required="true" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500">
								</div>

								<div>
									<label class="block text-gray-400 text-xs mb-1">Email</label>
									<input type="email" name="email" required="true" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500">
								</div>

								<div>
									<label class="block text-gray-400 text-xs mb-1">Mobile Number</label>
									<input type="text" name="mobnum" required="true" maxlength="10" pattern="[0-9]+"
										class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500"
										onkeypress="return isNumberKey(event)"
										oninput="this.value = this.value.replace(/[^0-9]/g, '')">
								</div>

								<div>
									<label class="block text-gray-400 text-xs mb-1">Message</label>
									<textarea name="message" required="true" rows="4" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500"></textarea>
								</div>

								<button type="submit" name="submit" class="bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded hover:bg-red-600 transition cursor-pointer">
									Send Message
								</button>
							</form>
						</div>
					</div>
			<?php }
			} ?>
		</section>
	</main>

	<!-- Notification Container -->
	<div id="notification-container"></div>

	<?php include_once('includes/footer.php'); ?>

	<!-- Script untuk notifikasi -->
	<script>
		function showSuccessNotification(message) {
			createNotification(message, 'success');
		}

		function showErrorNotification(message) {
			createNotification(message, 'error');
		}

		function createNotification(message, type) {
			// Hapus notifikasi yang sudah ada
			const existingNotification = document.querySelector('.notification');
			if (existingNotification) {
				existingNotification.remove();
			}

			// Buat elemen notifikasi baru
			const notification = document.createElement('div');
			notification.className = `notification notification-${type}`;

			// Ikon berdasarkan tipe notifikasi
			const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';

			// Isi HTML notifikasi
			notification.innerHTML = `
				<span class="notification-icon"><i class="fas fa-${icon}"></i></span>
				<span>${message}</span>
				<span class="notification-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></span>
			`;

			// Tambahkan ke container
			document.body.appendChild(notification);

			// Tampilkan notifikasi dengan animasi
			setTimeout(() => {
				notification.classList.add('show');
			}, 10);

			// Hapus notifikasi setelah 5 detik
			setTimeout(() => {
				notification.classList.remove('show');
				setTimeout(() => {
					notification.remove();
				}, 300);
			}, 5000);
		}

		// Fungsi untuk memvalidasi input angka
		function isNumberKey(evt) {
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode > 31 && (charCode < 48 || charCode > 57)) {
				return false;
			}
			return true;
		}
	</script>
</body>

</html>