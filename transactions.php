<?php
include("conn.php");
$conn = connection();

if (isset($_GET['day'], $_GET['month'], $_GET['year'])) {
    $day = intval($_GET['day']);
    $month = intval($_GET['month']);
    $year = intval($_GET['year']);

    $date = "$year-$month-$day";
    $stmt = $conn->prepare('SELECT type, amount, description FROM daily_expenses WHERE date = ?');
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h4>Transaksi pada tanggal $date</h4>";
    if ($result->num_rows > 0) {
        echo "<ul class='list-group'>";
        while ($row = $result->fetch_assoc()) {
            echo "<li class='list-group-item'>" . ucfirst($row['type']) . ": Rp" . number_format($row['amount'], 2) . "<br>" . $row['description'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Tidak ada transaksi pada tanggal ini.</p>";
    }
} else {
    echo "<p>Data tidak valid.</p>";
}
?>
