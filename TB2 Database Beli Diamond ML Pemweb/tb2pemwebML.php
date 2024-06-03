<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nell Shoop</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="container">
    <h1>Nell Shoop</h1>

    <?php
    $host = "localhost";
    $user = "root";
    $passwd = "";
    $db = "mainml";

    $conn = mysqli_connect($host, $user, $passwd, $db);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Form submission to add data
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_token']) && $_POST['form_token'] === $_SESSION['form_token']) {
        $name = $_POST['name'];
        $id = $_POST['id'];
        $gmail = $_POST['gmail'];
        $beli_diamond = $_POST['beli_diamond'];
        $skin = $_POST['skin'];
        $tanggal_beli = $_POST['tanggal_beli'];

        $sql_insert = "INSERT INTO loginml (name, id, gmail, beli_diamond, skin, tanggal_beli) VALUES ('$name', '$id', '$gmail', '$beli_diamond', '$skin', '$tanggal_beli')";

        if (mysqli_query($conn, $sql_insert)) {
            echo "<p>New record created successfully</p>";
        } else {
            echo "Error: " . $sql_insert . "<br>" . mysqli_error($conn);
        }

        // Regenerate form token to prevent resubmission
        $_SESSION['form_token'] = bin2hex(random_bytes(32));
    }

    // Handle delete request
    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];
        $sql_delete = "DELETE FROM loginml WHERE id='$delete_id'";

        if (mysqli_query($conn, $sql_delete)) {
            echo "<p>Record deleted successfully</p>";
        } else {
            echo "Error: " . $sql_delete . "<br>" . mysqli_error($conn);
        }
    }

    $sql = "SELECT * FROM loginml";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Error in SQL query: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
    ?>

    <table>
        <tr>
            <th>Nama</th>
            <th>ID ML</th>
            <th>Gmail</th>
            <th>Beli Diamond</th>
            <th>Skin ML</th>
            <th>Tanggal Beli</th>
            <th>Aksi</th> <!-- New column for actions -->
        </tr>

    <?php
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['gmail']) ?></td>
            <td><?= htmlspecialchars($row['beli_diamond']) ?></td>
            <td><?= htmlspecialchars($row['skin']) ?></td>
            <td><?= htmlspecialchars($row['tanggal_beli']) ?></td>
            <td><a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">Hapus</a></td> <!-- Delete link -->
        </tr>
    <?php
        }
    ?>
    </table>

    <?php
    } else {
        echo "<p>No results found</p>";
    }
    mysqli_close($conn);

    // Generate new form token
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
    ?>

    <form method="post" action="">
        <h2>Tambah Data Baru</h2>
        <label for="name">Nama:</label>
        <input type="text" id="name" name="name" required><br>
        <label for="id">ID ML:</label>
        <input type="text" id="id" name="id" required><br>
        <label for="gmail">Gmail:</label>
        <input type="email" id="gmail" name="gmail" required><br>
        <label for="beli_diamond">Beli Diamond:</label>
        <input type="number" id="beli_diamond" name="beli_diamond" required><br>
        <label for="skin">Skin ML:</label>
        <input type="text" id="skin" name="skin" required><br>
        <label for="tanggal_beli">Tanggal Beli:</label>
        <input type="date" id="tanggal_beli" name="tanggal_beli" required><br>
        <input type="hidden" name="form_token" value="<?= $_SESSION['form_token'] ?>">
        <button type="submit">Submit</button>
    </form>
</div>

</body>
</html>
