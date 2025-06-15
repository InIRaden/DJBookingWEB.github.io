<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    include 'dbconnection.php';
}
?>

<!-- Header dengan Tailwind CSS -->
<style>
    /* Animasi hover untuk menu navigasi */
    .nav-link {
        position: relative;
        transition: all 0.3s ease;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -4px;
        left: 0;
        background-color: #ff4d4d;
        transition: width 0.3s ease;
    }

    .nav-link:hover {
        color: #ff4d4d;
        transform: translateY(-2px);
    }

    .nav-link:hover::after {
        width: 100%;
    }

    /* Efek hover untuk tombol */
    .btn-signin {
        background: linear-gradient(45deg, #ff4d4d, #f9333f);
        box-shadow: 0 4px 15px rgba(255, 77, 77, 0.3);
        transition: all 0.3s ease;
    }

    .btn-signin:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(255, 77, 77, 0.4);
    }

    .btn-signup {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .btn-signup:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    /* Ensure header is above all elements */
    .header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        /* High z-index to stay above other elements */
        background-color: rgba(0, 0, 0, 0.9);
        /* Slightly transparent black background */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        /* Subtle shadow for depth */
    }

    /* Custom animation for spinning icon */
    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin-slow {
        animation: spin-slow 3s linear infinite;
    }
</style>

<header class="header">
    <nav class="flex items-center justify-between px-6 py-4 text-white">
        <!-- Logo / Judul -->
        <div class="flex items-center space-x-2 text-sm font-semibold">
            <a href="index.php" class="flex items-center space-x-2 hover:text-red-400 transition-colors duration-300">
                <i class="fas fa-compact-disc animate-spin-slow"></i>
                <span>SPINUP</span>
            </a>
        </div>

        <!-- Menu Navigasi -->
        <ul class="hidden md:flex space-x-8 text-sm font-normal">
            <li><a class="nav-link" href="index.php">Home</a></li>
            <li><a class="nav-link" href="services.php">Services</a></li>
            <li><a class="nav-link" href="status.php">Request Status</a></li>
            <li><a class="nav-link" href="about.php">About</a></li>
            <li><a class="nav-link" href="contact.php">Contact</a></li>
            <li><a class="nav-link" href="admin/login.php">Admin</a></li>
        </ul>

        <!-- Tombol Sign In / Logout -->
        <div class="hidden md:flex items-center space-x-4">
            <?php if (isset($_SESSION['odmsaid'])): ?>
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-gray-300">
                        Welcome, <?php echo htmlspecialchars($_SESSION['NamaUser']); ?>
                    </span>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded-md transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                        Logout
                    </a>
                </div>
            <?php else: ?>
                <div class="flex items-center space-x-3">
                    <a href="signin.php" class="btn-signin text-white text-xs font-semibold px-4 py-2 rounded-md transition-all duration-300">
                        Sign In
                    </a>
                    <a href="signup.php" class="btn-signup text-white text-xs font-semibold px-4 py-2 rounded-md transition-all duration-300">
                        Sign Up
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</header>