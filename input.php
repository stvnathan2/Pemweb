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
        <div class="container">
            <div class="logo">Money Mastery</div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="input_expense.php">Input Data</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <section class="input-form">
                <h2>Input Pemasukan dan Pengeluaran</h2>
                <form action="input_expense.php" method="POST">
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
