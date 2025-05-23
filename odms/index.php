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
    <title>DjBooking</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: "Inter", sans-serif;
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
    <header class="relative">
    <?php include_once('includes/header.php');?>
        <img alt="DJ wearing headphones with raised hands" class="w-full h-[500px] object-cover" src="images/homepage.jpg" />
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
            <h1 class="text-white font-bold text-lg md:text-xl leading-tight">Make Your Event Unforgettable</h1>
            <p class="text-xs md:text-sm mt-2 text-white">
                Customize your event with the right DJ. Explore styles, check availability, and book in just a few clicks.
            </p>
            <a href="services.php" class="mt-3 inline-block bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded hover:bg-red-600 transition">
                Buy Ticket Now
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto">
        <!-- Featured DJs Section -->
        <section class="mb-12">
            <h2 class="font-semibold text-white text-sm mb-6">Featured DJs</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php
                $featured_djs = [
                    ['name' => 'DJ Alex', 'events' => '246 events', 'image' => 'images/7.jpg'],
                    ['name' => 'DJ Sarah', 'events' => '178 events', 'image' => 'images/2.jpg'],
                    ['name' => 'DJ Mike', 'events' => '480 events', 'image' => 'images/10.jpg'],
                    ['name' => 'DJ Lisa', 'events' => '320 events', 'image' => 'images/5.jpg']
                ];

                foreach ($featured_djs as $dj) { ?>
                    <div>
                        <img alt="<?php echo $dj['name']; ?>" class="rounded-md w-full h-[120px] object-cover" src="<?php echo $dj['image']; ?>" />
                        <p class="mt-2 text-xs font-bold text-white"><?php echo $dj['name']; ?></p>
                        <p class="text-[9px] text-gray-300"><?php echo $dj['events']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </section>

        <!-- Latest Events Section -->
        <section>
            <h2 class="font-semibold text-white text-sm mb-6">Latest Events</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <?php
                $latest_events = [
                    [
                        'title' => 'Summer Beach Party 2024',
                        'description' => 'Experience the ultimate beach party with top DJs and amazing vibes...'
                    ],
                    [
                        'title' => 'Electronic Music Festival',
                        'description' => 'Join us for the biggest electronic music festival of the year...'
                    ],
                    [
                        'title' => 'Club Night Special',
                        'description' => 'Special performance by international guest DJs...'
                    ]
                ];

                foreach ($latest_events as $event) { ?>
                    <article class="bg-gray-800 rounded-md p-4 text-[10px] sm:text-xs leading-tight">
                        <h3 class="font-bold text-white mb-2"><?php echo $event['title']; ?></h3>
                        <p class="text-gray-400"><?php echo $event['description']; ?></p>
                    </article>
                <?php } ?>
            </div>
        </section>
    </main>

    <?php include_once('includes/footer.php'); ?>
</body>

</html>