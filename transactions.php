<?php
include("koneksiuser.php");
$conn = connection();

if (isset($_GET['day']) && isset($_GET['month']) && isset($_GET['year'])) {
    $day = intval($_GET['day']);
    $month = intval($_GET['month']);
    $year = intval($_GET['year']);

    $stmt = $conn->prepare('SELECT * FROM daily_expenses WHERE DAY(date) = ? AND MONTH(date) = ? AND YEAR(date) = ?');
    $stmt->bind_param('iii', $day, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Transactions</title>
        <link rel="stylesheet" href="calendar.css">
    </head>
    <body>
        <div class="container">
            <?php
            if ($result->num_rows > 0) {
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered">';
                echo '<thead class="thead-light">';
                echo '<tr>';
                echo '<th>Deskripsi</th>';
                echo '<th>Jumlah</th>';
                echo '<th>Kategori</th>';
                echo '<th>Tipe</th>';
                echo '<th>Metode Pembayaran</th>';
                echo '<th>Detail</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['description']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['amount']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['category']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['type']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['payment_method']) . '</td>';
                    echo '<td><a href="detail.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-primary btn-sm">Detail</a></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<div class="no-transactions">Tidak ada transaksi untuk tanggal ini.</div>';
            }
            ?>
        </div>
    </body>
    </html>
    <?php
} else {
    echo 'Tanggal tidak valid.';
}
?>
