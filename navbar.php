<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Layout</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 pt-20"> <!-- Tambah padding atas agar konten tidak tertutup navbar -->

    <!-- Navbar -->
    <nav class="bg-gray-100 shadow-md py-4 fixed top-0 left-0 w-full z-50">
        <div class="container mx-auto flex items-center justify-between px-6">
            <!-- Logo dan Nama -->
            <a href="#" class="flex items-center space-x-3 text-2xl font-bold">
                <img src="pid.png" alt="Logo PELNI" class="h-12 w-12"> 
                <p class="text-4xl font-bold text-blue-800 tracking-wide">PELNI</p>
            </a>
            
            <!-- Tombol Hamburger untuk Mobile -->
            <button id="menu-toggle" class="md:hidden text-black text-2xl">☰</button>

            <!-- Menu Navigasi --> 
            <div id="menu" class="hidden md:flex space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-blue-500 transition">Home</a>
                <a href="slide.php" class="text-gray-700 hover:text-blue-500 transition">Slideshow</a>
            </div>

            <!-- Auth Navigation -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Logout</a>
            </div>
        </div>
    </nav>
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function () {
            document.getElementById('menu').classList.toggle('hidden');
        });
    </script>

</body>
</html>
