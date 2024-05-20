<?php
include("conn.php");
$conn = connection();

$day = isset($_GET['day']) ? intval($_GET['day']) : 0;
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

if ($day === 0 || $month === 0) {
    exit('Invalid date');
}

function getTransactionsByDay($conn, $day, $month) {
    $stmt = $conn->prepare('SELECT date, type, amount, category, description, payment_method, account FROM daily_expenses WHERE DAY(date) = ? AND MONTH(date) = ?');
    $stmt->bind_param('ii', $day, $month);
    $stmt->execute();
    return $stmt->get_result();
}

$transactions = getTransactionsByDay($conn, $day, $month);

if ($transactions->num_rows > 0) {
    echo '<table border="1">';
    echo '<tr><th>Tanggal</th><th>Tipe</th><th>Jumlah</th><th>Kategori</th><th>Deskripsi</th><th>Metode Pembayaran</th><th>Akun</th></tr>';
    while ($row = $transactions->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['date'] . '</td>';
        echo '<td>' . $row['type'] . '</td>';
        echo '<td>' . $row['amount'] . '</td>';
        echo '<td>' . $row['category'] . '</td>';
        echo '<td>' . $row['description'] . '</td>';
        echo '<td>' . $row['payment_method'] . '</td>';
        echo '<td>' . $row['account'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo 'Tidak ada transaksi pada tanggal ini.';
}
?>
