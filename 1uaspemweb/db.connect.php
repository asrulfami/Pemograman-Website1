<?php
// Konfigurasi database
$host = 'localhost'; // Biasanya 'localhost'
$dbname = 'uaspemweb'; // Nama database Anda
$username = 'root'; // Username database (default untuk XAMPP adalah 'root')
$password = ''; // Password database (default untuk XAMPP adalah kosong)

// Koneksi ke database menggunakan PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
    exit();
}
?>
