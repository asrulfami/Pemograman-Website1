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

// Initialize edit data
$edit_data = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_mata_kuliah'])) {
        $kode_jurusan = $_POST['kode_jurusan'];
        $kode_mata_kuliah = $_POST['kode_mata_kuliah'];
        $mata_kuliah = $_POST['mata_kuliah'];
        $semester = $_POST['semester'];

        // Check if kode_jurusan exists in the jurusan table
        $sql = "SELECT COUNT(*) FROM jurusan WHERE kode_jurusan = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kode_jurusan]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            echo "Error: Kode Jurusan tidak ditemukan!";
        } else {
            $sql = "INSERT INTO mata_kuliah (kode_jurusan, kode_mata_kuliah, mata_kuliah, semester) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$kode_jurusan, $kode_mata_kuliah, $mata_kuliah, $semester]);
        }
    } elseif (isset($_POST['edit_mata_kuliah'])) {
        $id = $_POST['id'];
        $kode_jurusan = $_POST['kode_jurusan'];
        $kode_mata_kuliah = $_POST['kode_mata_kuliah'];
        $mata_kuliah = $_POST['mata_kuliah'];
        $semester = $_POST['semester'];
        
        $sql = "UPDATE mata_kuliah SET kode_jurusan = ?, kode_mata_kuliah = ?, mata_kuliah = ?, semester = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kode_jurusan, $kode_mata_kuliah, $mata_kuliah, $semester, $id]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM mata_kuliah WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    } elseif (isset($_POST['edit_form'])) {
        $id = $_POST['id'];
        $sql = "SELECT * FROM mata_kuliah WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Fetch jurusan data
$sql = "SELECT * FROM jurusan";
$stmt = $pdo->query($sql);
$jurusan_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch mata kuliah data with jurusan name
$sql = "SELECT mata_kuliah.*, jurusan.nama_jurusan FROM mata_kuliah JOIN jurusan ON mata_kuliah.kode_jurusan = jurusan.kode_jurusan";
$stmt = $pdo->query($sql);
$mata_kuliah_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Mata Kuliah</title>
    <link rel="stylesheet" href="css/mata_kuliah.css">
</head>
<body>
    <h1>Data Mata Kuliah</h1>

    <!-- Kotak 1: Form to Add/Edit Mata Kuliah -->
    <div class="form-container">
        <h2><?php echo isset($edit_data) ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah'; ?></h2>
        <form action="mata_kuliah.php" method="post">
            <?php if (isset($edit_data)): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>
            <table>
                <tr>
                    <td>Jurusan:</td>
                    <td>
                        <select id="kode_jurusan" name="kode_jurusan" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php foreach ($jurusan_data as $jurusan): ?>
                            <option value="<?php echo htmlspecialchars($jurusan['kode_jurusan']); ?>" <?php echo (isset($edit_data) && $edit_data['kode_jurusan'] == $jurusan['kode_jurusan']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($jurusan['nama_jurusan']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Kode Mata Kuliah:</td>
                    <td><input type="text" id="kode_mata_kuliah" name="kode_mata_kuliah" required value="<?php echo isset($edit_data) ? htmlspecialchars($edit_data['kode_mata_kuliah']) : ''; ?>"></td>
                </tr>
                <tr>
                    <td>Mata Kuliah:</td>
                    <td><input type="text" id="mata_kuliah" name="mata_kuliah" required value="<?php echo isset($edit_data) ? htmlspecialchars($edit_data['mata_kuliah']) : ''; ?>"></td>
                </tr>
                <tr>
                    <td>Semester:</td>
                    <td><input type="text" id="semester" name="semester" required value="<?php echo isset($edit_data) ? htmlspecialchars($edit_data['semester']) : ''; ?>"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="submit" name="<?php echo isset($edit_data) ? 'edit_mata_kuliah' : 'add_mata_kuliah'; ?>"><?php echo isset($edit_data) ? 'Simpan Perubahan' : 'Tambah'; ?></button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Kotak 2: Mata Kuliah Table -->
    <div class="table-container">
        <h2>Daftar Mata Kuliah</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode Jurusan</th>
                    <th>Nama Jurusan</th>
                    <th>Kode Mata Kuliah</th>
                    <th>Mata Kuliah</th>
                    <th>Semester</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mata_kuliah_data as $mata_kuliah): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mata_kuliah['kode_jurusan']); ?></td>
                    <td><?php echo htmlspecialchars($mata_kuliah['nama_jurusan']); ?></td>
                    <td><?php echo htmlspecialchars($mata_kuliah['kode_mata_kuliah']); ?></td>
                    <td><?php echo htmlspecialchars($mata_kuliah['mata_kuliah']); ?></td>
                    <td><?php echo htmlspecialchars($mata_kuliah['semester']); ?></td>
                    <td>
                        <form action="mata_kuliah.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($mata_kuliah['id']); ?>">
                            <button type="submit" name="edit_form">Edit</button>
                        </form>
                        <form action="mata_kuliah.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($mata_kuliah['id']); ?>">
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
