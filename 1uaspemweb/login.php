<?php
session_start();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded username and password
    $valid_username = 'admin';
    $valid_password = 'password123';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
        .form-group {
            margin-bottom: 15px;
            position: relative;
        }
        .form-group input {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 35px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility()">👁️</span>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById('password');
            var passwordFieldType = passwordField.getAttribute('type');
            var togglePassword = document.querySelector('.toggle-password');
            if (passwordFieldType === 'password') {
                passwordField.setAttribute('type', 'text');
                togglePassword.textContent = '🙈';
            } else {
                passwordField.setAttribute('type', 'password');
                togglePassword.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
