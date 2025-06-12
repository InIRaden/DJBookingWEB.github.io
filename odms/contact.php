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
		echo "<script>
			document.addEventListener('DOMContentLoaded', function() {
				showSuccessNotification('Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
			});
		</script>";
	} else {
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
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
	<style>
		body {
			font-family: "Poppins", sans-serif;
			background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
		}

		/* Styling untuk notifikasi */
		.notification {
			position: fixed;
			top: 20px;
			right: 20px;
			padding: 15px 20px;
			border-radius: 8px;
			color: white;
			font-size: 14px;
			z-index: 1000;
			display: flex;
			align-items: center;
			box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
			transform: translateX(120%);
			transition: transform 0.4s ease-in-out, background 0.3s ease;
			max-width: 350px;
		}

		.notification.show {
			transform: translateX(0);
		}

		.notification-success {
			background: linear-gradient(135deg, #822b2b, #a52a2a);
			border-left: 6px solid #ff5252;
		}

		.notification-error {
			background: linear-gradient(135deg, #d32f2f, #b71c1c);
			border-left: 6px solid #b71c1c;
		}

		.notification-icon {
			margin-right: 12px;
			font-size: 18px;
		}

		.notification-close {
			margin-left: 12px;
			cursor: pointer;
			opacity: 0.7;
			transition: opacity 0.3s ease;
		}

		.notification-close:hover {
			opacity: 1;
		}
	</style>
</head>

<body class="bg-black text-white">
<header class="relative">
        <?php include_once('includes/header.php'); ?>
        <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center max-w-md px-4">
            <h1 class="text-white font-bold text-lg md:text-xl leading-tight text-shadow">Contact</h1>
            <p class="text-xs md:text-sm mt-2 text-white opacity-90">Learn more about our DJ services and what makes us special</p>
        </div>
    </header>
	<!-- Main Content -->
	<main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto bg-gray-900 rounded-lg shadow-2xl transition-all duration-300 hover:shadow-red-600/30">
		<!-- Breadcrumb -->
		<div class="flex items-center space-x-2 text-xs mb-8 text-gray-400">
			<a href="index.php" class="hover:text-white transition-colors duration-200">Home</a>
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
					<h2 class="font-playfair text-white text-2xl mb-6 text-center tracking-wide">Contact Us</h2>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
						<div>
							<div class="bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
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
							<div class="mt-6 h-[250px] rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
								<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15842.5451974668!2d107.72809224999999!3d-6.9339996500000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68c323777ca3a1%3A0x355eff6734ed9167!2sUniversitas%20Pendidikan%20Indonesia%20(UPI)%20Kampus%20Cibiru!5e0!3m2!1sid!2sid!4v1747864079973!5m2!1sid!2sid"
									class="w-full h-full"
									style="border:0;"
									allowfullscreen=""
									loading="lazy">
								</iframe>
							</div>
						</div>

						<div class="bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
							<h3 class="text-white text-sm font-semibold mb-4">Send Us a Message</h3>
							<p class="text-gray-400 text-xs mb-4">Drop us a message and we'll get back to you soon.</p>

							<form method="post" class="space-y-4">
								<div>
									<label class="block text-gray-400 text-xs mb-1">Name</label>
									<input type="text" name="name" required="true" class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500 transition-colors duration-200">
								</div>

								<div>
									<label class="block text-gray-400 text-xs mb-1">Email</label>
									<input type="email" name="email" required="true" class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500 transition-colors duration-200">
								</div>

								<div>
									<label class="block text-gray-400 text-xs mb-1">Mobile Number</label>
									<input type="text" name="mobnum" required="true" maxlength="10" pattern="[0-9]+"
										class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500 transition-colors duration-200"
										onkeypress="return isNumberKey(event)"
										oninput="this.value = this.value.replace(/[^0-9]/g, '')">
								</div>

								<div>
									<label class="block text-gray-400 text-xs mb-1">Message</label>
									<textarea name="message" required="true" rows="4" class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-xs text-white focus:outline-none focus:border-red-500 transition-colors duration-200"></textarea>
								</div>

								<button type="submit" name="submit" class="bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded-md hover:bg-red-600 transition-all duration-200 cursor-pointer">
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