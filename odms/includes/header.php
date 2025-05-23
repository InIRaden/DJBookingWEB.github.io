<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Header dengan Tailwind CSS -->
<header class="relative">
    <nav class="absolute top-0 left-0 w-full flex items-center justify-between px-6 py-4 text-white bg-black bg-opacity-80 z-50">
        <!-- Logo / Judul -->
        <div class="flex items-center space-x-2 text-sm font-semibold">
            <a href="index.php" class="flex items-center space-x-2">
                <i class="fas fa-compact-disc"></i>
                <span>DjBooking</span>
            </a>
        </div>

        <!-- Menu Navigasi -->
        <ul class="hidden md:flex space-x-8 text-sm font-normal">
            <li><a class="hover:underline" href="index.php">Home</a></li>
            <li><a class="hover:underline" href="services.php">Services</a></li>
            <li><a class="hover:underline" href="status.php">Request Status</a></li>
            <li><a class="hover:underline" href="about.php">About</a></li>
            <li><a class="hover:underline" href="contact.php">Contact</a></li>
            <li><a class="hover:underline" href="admin/login.php">Admin</a></li>
        </ul>

        <!-- Tombol Sign In / Logout -->
        <div class="hidden md:flex items-center space-x-4">
            <?php if(isset($_SESSION['odmsaid'])): ?>
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-gray-300">
                        Welcome, <?php echo htmlspecialchars($_SESSION['fname']); ?>
                    </span>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded transition-colors">
                        Logout
                    </a>
                </div>
            <?php else: ?>
                <a href="signin.php" class="bg-gray-700 bg-opacity-60 rounded px-3 py-1 text-xs font-semibold hover:bg-gray-600 transition">
                    Sign In
                </a>
            <?php endif; ?>
        </div>
    </nav>
</header>
