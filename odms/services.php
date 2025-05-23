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
        
        <!-- Services List -->
        <div class="space-y-4">
            <?php
            $sql="SELECT * from tblservice";
            $query = $dbh -> prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);

            $cnt=1;
            if($query->rowCount() > 0)
            {
            foreach($results as $row)
            {               ?>
                <div class="bg-gray-800 rounded-md p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <h3 class="text-white font-semibold"><?php echo htmlentities($row->ServiceName);?></h3>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-300 text-sm"><?php echo htmlentities($row->SerDes);?></p>
                    </div>
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-white font-bold mb-2 md:mb-0">$ <?php echo htmlentities($row->ServicePrice);?> USD</p>
                        <a href="book-services.php?bookid=<?php echo $row->ID;?>" class="bg-red-700 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition">Book Services</a>
                    </div>
                </div>
            <?php $cnt=$cnt+1;}} ?>
        </div>
    </main>

    <?php include_once('includes/footer.php');?>

    <!-- Script Fancybox jika diperlukan -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
</body>
</html>