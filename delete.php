<?php
include 'koneksiuser.php';

$conn = connection();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare('DELETE FROM daily_expenses WHERE id = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Hapus Transaksi</title>
            <link rel="icon" type="image/x-icon" href="money.png">
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
                        <h2>Hapus Transaksi</h2>
                        <p>Transaksi telah berhasil dihapus.</p>
                        <a href="calendar.php" class="btn-back">Kembali ke Kalender</a>
                    </section>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo 'Gagal menghapus transaksi.';
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'ID tidak valid.';
}
?>
