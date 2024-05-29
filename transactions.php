<?php
include("conn.php");
$conn = connection();

if (isset($_GET['day']) && isset($_GET['month']) && isset($_GET['year'])) {
    $day = intval($_GET['day']);
    $month = intval($_GET['month']);
    $year = intval($_GET['year']);

    $stmt = $conn->prepare('SELECT * FROM daily_expenses WHERE DAY(date) = ? AND MONTH(date) = ? AND YEAR(date) = ?');
    $stmt->bind_param('iii', $day, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered">';
        echo '<thead class="thead-dark">';
        echo '<tr>';
        echo '<th>Deskripsi</th>';
        echo '<th>Jumlah</th>';
        echo '<th>Kategori</th>';
        echo '<th>Tipe</th>';
        echo '<th>Metode Pembayaran</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['description'] . '</td>';
            echo '<td>' . $row['amount'] . '</td>';
            echo '<td>' . $row['category'] . '</td>';
            echo '<td>' . $row['type'] . '</td>';
            echo '<td>' . $row['payment_method'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo 'Tidak ada transaksi untuk tanggal ini.';
    }
} else {
    echo 'Tanggal tidak valid.';
}
?>
