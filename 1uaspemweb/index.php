<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang Administrator Asrul</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <h1>Selamat Datang Administrator Asrul</h1>
    <table>
        <tr>
            <td colspan="2" class="table-header">Menu Aplikasi Sistem Informasi Kampus</td>
        </tr>
        <tr>
            <td>
                <ul>
                    <li><a href="dosen.php">Dosen</a></li>
                    <li><a href="mata_kuliah.php">Mata Kuliah</a></li> <!-- Perbaikan nama file -->
                </ul>
            </td>
            <td>
                <ul>
                    <li><a href="jurusan.php">Jurusan</a></li>
                    <li><a href="mahasiswa.php">Mahasiswa</a></li>
                </ul>
            </td>
        </tr>
    </table>
    <a href="logout.php" class="logout">Logout</a>
</body>
</html>
