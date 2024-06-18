<?php
session_start(); 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksiuser.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $payment_method = $_POST['payment_method'];
    $account = $_POST['account'];
    $username = $_SESSION['username'];
    $file_path = null;

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
            $uploadOk = 0;
        }
        
        if ($file["size"] > 2000000) {
            $uploadOk = 0;
        }
        
        if($fileType != "jpg" && $fileType != "png" && $fileType != "pdf" ) {
            $uploadOk = 0;
        }
        
        if ($uploadOk == 0) {
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                $file_path = $target_file;
            }
        }
    }

    $conn = connection();
    
    $sql = "INSERT INTO daily_expenses (amount, type, category, date, description, payment_method, account, file_path, username) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dssssssss", $amount, $type, $category, $date, $description, $payment_method, $account, $file_path, $username);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan.');</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pemasukan dan Pengeluaran</title>
    <link rel="icon" type="image/x-icon" href="money.png">
    <link rel="stylesheet" href="styles.css">
    <script>
        function formatAndSaveNominal(input) {
            let originalValue = input.value.replace(/\D/g, '');

            let formattedValue = new Intl.NumberFormat().format(originalValue);

            document.getElementById('amount_original').value = originalValue;

            input.value = formattedValue;
        }

        const pemasukanOptions = [
            { value: "gaji", text: "Gaji" },
            { value: "hadiah", text: "Hadiah" },
            { value: "lainnya", text: "Lainnya" }
        ];

        const pengeluaranOptions = [
            { value: "transportasi", text: "Transportasi" },
            { value: "belanja", text: "Belanja" },
            { value: "makanan", text: "Makanan" },
            { value: "lainnya", text: "Lainnya" }
        ];

        function updateCategoryOptions() {
            const typeSelect = document.getElementById('type');
            const categorySelect = document.getElementById('category');
            const selectedType = typeSelect.value;

            categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';

            let options = selectedType === 'pemasukan' ? pemasukanOptions : pengeluaranOptions;
            options.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option.value;
                opt.textContent = option.text;
                categorySelect.appendChild(opt);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateCategoryOptions();
        });

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date').value = today;

            updateCategoryOptions();
        });
    </script>
</head>
<body>
    <header>
        <a href="home.php" class="logo">Money Mastery</a>
        <nav class="navigation">
            <a href="tips.php">Tips</a>
            <a href="konsultasi.php">Konsultasi</a>
            <a href="about.php">Tentang</a>
            <a href="bantuan.php">Bantuan</a>
        </nav>
    </header>

    <main>
        <div class="container">
            <section class="input-form" style="width: 400px;">
                <form action="input.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="date">Tanggal</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="type">Tipe</label>
                        <select id="type" name="type" required onchange="updateCategoryOptions()" value="pemasukan">
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <select id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Nominal</label>
                        <input type="text" id="amount_display" name="amount_display" required autocomplete="off" oninput="formatAndSaveNominal(this)">
                        <input type="hidden" id="amount_original" name="amount">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Keterangan</label>
                        <textarea id="description" name="description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Metode Pembayaran</label>
                        <input type="text" id="payment_method" name="payment_method" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="account">Akun</label>
                        <input type="text" id="account" name="account" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="file">Unggah Struk</label>
                        <input type="file" id="file" name="file">
                    </div>
                    
                    <button type="submit">Submit</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
