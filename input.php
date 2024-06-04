<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $conn = connection();
    
    $sql = "INSERT INTO daily_expenses (amount, type, category, date, description) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dssss", $amount, $type, $category, $date, $description);

    if ($stmt->execute()) {
        echo "Data berhasil disimpan.";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . basename($file["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.<br>";
            $uploadOk = 0;
        }
        
        if ($file["size"] > 2000000) {
            echo "Sorry, your file is too large.<br>";
            $uploadOk = 0;
        }
        
        if($fileType != "jpg" && $fileType != "png" && $fileType != "pdf" ) {
            echo "Sorry, only JPG, PNG & PDF files are allowed.<br>";
            $uploadOk = 0;
        }
        
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.<br>";
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars(basename($file["name"])). " has been uploaded.<br>";

                $conn = connection();
                $stmt = $conn->prepare("INSERT INTO receipts (file_path) VALUES (?)");
                $stmt->bind_param("s", $target_file);
                if ($stmt->execute()) {
                    echo "File record saved successfully<br>";
                } else {
                    echo "Error: " . $stmt->error . "<br>";
                }
                $stmt->close();
                $conn->close();
            } else {
                echo "Sorry, there was an error uploading your file.<br>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pemasukan dan Pengeluaran</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h2 class="logo">Money Mastery</h2>
        <nav class="navigation">
            <a href="#tips">Tips</a>
            <a href="#konsultasi">Konsultasi</a>
            <a href="#tentang">Tentang</a>
            <a href="#bantuan">Bantuan</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <section class="input-form">
                <h2>Input Pemasukan dan Pengeluaran</h2>
                <form action="input.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="date">Tanggal</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="type">Tipe</label>
                        <select id="type" name="type" required>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <input type="text" id="category" name="category" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Nominal</label>
                        <input type="number" step="0.01" id="amount" name="amount" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Keterangan</label>
                        <textarea id="description" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="file">Upload Receipt:</label>
                        <input type="file" id="file" name="file" required>
                    </div>
                    
                    <button type="submit">Submit</button>
                </form>
            </section>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 Money Mastery. All rights reserved.</p>
    </footer>
</body>
</html>

