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
    echo "Connection failed: " . $e->getMessage();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $kode_dosen = $_POST['kode_dosen'];
        $nama_dosen = $_POST['nama_dosen'];
        $sql = "INSERT INTO dosen (kode_dosen, nama_dosen) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kode_dosen, $nama_dosen]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $kode_dosen = $_POST['kode_dosen'];
        $nama_dosen = $_POST['nama_dosen'];
        $sql = "UPDATE dosen SET kode_dosen = ?, nama_dosen = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kode_dosen, $nama_dosen, $id]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM dosen WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}

// Fetch dosen data
$sql = "SELECT * FROM dosen";
$stmt = $pdo->query($sql);
$dosen_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Dosen</title>
    <link rel="stylesheet" href="css/dosen.css">
</head>
<body>
    <h1>Data Dosen</h1>

    <!-- Form to Add Dosen -->
    <div class="form-container">
        <h2>Tambah Dosen</h2>
        <form action="dosen.php" method="post">
            <label for="kode_dosen">Kode Dosen:</label>
            <input type="text" id="kode_dosen" name="kode_dosen" required>
            <label for="nama_dosen">Nama Dosen:</label>
            <input type="text" id="nama_dosen" name="nama_dosen" required>
            <button type="submit" name="add">Tambah</button>
        </form>
    </div>

    <!-- Dosen Table -->
    <div class="table-container">
        <h3>Daftar Dosen</h3>
        <table>
            <thead>
                <tr>
                    <th>Kode Dosen</th>
                    <th>Nama Dosen</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dosen_data as $dosen): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dosen['kode_dosen']); ?></td>
                    <td><?php echo htmlspecialchars($dosen['nama_dosen']); ?></td>
                    <td>
                        <!-- Edit Button -->
                        <form action="dosen.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($dosen['id']); ?>">
                            <button type="submit" name="edit_form" value="Edit">Edit</button>
                        </form>
                        <!-- Delete Button -->
                        <form action="dosen.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($dosen['id']); ?>">
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
    $id = $_POST['id'];
    $sql = "SELECT * FROM dosen WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $dosen_edit = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="form-container">
        <h2>Edit Dosen</h2>
        <form action="dosen.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($dosen_edit['id']); ?>">
            <label for="kode_dosen">Kode Dosen:</label>
            <input type="text" id="kode_dosen" name="kode_dosen" value="<?php echo htmlspecialchars($dosen_edit['kode_dosen']); ?>" required>
            <label for="nama_dosen">Nama Dosen:</label>
            <input type="text" id="nama_dosen" name="nama_dosen" value="<?php echo htmlspecialchars($dosen_edit['nama_dosen']); ?>" required>
            <button type="submit" name="edit">Simpan Perubahan</button>
        </form>
    </div>
    <?php endif; ?>

    <a href="index.php">Kembali</a>
</body>
</html>
