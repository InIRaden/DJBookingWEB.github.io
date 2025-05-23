<!-- Tambahkan link ke Tailwind CSS dan Font Awesome -->
<link rel="stylesheet" href="../src/output.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />

<!-- Header dengan Tailwind CSS -->
<header class="relative">
    <img alt="DJ performing at event" class="w-full h-[300px] object-cover" src="images/abt.jpg" />
    <nav class="absolute top-0 left-0 w-full flex items-center justify-between px-6 py-4 text-white bg-black">
        <div class="flex items-center space-x-2 text-sm font-semibold">
            <i class="fas fa-compact-disc"></i>
            <span>DjBooking</span>
        </div>
        <ul class="hidden md:flex space-x-8 text-sm font-normal">
            <li><a class="hover:underline" href="index.php">Home</a></li>
            <li><a class="hover:underline" href="services.php">Services</a></li>
            <li><a class="hover:underline" href="status.php">Request Status</a></li>
            <li><a class="hover:underline" href="about.php">About</a></li>
            <li><a class="hover:underline" href="contact.php">Contact</a></li>
            <li><a class="hover:underline" href="admin/login.php">Admin</a></li>
        </ul>
        <a href="signup.php" class="hidden md:inline-block bg-gray-700 bg-opacity-60 rounded px-3 py-1 text-xs font-semibold hover:bg-gray-600 transition">
            Sign Up
        </a>
    </nav>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center max-w-md px-4">
        <h1 class="text-white font-bold text-lg md:text-xl leading-tight">About Us</h1>
        <p class="text-xs md:text-sm mt-2 text-white">
            Learn more about our DJ services and what makes us special
        </p>
    </div>
</header>
