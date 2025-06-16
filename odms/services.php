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
            background: linear-gradient(135deg, #1a1a1a, #0d0d0d); /* Match body background */
            border-radius: 0.75rem;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            position: relative;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            border: none; /* Remove border by default */
        }

        .service-card:hover, .service-card:active {
            background: linear-gradient(145deg, #1f2937, #111827); /* Change on hover/click */
            border: 1px solid #374151; /* Add border on interaction */
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
            background: transparent; /* Match card background */
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

        .dj-names {
            display: none; /* Hidden by default */
            color: #f3f4f6;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0));
            transform: translateY(100%); /* Start from below the image */
            transition: transform 0.3s ease;
        }

        .service-card:hover .dj-names, .service-card:active .dj-names {
            display: block; /* Show on hover/click */
            transform: translateY(0); /* Slide up to visible position */
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #374151;
            background: linear-gradient(135deg, #1a1a1a, #0d0d0d); /* Match with .service-card background */
            transition: background 0.3s ease;
        }

        .service-card:hover .service-footer, .service-card:active .service-footer {
            background: linear-gradient(145deg, #1f2937, #111827); /* Match hover state of .service-card */
        }

        .service-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #dc2626;
            text-shadow: 1px 1px 3px rgba(220, 38, 38, 0.3);
            border: none;
            padding: 0.25rem 0.5rem;
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
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: none; /* Remove border by default */
            background: linear-gradient(135deg, #1a1a1a, #0d0d0d); /* Match body background */
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 1rem;
            width: 100%;
        }        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #d1d5db;
            font-size: 0.875rem;
            outline: none;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        .search-input:focus {
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .search-button {
            background: linear-gradient(90deg, #dc2626, #ef4444);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-button.cancel-button {
            background: transparent;
            border: 1px solid #dc2626;
            color: #dc2626;
            transition: all 0.3s ease;
        }

        .search-button.cancel-button:hover {
            background: rgba(220, 38, 38, 0.1);
        }
        
        .search-button:hover {
            background: linear-gradient(90deg, #b91c1c, #dc2626);
        }
        
        .no-results {
            text-align: center;
            padding: 2rem;
            background: #121212;
            border-radius: 0.75rem;
            border: 0.8px solid #212121;
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

        /* Tambahan untuk breadcrumb */
        .breadcrumb {
            margin-top: 40px; /* Jarak dari atas ditingkatkan menjadi 40px untuk lebih rendah */
            padding: 10px 0;
        }
    </style>
</head>

<body class="bg-black text-white">
    <!-- Header Section -->
    <header class="relative">
        <?php include_once('includes/header.php'); ?>
    </header>

    <!-- Main Content -->
    <main class="px-6 md:px-16 lg:px-24 xl:px-32 py-10 max-w-[1280px] mx-auto bg-[#121212] rounded-lg shadow-2xl transition-all duration-300 hover:shadow-red-600/20">
        <!-- Breadcrumb -->
        <div class="breadcrumb flex items-center space-x-2 text-xs mb-8 text-gray-400">
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
                <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>                <a href="services.php" class="search-button cancel-button">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $isLoggedIn = isset($_SESSION['odmsaid']);
            if (!empty($search)) {
                $sql = "SELECT *, RANK() OVER (ORDER BY booking_count DESC) AS ranking FROM (\n                SELECT s.ID, s.ServiceName, s.SerDes, s.ServicePrice,\n                       COUNT(b.ID) AS booking_count\n                FROM tblservice s\n                LEFT JOIN tblbooking b ON s.ID = b.ServiceID\n                WHERE s.ServiceName LIKE :search OR s.SerDes LIKE :search\n                GROUP BY s.ID, s.ServiceName, s.SerDes, s.ServicePrice\n                ) AS ranked_services";
                
                $query = $dbh->prepare($sql);
                $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            } else {
                $sql = "SELECT *, RANK() OVER (ORDER BY booking_count DESC) AS ranking FROM (\n                SELECT s.ID, s.ServiceName, s.SerDes, s.ServicePrice,\n                       COUNT(b.ID) AS booking_count\n                FROM tblservice s\n                LEFT JOIN tblbooking b ON s.ID = b.ServiceID\n                GROUP BY s.ID, s.ServiceName, s.SerDes, s.ServicePrice\n                ) AS ranked_services\n                LIMIT 5";
                
                $query = $dbh->prepare($sql);
            }

            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
                foreach ($results as $row) {
                    $serviceImage = '';
                    $djNames = '';
                    switch (strtolower($row->ServiceName)) {
                        case 'wedding dj':
                            $serviceImage = 'weddingDj.jpg';
                            $djNames = 'Emily Carter, James Harper, Sophia Bennet';
                            break;
                        case 'party dj':
                            $serviceImage = 'partyDj.jpg';
                            $djNames = 'Mia Sullivan, Liam Parker, Chloe Evans';
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
                    <div class="service-card" onclick="this.classList.toggle('active')">
                        <div class="service-image-container relative">
                            <div class="ranking-badge">
                                #<?php echo $row->ranking; ?>
                            </div>
                            <img src="images/<?php echo $serviceImage; ?>"
                                alt="<?php echo htmlentities($row->ServiceName); ?>"
                                class="service-image">
                            <?php if (!empty($djNames)): ?>
                                <div class="dj-names"><?php echo $djNames; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="service-content">
                            <h3 class="service-title"><?php echo htmlentities($row->ServiceName); ?></h3>
                            <p class="service-description"><?php echo htmlentities($row->SerDes); ?></p>
                            <div class="service-footer">
                                <span class="service-price">$<?php echo htmlentities($row->ServicePrice); ?></span>
                                <?php if ($isLoggedIn): ?>
                                    <a href="book-services.php?bookid=<?php echo $row->ID; ?>" class="book-button">Book Now <span class="icon">→</span></a>
                                <?php else: ?>
                                    <button type="button" class="book-button book-now-guest" data-service="<?php echo $row->ID; ?>">Book Now <span class="icon">→</span></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else { ?>
                <div class="no-results">
                    <i class="fas fa-search fa-2x mb-4"></i>
                    <h3 class="text-lg font-semibold mb-2">No services found</h3>
                    <p>We couldn't find any services matching your search criteria.</p>
                </div>
            <?php } ?>
        </div>
    </main>

    <!-- Login/Register Modal for Guest -->
    <div id="loginModal" class="modal-login">
        <div class="modal-login-content modal-login-content-wide">
            <span class="close-login-modal" id="closeLoginModal">&times;</span>
            <div class="modal-modal-body">
                <h2 class="modal-title">Login is Required</h2>
                <p class="modal-desc">To book a service, please sign in or create an account first.</p>
                <div class="modal-btn-group-col">
                    <a href="signin.php" class="modal-btn modal-btn-signin">Sign In</a>
                    <div class="modal-or-separator"><span>Or</span></div>
                    <a href="signup.php" class="modal-btn modal-btn-signup">Sign Up</a>
                </div>
            </div>
        </div>
    </div>

    <?php include_once('includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancyapps.umd.js"></script>
    <script>
        // Modal logic for guest booking
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('loginModal');
            var closeBtn = document.getElementById('closeLoginModal');
            var bookBtns = document.querySelectorAll('.book-now-guest');
            bookBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    modal.style.display = 'flex';
                });
            });
            closeBtn.onclick = function() {
                modal.style.display = 'none';
            };
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            };
        });
    </script>
    <style>
        .modal-login {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(18,18,18,0.92);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(2px);
        }
        .modal-login-content {
            background: linear-gradient(135deg, #212121 80%, #121212 100%);
            border-radius: 1.5rem;
            padding: 2.5rem 2.5rem 2.5rem 2.5rem;
            box-shadow: 0 12px 48px 0 rgba(0,0,0,0.7), 0 1.5px 8px 0 #53535344;
            min-width: 400px;
            max-width: 98vw;
            width: 480px;
            min-height: 320px;
            text-align: left;
            position: relative;
            animation: fadeInModal 0.35s cubic-bezier(.4,2,.6,1);
            border: 1.5px solid #353535;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        @keyframes fadeInModal {
            from { opacity: 0; transform: scale(0.92); }
            to { opacity: 1; transform: scale(1); }
        }
        .close-login-modal {
            position: absolute;
            top: 22px;
            right: 32px;
            font-size: 2rem;
            color: #fff;
            cursor: pointer;
            transition: color 0.2s, transform 0.2s;
            z-index: 2;
        }
        .close-login-modal:hover {
            color: #dc2626;
            transform: scale(1.15) rotate(90deg);
        }
        .modal-modal-body {
            width: 100%;
        }
        .modal-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.3rem;
            letter-spacing: 0.1px;
            text-align: left;
        }
        .modal-desc {
            color: #bdbdbd;
            font-size: 0.98rem;
            margin-bottom: 2.1rem;
            font-weight: 400;
            text-align: left;
        }
        .modal-btn-group-col {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
            align-items: stretch;
        }
        .modal-btn {
            padding: 0.95rem 0;
            border-radius: 0.7rem;
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s, transform 0.18s, border 0.18s;
            box-shadow: 0 2px 12px 0 rgba(33,33,33,0.10);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            border: none;
            outline: none;
            letter-spacing: 0.1px;
        }
        .modal-btn-signin {
            background: #fff;
            color: #232323;
            border: 1.5px solid #353535;
        }
        .modal-btn-signin:hover {
            background: #ededed;
            color: #181818;
            box-shadow: 0 4px 18px 0 #53535333;
            transform: translateY(-1px) scale(1.02);
        }
        .modal-btn-signup {
            background: transparent;
            color: #fff;
            border: 1.5px solid #353535;
        }
        .modal-btn-signup:hover {
            background: #232323;
            color: #fff;
            box-shadow: 0 4px 18px 0 #53535333;
            transform: translateY(-1px) scale(1.02);
        }
        .modal-btn i {
            font-size: 1.2em;
            margin-right: 0.5em;
        }
        .modal-login-content-wide {
            width: 540px;
            min-width: 420px;
            max-width: 99vw;
        }
        .modal-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.25rem;
            letter-spacing: 0.1px;
            text-align: left;
        }
        .modal-desc {
            color: #bdbdbd;
            font-size: 0.93rem;
            margin-bottom: 1.7rem;
            font-weight: 400;
            text-align: left;
        }
        .modal-btn {
            font-size: 0.98rem;
        }
        .modal-or-separator {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin: 0.5rem 0 0.5rem 0;
            color: #535353;
            font-size: 0.93rem;
            font-weight: 500;
            letter-spacing: 0.1em;
        }
        .modal-or-separator span {
            padding: 0 1.1em;
            color: #b3b3b3;
            font-size: 0.93rem;
        }
      .modal-or-separator:before,
.modal-or-separator:after {
    content: '';
    flex: 1;
    border-bottom: 1.2px solid rgba(179, 179, 180, 0.3); /* transparan */
    margin: 0 0.2em;
}

        @media (max-width: 600px) {
            .modal-login-content {
                min-width: 90vw;
                width: 99vw;
                padding: 1.2rem 0.5rem 1.2rem 0.5rem;
            }
            .modal-title {
                font-size: 1.05rem;
            }
        }
    </style>
    <script>
        // ...existing code...
    </script>
</body>

</html>