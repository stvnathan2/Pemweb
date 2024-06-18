<?php
include 'koneksiuser.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $conn = connection();
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
            <title>Update Transaksi</title>
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
                        <h2>Update Transaksi</h2>
                        <form action="update.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <div class="form-group">
                                <label for="date">Tanggal:</label>
                                <input type="text" name="date" value="<?php echo htmlspecialchars($row['date']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Tipe:</label>
                                <input type="text" name="type" value="<?php echo htmlspecialchars($row['type']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Kategori:</label>
                                <input type="text" name="category" value="<?php echo htmlspecialchars($row['category']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="amount">Nominal (Rp.):</label>
                                <input type="text" name="amount" value="<?php echo htmlspecialchars($row['amount']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Keterangan:</label>
                                <textarea name="description" rows="4" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="payment_method">Metode Pembayaran:</label>
                                <input type="text" name="payment_method" value="<?php echo htmlspecialchars($row['payment_method']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="file">Unggah Struk:</label>
                                <input type="file" name="file">
                                <?php if ($row['file_path']): ?>
                                    <p><a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">Lihat File</a></p>
                                <?php else: ?>
                                    <p>Tidak ada file yang diunggah.</p>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-update">Simpan Perubahan</button>
                            <a href="detail.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-back">Batal</a>
                        </form>
                    </section>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo 'Data transaksi tidak ditemukan.';
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'ID tidak valid.';
}
?>