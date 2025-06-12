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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
    <!-- Tambahkan CSS Fancybox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
        }

        .photo-card {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .photo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        }

        .photo-card-image {
            transition: transform 0.3s ease;
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .photo-card:hover .photo-card-image {
            transform: scale(1.1);
        }

        .photo-card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0));
            padding: 0.75rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            color: white;
            border-radius: 0 0 0.5rem 0.5rem;
        }

        .photo-card:hover .photo-card-content {
            transform: translateY(0);
        }

        .photo-card-content h3 {
            font-family: "Playfair Display", serif;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            letter-spacing: 0.5px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .photo-card-content p {
            font-size: 0.7rem;
            line-height: 1.4;
            color: #d1d5db;
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center max-w-md px-4">
            <h1 class="text-white font-bold text-lg md:text-xl leading-tight text-shadow">About Us</h1>
            <p class="text-xs md:text-sm mt-2 text-white opacity-90">Learn more about our DJ services and what makes us special</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto bg-gray-900 rounded-lg shadow-2xl transition-all duration-300 hover:shadow-red-600/30">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-xs mb-8 text-gray-400">
            <a href="index.php" class="hover:text-white transition-colors duration-200">Home</a>
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
                    <h2 class="font-playfair text-white text-2xl mb-6 text-center tracking-wide">About Us</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <a href="images/abt.jpg" data-fancybox="about-gallery">
                                <img src="images/abt.jpg" alt="About Us" class="w-full h-auto rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300" />
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
            <h2 class="font-playfair text-white text-2xl mb-6 text-center tracking-wide">Latest Photos</h2>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <?php
                $team_members = [
                    [
                        'name' => 'Bagas',
                        'role' => 'Team Leader',
                        'image' => './images/bagasTutut.jpg',
                        'description' => 'Halo, nama aku Bagas, mahasiswa RPL angkatan 2023, berperan sebagai team leader dalam proyek ini.'
                    ],
                    [
                        'name' => 'Rahma Dina',
                        'role' => 'Frontend Developer',
                        'image' => './images/ama.jpg',
                        'description' => 'Saya Rahma Dina, bertanggung jawab untuk tampilan antarmuka pengguna yang menarik dan responsif.'
                    ],
                    [
                        'name' => 'Arul',
                        'role' => 'Backend Developer',
                        'image' => './images/arulGenteng.jpg',
                        'description' => 'Perkenalkan, Arul. Saya fokus pada pengembangan sisi server dan logika aplikasi.'
                    ],
                    [
                        'name' => 'Rifiani',
                        'role' => 'UI/UX Designer',
                        'image' => './images/piaMbokMbokbree.jpg',
                        'description' => 'Hai, saya Rifiani. Saya merancang pengalaman pengguna yang intuitif dan estetis untuk aplikasi ini.'
                    ],
                    [
                        'name' => 'Raden Mahesa',
                        'role' => 'Database Specialist',
                        'image' => 'images/mahesaa.jpg',
                        'description' => 'Insyaallah pekerjaan saya, memastikan data tersimpan dengan aman dan efisien.'
                    ]
                ];

                foreach ($team_members as $member) { ?>
                    <div class="photo-card group">
                        <a href="<?php echo $member['image']; ?>" data-fancybox="gallery" class="block">
                            <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="photo-card-image" />
                        </a>
                        <div class="photo-card-content">
                            <h3 class="text-xs font-bold"><?php echo $member['name']; ?></h3>
                            <p class="text-[9px] text-gray-300 mb-1"><?php echo $member['role']; ?></p>
                            <p class="text-[9px]"><?php echo $member['description']; ?></p>
                        </div>
                    </div>
                <?php } ?>
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