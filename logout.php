<?php
// logout.php
session_start();
session_destroy(); // Hapus semua sesi
header("Location: login.php"); // Redirect kembali ke login setelah logout
exit();
?>

<?php
// index.php
session_start();

// Periksa apakah sesi user sudah ada
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}
?>

