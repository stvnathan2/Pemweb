<?php
include('koneksiuser.php');

$status = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $namalengkap = $_POST['namalengkap'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $check_query = "SELECT * FROM user WHERE username='$username'";
    $check_result = mysqli_query(connection(), $check_query);

    if(mysqli_num_rows($check_result) > 0){
        $status = 'exists';
    } else {
        $query = "INSERT INTO user (username, namalengkap, email, password) 
        VALUES ('$username', '$namalengkap', '$email', '$password')";

        $result = mysqli_query(connection(), $query);
        if($result){
            header("Location: login.php");
        } else {
            $status = 'Err';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun MoneyMastery</title>
    <link rel="stylesheet" href="regist.css">
    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        var status = '<?php echo $status; ?>';
        if (status === 'exists') {
            alert('Username sudah ada. Silakan gunakan username lain.');
        }

        document.getElementById('loginBtn').addEventListener('click', function() {
            window.location.href = 'login.php';
        });

        document.getElementById('btn').addEventListener('click', function() {
            window.location.href = 'home.php';
        });
    </script>
</head>

<body>
    <header>
        <a href="index.php" class="logo">Money Mastery</a>
        <nav class="navigation">
            <a href="index.php">Beranda</a>
            <a href="tips.php">Tips</a>
            <a href="konsultasi.php">Konsultasi</a>
            <a href="about.php">Tentang</a>
            <a href="bantuan.php">Bantuan</a>
        </nav>
    </header>

    <div class="wrapper">
        <a href="index.php"><span class="icon-close"><ion-icon name="close"></ion-icon></span></a>
        <div class="form-box login">
            <h2>Daftar Akun</h2>
            <form action="regist.php" method="post">
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="namalengkap" required>
                    <label>Nama Lengkap</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="username" required>
                    <label>Username</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>Alamat Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Kata Sandi</label>
                </div>
                <div class="remember-forgot">
                    <label><input type="checkbox" name="terms" required>Saya menyetujui seluruh syarat & ketentuan</label>
                </div>
                <button type="submit" class="btn">Daftar</button>
                <div class="login-register">
                    <p>Sudah punya akun? <a href="login.php" class="register-link">Masuk</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>