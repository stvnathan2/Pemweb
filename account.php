<?php
session_start();
require 'koneksiuser.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = connection();
$username = $_SESSION['username'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namalengkap = $_POST['namalengkap'];
    $email = $_POST['email'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == UPLOAD_ERR_OK) {
        $foto_profil = $_FILES['foto_profil']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($foto_profil);

        if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
            $query = "UPDATE user SET namalengkap = '$namalengkap', email = '$email', jenis_kelamin = '$jenis_kelamin', foto_profil = '$target_file' WHERE username = '$username'";
        } else {
            $message = "Error uploading profile picture.";
        }
    } else {
        $query = "UPDATE user SET namalengkap = '$namalengkap', email = '$email', jenis_kelamin = '$jenis_kelamin' WHERE username = '$username'";
    }

    if (mysqli_query($conn, $query)) {
        $message = "Profil telah sukses diperbarui!";
    } else {
        $message = "Profil gagal diperbarui: " . mysqli_error($conn);
    }
}

$query = "SELECT * FROM user WHERE username = '$username'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Profil</title>
    <link rel="icon" type="image/x-icon" href="money.png">
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <div class="image">
        <?php 
        if (!empty($user['foto_profil'])) {
            $foto_profil_path = $user['foto_profil'];
            if (file_exists($foto_profil_path)) {
                echo "<img src='" . $foto_profil_path . "' alt='Foto Profil'>";
            } else {
                echo "<p>Foto Profil Tidak Ditemukan</p>";
            }
        }
        ?>
    </div>
    <div class="container">
        <h2>Profil</h2>
        <?php if ($message != "") { echo "<p class='message'>$message</p>"; } ?>
        <form action="account.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" readonly>
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="namalengkap" value="<?php echo $user['namalengkap']; ?>" required>
            </div>
            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <label>
                    <input type="radio" name="jenis_kelamin" value="Laki-laki" <?php if ($user['jenis_kelamin'] == 'Laki-laki') echo 'checked'; ?>> Laki-laki
                </label>
                <label>
                    <input type="radio" name="jenis_kelamin" value="Perempuan" <?php if ($user['jenis_kelamin'] == 'Perempuan') echo 'checked'; ?>> Perempuan
                </label>
            </div>
            <div class="form-group">
                <label>Foto Profil</label>
                <input type="file" name="foto_profil">
            </div>
            <div class="form-group">
                <button type="submit">Perbarui Profil</button>
                <a href="home.php" class="back-btn">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>
