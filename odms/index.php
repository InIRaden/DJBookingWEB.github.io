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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --heading-font: 'Poppins', sans-serif;
            --body-font: 'Montserrat', sans-serif;
        }
        
        body {
            font-family: var(--body-font);
            letter-spacing: 0.015em;
        }
        
        h1, h2, h3, h4, h5, h6, .heading-text {
            font-family: var(--heading-font);
            letter-spacing: -0.02em;
        }

        /* Card hover effects for DJs */
        .dj-card {
            position: relative;
            overflow: hidden;
            border-radius: 0.375rem;
            transition: transform 0.3s ease;
        }

        .dj-card:hover {
            transform: translateY(-5px);
        }

        .dj-card-image {
            transition: transform 0.5s ease;
        }

        .dj-card:hover .dj-card-image {
            transform: scale(1.1);
        }

        .dj-card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0));
            padding: 1rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .dj-card:hover .dj-card-content {
            transform: translateY(0);
        }

        /* Event card hover effects */
        .event-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }

        .event-card::after {
            content: "Book Now →";
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            color: #ff3333;
            font-weight: bold;
            opacity: 0;
            transition: opacity 0.3s ease;
            font-family: var(--heading-font);
        }

        .event-card:hover::after {
            opacity: 1;
        }
        
        /* Perbaikan ukuran font dan spacing */
        .hero-content h1 {
            font-weight: 800;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            line-height: 1.1;
        }
        
        .hero-content p {
            font-weight: 400;
            line-height: 1.6;
            letter-spacing: 0.01em;
        }
        
        .hero-content a {
            font-family: var(--heading-font);
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        
        section h2 {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.9rem;
        }
        
        .dj-card p {
            font-weight: 500;
        }
        
        .dj-card-content h3 {
            font-weight: 700;
        }
        
        .event-card h3 {
            font-family: var(--heading-font);
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        .event-card p {
            font-size: 0.8rem;
            line-height: 1.5;
        }
        
        .faq-button span {
            font-family: var(--heading-font);
            font-weight: 600;
        }
        
        .faq-answer p, .faq-answer li {
            font-family: var(--body-font);
            line-height: 1.6;
        }
    </style>
</head>

<body class="bg-black text-white font-sans">
    <!-- Header Section -->
    <header class="relative text-center">
        <?php include_once('includes/header.php'); ?>
        <img alt="DJ wearing headphones with raised hands" class="w-full h-[500px] object-cover" src="images/homepage.jpg" />

        <div class="hero-content absolute top-1/2 left-1/2 -translate-x-[50%] -translate-y-[40%] text-center max-w-xl px-4">
            <h1 class="text-white font-bold text-3xl md:text-6xl leading-tight">Make Your Event Unforgettable</h1>
            <p class="text-sm md:text-base mt-3 text-white">
                Customize your event with the right DJ. Explore styles, check availability, and book in just a few clicks.
            </p>
            <a href="services.php" class="mt-4 inline-block bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded hover:bg-red-600 transition">
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
                // SQL dengan RANK() untuk memberi peringkat berdasarkan booking_count
                $sql = "SELECT
                            s.ID,
                            s.ServiceName,
                            s.SerDes,
                            s.ServicePrice,
                        COUNT(b.ID) AS booking_count,
                        RANK() OVER (ORDER BY COUNT(b.ID) DESC) AS ranking
                        FROM tblservice s
                        LEFT JOIN tblbooking b ON s.ID = b.ServiceID
                        GROUP BY s.ID, s.ServiceName, s.SerDes, s.ServicePrice
                        ORDER BY booking_count DESC
                        LIMIT 4";
                $query = $dbh->prepare($sql);
                $query->execute();
                $featured_djs = $query->fetchAll(PDO::FETCH_OBJ);

                foreach ($featured_djs as $dj) {
                    // Tentukan gambar berdasarkan nama service
                    $djImage = '';
                    switch (strtolower($dj->ServiceName)) {
                        case 'wedding dj':
                            $djImage = 'images/weddingDj.jpg';
                            break;
                        case 'party dj':
                            $djImage = 'images/partyDj.jpg';
                            break;
                        case 'ceremony music':
                            $djImage = 'images/blg2.jpg';
                            break;
                        case 'photo booth hire':
                            $djImage = 'images/photobooth.jpg';
                            break;
                        case 'karaoke add-on':
                            $djImage = 'images/karaoke.jpg';
                            break;
                        case 'uplighters':
                            $djImage = 'images/uplighters.jpg';
                            break;
                        default:
                            $djImage = 'images/abt.jpg';
                    }
                ?>
                    <div class="dj-card">
                        <div class="overflow-hidden rounded-md">
                            <img alt="<?php echo htmlentities($dj->ServiceName); ?>" class="dj-card-image rounded-md w-full h-[150px] object-cover" src="<?php echo $djImage; ?>" />
                        </div>
                        <p class="mt-2 text-xs font-bold text-white"><?php echo htmlentities($dj->ServiceName); ?></p>
                        <div class="dj-card-content">
                            <h3 class="text-sm font-bold text-white"><?php echo htmlentities($dj->ServiceName); ?></h3>
                            <p class="text-[10px] text-gray-300 mb-1">
                                Rank #<?php echo $dj->ranking; ?> — <?php echo $dj->booking_count . ' events'; ?>
                            </p>
                            <p class="text-[10px] text-white"><?php echo htmlentities($dj->SerDes); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>


        <!-- Hero Banner Section -->
        <section class="py-10 bg-black text-white">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <h2 class="text-center text-5xl sm:text-6xl font-bold leading-tight">
                Book Top DJs for Any Event <br />
                Effortlessly with <br />
                    our <span class="text-red-800">APPS!</span>
                </h2>
                <p class="mt-12 mx-auto max-w-md text-sm text-gray-300">
                    This app, developed by students of Universitas Pendidikan Indonesia, is made for all DJ music enthusiasts to make booking a DJ for your special event easier than ever
                </p>
                <div class="flex items-center space-x-2 mt-4 mx-auto max-w-xs justify-center">
                    <div class="bg-red-600 rounded-full h-3 w-24"></div>
                    <div class="bg-red-600 rounded-full h-3 w-4"></div>
                </div>
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
                        'description' => 'Experience the ultimate beach party with top DJs and amazing vibes. Dance under the stars with the sound of waves in the background.'
                    ],
                    [
                        'title' => 'Electronic Music Festival',
                        'description' => 'Join us for the biggest electronic music festival of the year featuring international artists and state-of-the-art sound systems.'
                    ],
                    [
                        'title' => 'Club Night Special',
                        'description' => 'Special performance by international guest DJs at our premium venue with exclusive VIP areas and signature cocktails.'
                    ]
                ];

                foreach ($latest_events as $event) { ?>
                    <a href="book-services.php" class="block">
                        <article class="event-card bg-gray-800 rounded-md p-4 text-[10px] sm:text-xs leading-tight h-full">
                            <h3 class="font-bold text-white mb-2"><?php echo $event['title']; ?></h3>
                            <p class="text-gray-400"><?php echo $event['description']; ?></p>
                        </article>
                    </a>
                <?php } ?>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="bg-black text-white py-20">
            <div class="max-w-4xl mx-auto px-6">
                <h2 class="text-center text-4xl font-medium mb-12 text-gray-100">Frequently Asked Questions</h2>
                <div class="space-y-4" id="faq-container">
                    <div class="faq-item bg-[#1a1a1a] rounded-lg overflow-hidden transition-all duration-300 ease-in-out hover:bg-[#2a2a2a] shadow-lg shadow-black/50">
                        <button class="faq-button w-full text-left px-6 py-4 flex justify-between items-center text-base font-medium text-gray-100 focus:outline-none">
                            <span>How do I book a DJ for my event?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 py-4 text-gray-400 border-t border-gray-800 bg-[#222222]">
                            <p>Booking a DJ is very easy! Just follow these steps :</p>
                            <ul class="list-disc pl-5 mt-2 space-y-1 text-sm">
                                <li>Choose your event type</li>
                                <li>Select an available DJ</li>
                                <li>Choose the date and time</li>
                                <li>Fill in the booking details</li>
                                <li>Complete the payment</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Bagian FAQ lainnya tetap sama -->
                    <div class="faq-item bg-[#1a1a1a] rounded-lg overflow-hidden transition-all duration-300 ease-in-out hover:bg-[#2a2a2a] shadow-lg shadow-black/50">
                        <button class="faq-button w-full text-left px-6 py-4 flex justify-between items-center text-base font-medium text-gray-100 focus:outline-none">
                            <span>How far in advance should I book a DJ?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 py-4 text-gray-400 border-t border-gray-800 bg-[#222222]">
                            <p class="text-sm">We recommend booking at least 2 weeks before your event to ensure DJ availability. For larger events, it's best to book 1-2 months in advance.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-[#1a1a1a] rounded-lg overflow-hidden transition-all duration-300 ease-in-out hover:bg-[#2a2a2a] shadow-lg shadow-black/50">
                        <button class="faq-button w-full text-left px-6 py-4 flex justify-between items-center text-base font-medium text-gray-100 focus:outline-none">
                            <span>Can I request specific songs?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 py-4 text-gray-400 border-t border-gray-800 bg-[#222222]">
                            <p class="text-sm">Yes! You can submit a list of desired songs when making your booking. Our DJs will accommodate song requests according to the genre and music style suitable for your event.</p>
                        </div>
                    </div>

                    <div class="faq-item bg-[#1a1a1a] rounded-lg overflow-hidden transition-all duration-300 ease-in-out hover:bg-[#2a2a2a] shadow-lg shadow-black/50">
                        <button class="faq-button w-full text-left px-6 py-4 flex justify-between items-center text-base font-medium text-gray-100 focus:outline-none">
                            <span>What's included in the DJ service?</span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-answer hidden px-6 py-4 text-gray-400 border-t border-gray-800 bg-[#222222]">
                            <p class="text-sm">Our services include :</p>
                            <ul class="list-disc pl-5 mt-2 space-y-1 text-sm">
                                <li>Professional DJ of your choice</li>
                                <li>Standart sound system equipment</li>
                                <li>Basic lighting</li>
                                <li>Setup dan soundcheck</li>
                                <li>Coordination with event organizers</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <style>
            .faq-item {
                transform: translateY(0);
                transition: all 0.3s ease;
            }

            .faq-item:hover {
                transform: translateY(-2px);
            }

            .faq-answer {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease-out;
            }

            .faq-answer.show {
                max-height: 500px;
                transition: max-height 0.5s ease-in;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const faqButtons = document.querySelectorAll('.faq-button');

                faqButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const faqItem = button.parentElement;
                        const answer = button.nextElementSibling;
                        const icon = button.querySelector('svg');

                        // Toggle answer visibility with animation
                        answer.classList.toggle('hidden');
                        answer.classList.toggle('show');

                        // Rotate icon
                        icon.style.transform = answer.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';

                        // Add active state styles
                        faqItem.classList.toggle('bg-[#2a2a2a]');

                        // Close other answers
                        faqButtons.forEach(otherButton => {
                            if (otherButton !== button) {
                                const otherAnswer = otherButton.nextElementSibling;
                                const otherIcon = otherButton.querySelector('svg');
                                const otherItem = otherButton.parentElement;

                                otherAnswer.classList.add('hidden');
                                otherAnswer.classList.remove('show');
                                otherIcon.style.transform = 'rotate(0deg)';
                                otherItem.classList.remove('bg-[#2a2a2a]');
                            }
                        });
                    });
                });
            });
        </script>
    </main>

    <?php include_once('includes/footer.php'); ?>
</body>

</html>