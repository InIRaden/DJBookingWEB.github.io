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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancyapps.css" />
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
        }

        .service-card {
            background: linear-gradient(145deg, #1f2937, #111827);
            border-radius: 0.75rem;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            border: 1px solid #374151;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3);
        }

        .service-image-container {
            overflow: hidden;
            border-radius: 0.75rem 0.75rem 0 0;
            position: relative;
        }

        .service-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.4s ease, opacity 0.3s ease;
        }

        .service-card:hover .service-image {
            transform: scale(1.1);
            opacity: 0.9;
        }

        .service-content {
            padding: 1.5rem;
            background: linear-gradient(180deg, #1f2937, #111827);
        }

        .service-title {
            font-family: "Playfair Display", serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #f3f4f6;
            letter-spacing: 0.5px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .service-description {
            color: #d1d5db;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            line-height: 1.7;
            font-weight: 300;
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #374151;
            background: #111827;
        }

        .service-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #dc2626;
            text-shadow: 1px 1px 3px rgba(220, 38, 38, 0.3);
        }

        .book-button {
            background: linear-gradient(90deg, #dc2626, #ef4444);
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .book-button:hover {
            background: linear-gradient(90deg, #b91c1c, #dc2626);
            transform: scale(1.05);
        }

        .book-button .icon {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .book-button:hover .icon {
            transform: translateX(3px);
        }
        
        .search-container {
            margin-bottom: 2rem;
            background: #1e293b;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: none;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 1rem;
            width: 100%;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            background: transparent;
            border: none;
            color: #d1d5db;
            font-size: 0.875rem;
            outline: none;
            transition: box-shadow 0.3s ease;
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        .search-input:focus {
            box-shadow: 0 0 10px rgba(220, 38, 38, 0.6), 0 0 20px rgba(220, 38, 38, 0.4);
        }
        
        .search-button {
            background: linear-gradient(90deg, #dc2626, #ef4444);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .search-button:hover {
            background: linear-gradient(90deg, #b91c1c, #dc2626);
        }
        
        .no-results {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(145deg, #1f2937, #111827);
            border-radius: 0.75rem;
            border: 1px solid #374151;
            color: #9ca3af;
            grid-column: 1 / -1;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .ranking-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(135deg, #facc15, #fbbf24);
            color: #1a202c;
            padding: 6px 12px;
            border-radius: 50%;
            font-family: "Poppins", sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .service-card:hover .ranking-badge {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background: #1f2937;
            margin: 15% auto;
            padding: 20px;
            border-radius: 0.75rem;
            width: 70%;
            max-width: 500px;
            color: #d1d5db;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .close {
            color: #dc2626;
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #b91c1c;
            text-decoration: none;
            cursor: pointer;
        }

        .dj-list {
            list-style: none;
            padding: 0;
        }

        .dj-list li {
            padding: 10px 0;
            border-bottom: 1px solid #374151;
        }

        .dj-list li:last-child {
            border-bottom: none;
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
    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto bg-gray-900 rounded-lg shadow-2xl transition-all duration-300 hover:shadow-red-600/20">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-xs mb-8 text-gray-400">
            <a href="index.php" class="hover:text-white transition-colors duration-200">Home</a>
            <span class="text-gray-600">/</span>
            <span class="text-white">Services</span>
        </div>

        <h2 class="font-semibold text-white text-lg mb-6 text-center">Our Services</h2>
        
        <!-- Search Form -->
        <div class="search-container">
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" placeholder="What do you want to book?" class="search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                <a href="services.php" class="search-button" style="background-color: #4b5563;">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            // Check if search parameter exists
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            
            if (!empty($search)) {
                // SQL query with search filter
                $sql = "SELECT *, RANK() OVER (ORDER BY booking_count DESC) AS ranking FROM (
                SELECT s.ID, s.ServiceName, s.SerDes, s.ServicePrice,
                       COUNT(b.ID) AS booking_count
                FROM tblservice s
                LEFT JOIN tblbooking b ON s.ID = b.ServiceID
                WHERE s.ServiceName LIKE :search OR s.SerDes LIKE :search
                GROUP BY s.ID, s.ServiceName, s.SerDes, s.ServicePrice
                ) AS ranked_services";
                
                $query = $dbh->prepare($sql);
                $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            } else {
                // Original SQL query without search
                $sql = "SELECT *, RANK() OVER (ORDER BY booking_count DESC) AS ranking FROM (
                SELECT s.ID, s.ServiceName, s.SerDes, s.ServicePrice,
                       COUNT(b.ID) AS booking_count
                FROM tblservice s
                LEFT JOIN tblbooking b ON s.ID = b.ServiceID
                GROUP BY s.ID, s.ServiceName, s.SerDes, s.ServicePrice
                ) AS ranked_services
                LIMIT 5";
                
                $query = $dbh->prepare($sql);
            }

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
                    <div class="service-card" onclick="openDJModal('<?php echo strtolower($row->ServiceName); ?>')">
                        <div class="service-image-container relative">
                            <div class="ranking-badge">
                                #<?php echo $row->ranking; ?>
                            </div>
                            <img src="images/<?php echo $serviceImage; ?>"
                                alt="<?php echo htmlentities($row->ServiceName); ?>"
                                class="service-image">
                        </div>
                        <div class="service-content">
                            <h3 class="service-title"><?php echo htmlentities($row->ServiceName); ?></h3>
                            <p class="service-description"><?php echo htmlentities($row->SerDes); ?></p>
                            <div class="service-footer">
                                <span class="service-price">$<?php echo htmlentities($row->ServicePrice); ?></span>
                                <a href="book-services.php?bookid=<?php echo $row->ID; ?>" class="book-button">
                                    Book Now <span class="icon">→</span>
                                </a>
                            </div>
                        </div>
                    </div>
            <?php }
            } else { ?>
                <div class="no-results">
                    <i class="fas fa-search fa-3x mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">No services found</h3>
                    <p>We couldn't find any services matching your search criteria.</p>
                </div>
            <?php } ?>
        </div>
    </main>

    <!-- Modal -->
    <div id="djModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDJModal()">&times;</span>
            <h3 id="modal-title" class="service-title text-center mb-4"></h3>
            <ul id="dj-list" class="dj-list"></ul>
        </div>
    </div>

    <?php include_once('includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancyapps.umd.js"></script>
    <script>
        // Dummy data untuk DJ
        const djData = {
            'wedding dj': [
                'Emily Carter',
                'James Harper',
                'Sophia Bennet',
                'Ethan Reynolds',
                'Olivia Grayson'
            ],
            'party dj': [
                'Mia Sullivian',
                'Liam Parker',
                'Chloe Evans',
                'Noah Mitchell',
                'Isabella Brooks'
            ]
        };

        function openDJModal(service) {
            const modal = document.getElementById('djModal');
            const modalTitle = document.getElementById('modal-title');
            const djList = document.getElementById('dj-list');

            if (djData[service]) {
                modalTitle.textContent = `Available DJs for ${service.replace('dj', 'DJ').replace(/\b\w/g, l => l.toUpperCase())}`;
                djList.innerHTML = djData[service].map(dj => `<li>${dj}</li>`).join('');
                modal.style.display = 'block';
            }
        }

        function closeDJModal() {
            const modal = document.getElementById('djModal');
            modal.style.display = 'none';
        }

        // Menutup modal saat klik di luar konten
        window.onclick = function(event) {
            const modal = document.getElementById('djModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>

</html>