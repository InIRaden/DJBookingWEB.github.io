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
            background:rgb(30, 41, 57);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .service-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .service-content {
            padding: 20px;
        }
        .book-button {
            background-color: #dc2626;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .book-button:hover {
            background-color: #b91c1c;
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

            if($query->rowCount() > 0) {
                foreach($results as $row) {
                    // Define image based on service name
                    $serviceImage = '';
                    switch(strtolower($row->ServiceName)) {
                        case 'wedding dj':
                            $serviceImage = 'weddingDj.jpg';
                            break;
                        case 'party dj':
                            $serviceImage = 'partyDj.jpg';
                            break;
                        case 'ceremony music':
                            $serviceImage = 'blg2.jpg';
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
                            $serviceImage = 'abt.jpg';
                    }
            ?>
            <div class="service-card">
                <img src="images/<?php echo $serviceImage; ?>" 
                     alt="<?php echo htmlentities($row->ServiceName); ?>" 
                     class="service-image">
                <div class="service-content">
                    <h3 class="text-xl font-semibold mb-2"><?php echo htmlentities($row->ServiceName); ?></h3>
                    <p class="text-gray-400 mb-4"><?php echo htmlentities($row->SerDes); ?></p>
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold">$ <?php echo htmlentities($row->ServicePrice); ?> USD</span>
                        <a href="book-services.php?bookid=<?php echo $row->ID; ?>" 
                           class="book-button">Book Service</a>
                    </div>
                </div>
            </div>
            <?php }} ?>
        </div>
    </main>

    <?php include_once('includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
</body>
</html>