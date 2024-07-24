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
    if (isset($_POST['add_mahasiswa'])) {
        $nirm = $_POST['nirm'];
        $nama_mahasiswa = $_POST['nama_mahasiswa'];
        $alamat = $_POST['alamat'];
        $kota = $_POST['kota'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tempat_lahir = $_POST['tempat_lahir'];
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $kode_jurusan = $_POST['kode_jurusan'];
        $ipk_akhir = $_POST['ipk_akhir'];

        $sql = "INSERT INTO mahasiswa (nirm, nama_mahasiswa, alamat, kota, jenis_kelamin, tempat_lahir, tanggal_lahir, kode_jurusan, ipk_akhir) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nirm, $nama_mahasiswa, $alamat, $kota, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $kode_jurusan, $ipk_akhir]);
    } elseif (isset($_POST['edit_mahasiswa'])) {
        $nirm = $_POST['nirm'];
        $alamat = $_POST['alamat'];
        $kota = $_POST['kota'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tempat_lahir = $_POST['tempat_lahir'];
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $kode_jurusan = $_POST['kode_jurusan'];
        $ipk_akhir = $_POST['ipk_akhir'];

        $sql = "UPDATE mahasiswa SET alamat = ?, kota = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, kode_jurusan = ?, ipk_akhir = ? 
                WHERE nirm = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$alamat, $kota, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $kode_jurusan, $ipk_akhir, $nirm]);
    } elseif (isset($_POST['delete'])) {
        $nirm = $_POST['nirm'];
        $sql = "DELETE FROM mahasiswa WHERE nirm = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nirm]);
    }
}

// Fetch jurusan data
$sql = "SELECT * FROM jurusan";
$stmt = $pdo->query($sql);
$jurusan_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch mahasiswa data
$sql = "SELECT * FROM mahasiswa";
$stmt = $pdo->query($sql);
$mahasiswa_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch mahasiswa data for editing
$edit_mahasiswa = null;
if (isset($_GET['nirm'])) {
    $nirm = $_GET['nirm'];
    $sql = "SELECT * FROM mahasiswa WHERE nirm = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nirm]);
    $edit_mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="css/mahasiswa.css">
</head>
<body>
    <h1>Data Mahasiswa</h1>

    <!-- Form to Add/Edit Mahasiswa -->
    <div class="form-container">
        <h2><?php echo $edit_mahasiswa ? 'Edit Mahasiswa' : 'Tambah Mahasiswa'; ?></h2>
        <form action="mahasiswa.php" method="post">
            <?php if ($edit_mahasiswa): ?>
                <input type="hidden" name="nirm" value="<?php echo htmlspecialchars($edit_mahasiswa['nirm']); ?>">
            <?php else: ?>
                <label for="nirm">NIRM:</label>
                <input type="text" id="nirm" name="nirm" required>
            <?php endif; ?>

            <label for="nama_mahasiswa">Nama Mahasiswa:</label>
            <input type="text" id="nama_mahasiswa" name="nama_mahasiswa" value="<?php echo htmlspecialchars($edit_mahasiswa['nama_mahasiswa'] ?? ''); ?>" required>

            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" value="<?php echo htmlspecialchars($edit_mahasiswa['alamat'] ?? ''); ?>" required>

            <label for="kota">Kota:</label>
            <input type="text" id="kota" name="kota" value="<?php echo htmlspecialchars($edit_mahasiswa['kota'] ?? ''); ?>" required>

            <label for="jenis_kelamin">Jenis Kelamin:</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="Laki-laki" <?php echo ($edit_mahasiswa['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                <option value="Perempuan" <?php echo ($edit_mahasiswa['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
            </select>

            <label for="tempat_lahir">Tempat Lahir:</label>
            <input type="text" id="tempat_lahir" name="tempat_lahir" value="<?php echo htmlspecialchars($edit_mahasiswa['tempat_lahir'] ?? ''); ?>" required>

            <label for="tanggal_lahir">Tanggal Lahir:</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($edit_mahasiswa['tanggal_lahir'] ?? ''); ?>" required>

            <label for="kode_jurusan">Jurusan:</label>
            <select id="kode_jurusan" name="kode_jurusan" required>
                <?php foreach ($jurusan_data as $jurusan): ?>
                    <option value="<?php echo htmlspecialchars($jurusan['kode_jurusan']); ?>" <?php echo ($edit_mahasiswa['kode_jurusan'] ?? '') == $jurusan['kode_jurusan'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($jurusan['kode_jurusan']) . ' - ' . htmlspecialchars($jurusan['nama_jurusan']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="ipk_akhir">IPK Akhir:</label>
            <input type="number" step="0.01" id="ipk_akhir" name="ipk_akhir" value="<?php echo htmlspecialchars($edit_mahasiswa['ipk_akhir'] ?? ''); ?>" required>

            <button type="submit" name="<?php echo $edit_mahasiswa ? 'edit_mahasiswa' : 'add_mahasiswa'; ?>">
                <?php echo $edit_mahasiswa ? 'Simpan Perubahan' : 'Tambah'; ?>
            </button>
        </form>
    </div>

    <!-- Mahasiswa Table -->
    <div class="table-container">
        <h2>Daftar Mahasiswa</h2>
        <table>
            <thead>
                <tr>
                    <th>NIRM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Alamat</th>
                    <th>Kota</th>
                    <th>Jenis Kelamin</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Jurusan</th>
                    <th>IPK Akhir</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mahasiswa_data as $mahasiswa): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mahasiswa['nirm']); ?></td>
                    <td><?php echo htmlspecialchars($mahasiswa['nama_mahasiswa']); ?></td>
                    <td><?php echo htmlspecialchars($mahasiswa['alamat']); ?></td>
                    <td><?php echo htmlspecialchars($mahasiswa['kota']); ?></td>
                    <td><?php echo htmlspecialchars($mahasiswa['jenis_kelamin']); ?></td>
                    <td><?php echo htmlspecialchars($mahasiswa['tempat_lahir']); ?></td>
                    <td><?php echo htmlspecialchars($mahasiswa['tanggal_lahir']); ?></td>
                    <td><?php echo htmlspecialchars($mahasiswa['kode_jurusan']); ?></td>
                    <td><?php echo htmlspecialchars($mahasiswa['ipk_akhir']); ?></td>
                    <td>
                        <a href="mahasiswa.php?nirm=<?php echo htmlspecialchars($mahasiswa['nirm']); ?>">Edit</a>
                        <form action="mahasiswa.php" method="post" style="display:inline;">
                            <input type="hidden" name="nirm" value="<?php echo htmlspecialchars($mahasiswa['nirm']); ?>">
                            <button type="submit" name="delete">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="index.php">Kembali</a>
</body>
</html>
