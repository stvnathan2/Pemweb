<?php
require 'koneksiuser.php';
$conn = connection();

$login_failed = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $query_sql = "SELECT * FROM user 
            WHERE username = '$username' AND password = '$password'";

    $result = mysqli_query($conn, $query_sql);

    if (mysqli_num_rows($result) > 0) {
    header("Location: home.php");
    exit();
    } else {
        $login_failed = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible"content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Akun MoneyMastery</title>
    <link rel="stylesheet"href="login.css">
</head>

<body>
    <header>
        <h2 class="logo">Logo</h2>
        <nav class="navigation">
            <a href="#tips">Tips</a>
            <a href="#konsultasi">Konsultasi</a>
            <a href="#tentang">Tentang</a>
            <a href="#bantuan">Bantuan</a>
        </nav>
    </header>

    <div class="wrapper">
        <a href="index.php"><span class="icon-close"><ion-icon name="close"></ion-icon></span></a>
        
        <div class="form-box login">
            <h2>Masuk Akun</h2>
            <form action="login.php" method="post">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Kata Sandi</label>
                </div>
                <!-- <div class="remember-forgot">
                    <label><input type="checkbox">Ingat saya</label>
                    <a href="#">Lupa kata sandi?</a>
                </div> -->
                <button type="submit" class="btn">Masuk</button>
                <div class="login-register">
                    <p>Belum punya akun? <a href="regist.php" class="register-link">Daftar</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
    document.getElementById('btn').addEventListener('click', function() {
        window.location.href = 'home.php';
    });
    </script>
    <script>
    <?php if ($login_failed): ?>
        alert("Email atau Password Anda Salah. Silakan Coba Login Kembali.");
        document.getElementById('loginForm').reset();
    <?php endif; ?>
    </script>

</body>
</html>