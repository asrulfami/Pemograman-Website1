<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'uaspemweb';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $kode_jurusan = $_POST['kode_jurusan'];
        $nama_jurusan = $_POST['nama_jurusan'];

        // Check if kode_jurusan already exists
        $sql = "SELECT COUNT(*) FROM jurusan WHERE kode_jurusan = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kode_jurusan]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            echo "Error: Kode Jurusan sudah ada!";
        } else {
            $sql = "INSERT INTO jurusan (kode_jurusan, nama_jurusan) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute([$kode_jurusan, $nama_jurusan])) {
                echo "Error: " . print_r($stmt->errorInfo(), true);
            }
        }
    } elseif (isset($_POST['edit'])) {
        $kode_jurusan = $_POST['kode_jurusan'];
        $nama_jurusan = $_POST['nama_jurusan'];
        $sql = "UPDATE jurusan SET nama_jurusan = ? WHERE kode_jurusan = ?";
        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute([$nama_jurusan, $kode_jurusan])) {
            echo "Error: " . print_r($stmt->errorInfo(), true);
        }
    } elseif (isset($_POST['delete'])) {
        $kode_jurusan = $_POST['kode_jurusan'];
        $sql = "DELETE FROM jurusan WHERE kode_jurusan = ?";
        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute([$kode_jurusan])) {
            echo "Error: " . print_r($stmt->errorInfo(), true);
        }
    }
}

// Fetch jurusan data
$sql = "SELECT * FROM jurusan";
$stmt = $pdo->query($sql);
$jurusan_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Jurusan</title>
    <link rel="stylesheet" href="css/jurusan.css">
</head>
<body>
    <h1>Data Jurusan</h1>

    <!-- Form to Add Jurusan -->
    <div class="form-container">
        <h2>Tambah Jurusan</h2>
        <form action="jurusan.php" method="post">
            <label for="kode_jurusan">Kode Jurusan:</label>
            <input type="text" id="kode_jurusan" name="kode_jurusan" required>
            <label for="nama_jurusan">Nama Jurusan:</label>
            <input type="text" id="nama_jurusan" name="nama_jurusan" required>
            <button type="submit" name="add">Tambah</button>
        </form>
    </div>

    <!-- Jurusan Table -->
    <div class="table-container">
        <h2>Daftar Jurusan</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode Jurusan</th>
                    <th>Nama Jurusan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jurusan_data as $jurusan): ?>
                <tr>
                    <td><?php echo htmlspecialchars($jurusan['kode_jurusan']); ?></td>
                    <td><?php echo htmlspecialchars($jurusan['nama_jurusan']); ?></td>
                    <td>
                        <!-- Edit Button -->
                        <form action="jurusan.php" method="post" style="display:inline;">
                            <input type="hidden" name="kode_jurusan" value="<?php echo htmlspecialchars($jurusan['kode_jurusan']); ?>">
                            <button type="submit" name="edit_form">Edit</button>
                        </form>
                        <!-- Delete Button -->
                        <form action="jurusan.php" method="post" style="display:inline;">
                            <input type="hidden" name="kode_jurusan" value="<?php echo htmlspecialchars($jurusan['kode_jurusan']); ?>">
                            <button type="submit" name="delete">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Form -->
    <?php if (isset($_POST['edit_form'])): ?>
    <?php
    $kode_jurusan = $_POST['kode_jurusan'];
    $sql = "SELECT * FROM jurusan WHERE kode_jurusan = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kode_jurusan]);
    $jurusan_edit = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="form-container">
        <h2>Edit Jurusan</h2>
        <form action="jurusan.php" method="post">
            <input type="hidden" name="kode_jurusan" value="<?php echo htmlspecialchars($jurusan_edit['kode_jurusan']); ?>">
            <label for="nama_jurusan">Nama Jurusan:</label>
            <input type="text" id="nama_jurusan" name="nama_jurusan" value="<?php echo htmlspecialchars($jurusan_edit['nama_jurusan']); ?>" required>
            <button type="submit" name="edit">Simpan Perubahan</button>
        </form>
    </div>
    <?php endif; ?>

    <a href="index.php">Kembali</a>
</body>
</html>
