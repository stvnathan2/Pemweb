<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include("koneksiuser.php");
$conn = connection();
$username = $_SESSION['username'];
$query = "SELECT user FROM db_users WHERE username='$username'";
$result = $conn->query($query);

// Periksa apakah query berhasil dieksekusi
if ($result) {
    // Ambil nama pengguna dari hasil query
    $row = $result->fetch_assoc();
    $nama_pengguna = $row['username'];
} else {
    $nama_pengguna = "User";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible"content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet"href="home.css">
</head>

<body>
    <div class="header">
        <h3>MoneyMastery</h3>
    </div>
    <h1>Selamat Datang, <?php echo $username; ?></h1>
    <div class="menu-box">
            <p><a href="input.php" class="btn-view"><span class="icon"><ion-icon name="create"></ion-icon></span><b>Pencatatan Keuangan</b></a></p>
            
            <p><a href="calendar.php" class="btn-add"><span class="icon"><ion-icon name="calculator"></ion-icon></span><b>Kalender Keuangan</b></a></p>
            
            <p><a href="summary.php" class="btn-view"><span class="icon"><ion-icon name="bar-chart"></ion-icon></span><b>Analisis Keuangan</b></a></p>
            
            <p><a href="#" class="btn-add"><span class="icon"><ion-icon name="calculator"></ion-icon></span><b>Kalkulator</b></a></p>
            
            <p><a href="account.php" class="btn-add"><span class="icon"><ion-icon name="create"></ion-icon></span><b>Pengaturan Akun</b></a></p>

            <p><a href="index.php" class="btn-add"><span class="icon"><ion-icon name="create"></ion-icon></span><b>Keluar Akun</b></a></p>
    </div>

    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>

    <script>
    document.getElementById('btn').addEventListener('click', function() {
        window.location.href = 'home.php';
    });
    </script>
    
</body>