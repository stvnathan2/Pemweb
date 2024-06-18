<?php
session_start(); 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksiuser.php';

$conn = connection();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare('SELECT * FROM daily_expenses WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Detail Transaksi</title>
            <link rel="stylesheet" href="calendar.css">
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

            <div class="wrapper">
                <div class="container">
                    <section class="detail-form">
                        <h2>Detail Transaksi</h2>
                        <div class="form-group">
                            <label for="date">Tanggal:</label>
                            <p><?php echo htmlspecialchars($row['date']); ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="type">Tipe:</label>
                            <p><?php echo htmlspecialchars($row['type']); ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Kategori:</label>
                            <p><?php echo htmlspecialchars($row['category']); ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="amount">Nominal (Rp.):</label>
                            <p><?php echo htmlspecialchars(number_format($row['amount'], 2)); ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Keterangan:</label>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Metode Pembayaran:</label>
                            <p><?php echo htmlspecialchars($row['payment_method']); ?></p>
                        </div>

                        <div class="form-group">
                            <label for="file">Unggah Struk:</label>
                            <?php if ($row['file_path']): ?>
                                <p><a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">Lihat File</a></p>
                            <?php else: ?>
                                <p>Tidak ada file yang diunggah.</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <a href="calendar.php" class="btn-back">Kembali ke Kalender</a>
                            <a href="edit.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn-update">Edit</a>
                            <a href="delete.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn-delete">Hapus</a>
                        </div>
                    </section>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo 'Transaksi tidak ditemukan.';
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'ID tidak valid.';
}
?>
