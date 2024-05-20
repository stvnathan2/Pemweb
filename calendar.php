<?php
include("conn.php");
$conn = connection();

function getTransactions($conn, $month) {
    $stmt = $conn->prepare('SELECT date, type, amount FROM daily_expenses WHERE MONTH(date) = ?');
    $stmt->bind_param('i', $month);
    $stmt->execute();
    return $stmt->get_result();
}

$current_month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$transactions = getTransactions($conn, $current_month);

$calendar = [];
while ($row = $transactions->fetch_assoc()) {
    $day = date('j', strtotime($row['date']));
    $calendar[$day][] = $row;
}

function generateCalendar($calendar) {
    echo '<table border="1">';
    echo '<tr>';
    for ($day = 1; $day <= 31; $day++) {
        echo '<td onclick="showTransactions(' . $day . ')">' . $day;
        if (isset($calendar[$day])) {
            foreach ($calendar[$day] as $transaction) {
                echo '<br>' . $transaction['type'] . ': ' . $transaction['amount'];
            }
        }
        echo '</td>';
        if ($day % 7 == 0) {
            echo '</tr><tr>';
        }
    }
    echo '</tr>';
    echo '</table>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kalender Keuangan</title>
    <script>
        function showTransactions(day) {
            var month = document.getElementById('month').value;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'transactions.php?day=' + day + '&month=' + month, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('transactions').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>
    <h2>Kalender Keuangan Bulan Ini</h2>
    <form method="GET" action="calendar.php">
        <label for="month">Pilih Bulan:</label>
        <select id="month" name="month" onchange="this.form.submit()">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php if ($m == $current_month) echo 'selected'; ?>>
                    <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                </option>
            <?php endfor; ?>
        </select>
    </form>
    <?php generateCalendar($calendar); ?>
    <h3>Detail Transaksi</h3>
    <div id="transactions">Klik pada tanggal untuk melihat detail transaksi.</div>
</body>
</html>
