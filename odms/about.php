<?php
session_start();
error_reporting(0);

include('includes/dbconnection.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>DjBooking - About Us</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <!-- Tambahkan CSS Fancybox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
    <style>
        body {
            font-family: "Inter", sans-serif;
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
        <nav class="absolute top-0 left-0 w-full flex items-center justify-between px-6 py-4 text-white bg-black">
            <div class="flex items-center space-x-2 text-sm font-semibold">
                <i class="fas fa-compact-disc"></i>
                <span>DjBooking</span>
            </div>
            <ul class="hidden md:flex space-x-8 text-sm font-normal">
                <li><a class="hover:underline" href="index.php">Home</a></li>
                <li><a class="hover:underline" href="services.php">Services</a></li>
                <li><a class="hover:underline" href="request-status.php">Request Status</a></li>
                <li><a class="hover:underline" href="about.php">About</a></li>
                <li><a class="hover:underline" href="contact.php">Contact</a></li>
                <li><a class="hover:underline" href="admin/login.php">Admin</a></li>
            </ul>
            <a href="signup.php" class="hidden md:inline-block bg-gray-700 bg-opacity-60 rounded px-3 py-1 text-xs font-semibold hover:bg-gray-600 transition">
                Sign Up
            </a>
        </nav>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center max-w-md px-4">
            <h1 class="text-white font-bold text-lg md:text-xl leading-tight">About Us</h1>
            <p class="text-xs md:text-sm mt-2 text-white">
                Learn more about our DJ services and what makes us special
            </p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-xs mb-8">
            <a href="index.php" class="text-gray-400 hover:text-white">Home</a>
            <span class="text-gray-600">/</span>
            <span class="text-white">About</span>
        </div>

        <!-- About Content -->
        <section class="mb-12">
            <?php
            $sql = "SELECT * from tblpage where PageType='aboutus'";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
                foreach ($results as $row) { ?>
                    <h2 class="font-semibold text-white text-lg mb-6"><?php echo htmlentities($row->PageTitle); ?></h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <a href="images/abt.jpg" data-fancybox="about-gallery">
                                <img src="images/abt.jpg" alt="About Us" class="w-full h-auto rounded-md shadow-lg" />
                            </a>
                        </div>
                        <div class="text-gray-300 text-sm leading-relaxed">
                            <p><?php echo $row->PageDescription; ?></p>
                        </div>
                    </div>
            <?php }
            } ?>
        </section>

        <!-- Latest Photos Section -->
        <section>
            <h2 class="font-semibold text-white text-lg mb-6">LATEST PHOTOS</h2>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="group">
                    <a href="images/4.jpg" data-fancybox="gallery" class="block">
                        <img src="images/4.jpg" alt="DJ Event" class="w-full h-[120px] object-cover rounded-md transition duration-300 group-hover:opacity-80" />
                    </a>
                    <h3 class="mt-2 text-xs font-bold text-white">Aenean rutrum</h3>
                    <p class="text-[9px] text-gray-400">Suspendisse posuere enim eu ante</p>
                </div>
                <div class="group">
                    <a href="images/5.jpg" data-fancybox="gallery" class="block">
                        <img src="images/5.jpg" alt="DJ Event" class="w-full h-[120px] object-cover rounded-md transition duration-300 group-hover:opacity-80" />
                    </a>
                    <h3 class="mt-2 text-xs font-bold text-white">Aenean rutrum</h3>
                    <p class="text-[9px] text-gray-400">Suspendisse posuere enim eu ante</p>
                </div>
                <div class="group">
                    <a href="images/6.jpg" data-fancybox="gallery" class="block">
                        <img src="images/6.jpg" alt="DJ Event" class="w-full h-[120px] object-cover rounded-md transition duration-300 group-hover:opacity-80" />
                    </a>
                    <h3 class="mt-2 text-xs font-bold text-white">Aenean rutrum</h3>
                    <p class="text-[9px] text-gray-400">Suspendisse posuere enim eu ante</p>
                </div>
                <div class="group">
                    <a href="images/7.jpg" data-fancybox="gallery" class="block">
                        <img src="images/7.jpg" alt="DJ Event" class="w-full h-[120px] object-cover rounded-md transition duration-300 group-hover:opacity-80" />
                    </a>
                    <h3 class="mt-2 text-xs font-bold text-white">Aenean rutrum</h3>
                    <p class="text-[9px] text-gray-400">Suspendisse posuere enim eu ante</p>
                </div>
                <div class="group">
                    <a href="images/paham.png" data-fancybox="gallery" class="block">
                        <img src="images/paham.png" alt="DJ Event" class="w-full h-[120px] object-cover rounded-md transition duration-300 group-hover:opacity-80" />
                    </a>
                    <h3 class="mt-2 text-xs font-bold text-white">Aenean rutrum</h3>
                    <p class="text-[9px] text-gray-400">Suspendisse posuere enim eu ante</p>
                </div>
            </div>
        </section>
    </main>

    <?php include_once('includes/footer.php'); ?>

    <!-- Tambahkan script Fancybox -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Fancybox
            Fancybox.bind("[data-fancybox]", {
                // Opsi konfigurasi Fancybox
                animationEffect: "fade",
                transitionEffect: "fade",
                buttons: [
                    "zoom",
                    "slideShow",
                    "fullScreen",
                    "close"
                ]
            });
        });
    </script>
</body>

</html>