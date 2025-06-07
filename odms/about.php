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

        .photo-card {
            position: relative;
            overflow: hidden;
            border-radius: 0.375rem;
            /* rounded-md */
        }

        .photo-card-image {
            transition: transform 0.3s ease;
            width: 100%;
            height: 120px;
            /* h-[120px] */
            object-fit: cover;
            /* object-cover */
            border-radius: 0.375rem;
            /* rounded-md */
        }

        .photo-card:hover .photo-card-image {
            transform: scale(1.1);
        }

        .photo-card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0));
            padding: 0.5rem;
            /* p-2 */
            transform: translateY(100%);
            transition: transform 0.3s ease;
            color: white;
        }

        .photo-card:hover .photo-card-content {
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
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