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

// Proses update profil jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namalengkap = $_POST['namalengkap'];
    $email = $_POST['email'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    // Menangani upload file foto profil
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
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
    }
}

// Ambil data user dari database
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
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <div class="image">
        <?php if ($user['foto_profil'] != "") { echo "<img src='".$user['foto_profil']."'>"; } ?>
    </div>
    <div class="edit-container">
        <form id="uploadForm" action="account.php" method="post" enctype="multipart/form-data">
            <button type="button" id="editBtn" class="editBtn">
                <label for="foto_profil" class="custom-file-upload">Edit</label>
                <input type="file" id="foto_profil" name="foto_profil" style="display: none;">
            </button>
            <input type="hidden" name="namalengkap" value="<?php echo $user['namalengkap']; ?>">
            <input type="hidden" name="email" value="<?php echo $user['email']; ?>">
            <input type="hidden" name="jenis_kelamin" value="<?php echo $user['jenis_kelamin']; ?>">
        </form>
    </div>
    <div class="container">
        <h2>Update Profile</h2>
        <?php if ($message != "") { echo "<p class='message'>$message</p>"; } ?>
        <form action="account.php" method="post">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" readonly>
            </div>
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="namalengkap" value="<?php echo $user['namalengkap']; ?>" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label>Gender:</label>
                <label>
                    <input type="radio" name="jenis_kelamin" value="Laki-laki" <?php if ($user['jenis_kelamin'] == 'Laki-laki') echo 'checked'; ?>> Laki-laki
                </label>
                <label>
                    <input type="radio" name="jenis_kelamin" value="Perempuan" <?php if ($user['jenis_kelamin'] == 'Perempuan') echo 'checked'; ?>> Perempuan
                </label>
            </div>
            <div class="form-group">
                <button type="submit">Update Profile</button>
                <a href="home.php" class="back-btn">Kembali</a>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('editBtn').onclick = function() {
            document.getElementById('foto_profil').click();
        };
        document.getElementById('foto_profil').onchange = function () {
            document.getElementById('uploadForm').submit();
        };
    </script>
</body>
</html>
