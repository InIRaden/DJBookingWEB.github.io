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
    <title>DjBooking - Services</title>
    <link rel="stylesheet" href="../src/output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
    <style>
        body {
            font-family: "Inter", sans-serif;
        }

        .service-card {
            background-color: #111827;
            /* Dark gray, almost black */
            border-radius: 0.75rem;
            /* rounded-xl */
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            /* For pseudo-elements if needed */
            border: 1px solid #374151;
            /* Slightly lighter border */
        }

        .service-card:hover {
            transform: translateY(0.2px);
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3);
            /* Red glow effect */
        }

        .service-image-container {
            overflow: hidden;
            /* Ensures the image scaling stays within bounds */
            border-radius: 0.75rem 0.75rem 0 0;
            /* Match card rounding */
        }

        .service-image {
            width: 100%;
            height: 220px;
            /* Increased height */
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .service-card:hover .service-image {
            transform: scale(1.1);
        }

        .service-content {
            padding: 1.5rem;
            /* p-6 */
        }

        .service-title {
            font-size: 1.25rem;
            /* text-xl */
            font-weight: 600;
            /* font-semibold */
            margin-bottom: 0.75rem;
            /* mb-3 */
            color: #f3f4f6;
            /* Lighter text color */
        }

        .service-description {
            color: #9ca3af;
            /* text-gray-400 */
            margin-bottom: 1.25rem;
            /* mb-5 */
            font-size: 0.875rem;
            /* text-sm */
            line-height: 1.6;
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            /* pt-4 */
            border-top: 1px solid #374151;
            /* Separator line */
        }

        .service-price {
            font-size: 1.5rem;
            /* text-2xl */
            font-weight: 700;
            /* font-bold */
            color: #dc2626;
            /* Red color for price */
        }

        .book-button {
            background-color: #dc2626;
            /* bg-red-600 */
            color: white;
            padding: 0.625rem 1.25rem;
            /* py-2.5 px-5 */
            border-radius: 0.5rem;
            /* rounded-lg */
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 500;
            /* font-medium */
            display: inline-flex;
            align-items: center;
        }

        .book-button:hover {
            background-color: #b91c1c;
            /* hover:bg-red-700 */
            transform: scale(1.05);
        }

        .book-button .icon {
            margin-left: 0.5rem;
            /* ml-2 */
            transition: transform 0.3s ease;
        }

        .book-button:hover .icon {
            transform: translateX(3px);
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->

    <header class="relative">
        <?php include_once('includes/header.php'); ?>
        <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center max-w-md px-4">
            <h1 class="text-white font-bold text-lg md:text-xl leading-tight">Services</h1>
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
            <span class="text-white">Services</span>
        </div>

        <h2 class="font-semibold text-white text-lg mb-6">Our Services</h2>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $sql = "SELECT * from tblservice";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
                foreach ($results as $row) {
                    // Define image based on service name
                    $serviceImage = '';
                    switch (strtolower($row->ServiceName)) {
                        case 'wedding dj':
                            $serviceImage = 'weddingDj.jpg';
                            break;
                        case 'party dj':
                            $serviceImage = 'partyDj.jpg';
                            break;
                        case 'ceremony music':
                            $serviceImage = 'blg2.jpg'; // Anda mungkin ingin mengganti ini dengan gambar yang lebih relevan
                            break;
                        case 'photo booth hire':
                            $serviceImage = 'photobooth.jpg';
                            break;
                        case 'karaoke add-on':
                            $serviceImage = 'karaoke.jpg';
                            break;
                        case 'uplighters':
                            $serviceImage = 'uplighters.jpg';
                            break;
                        default:
                            $serviceImage = 'abt.jpg'; // Gambar default
                    }
            ?>
                    <div class="service-card">
                        <div class="service-image-container">
                            <img src="images/<?php echo $serviceImage; ?>"
                                alt="<?php echo htmlentities($row->ServiceName); ?>"
                                class="service-image">
                        </div>
                        <div class="service-content">
                            <h3 class="service-title"><?php echo htmlentities($row->ServiceName); ?></h3>
                            <p class="service-description"><?php echo htmlentities($row->SerDes); ?></p>
                            <div class="service-footer">
                                <span class="service-price">$<?php echo htmlentities($row->ServicePrice); ?></span>
                                <a href="book-services.php?bookid=<?php echo $row->ID; ?>"
                                    class="book-button">
                                    Book Now <span class="icon">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
            <?php }
            } ?>
        </div>
    </main>

    <?php include_once('includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
</body>

</html>