<?php
include("conn.php");
$conn = connection();

function getTransactions($conn, $month, $year) {
    $stmt = $conn->prepare('SELECT date, type, amount FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ?');
    $stmt->bind_param('ii', $month, $year);
    $stmt->execute();
    return $stmt->get_result();
}

$current_month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$current_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$transactions = getTransactions($conn, $current_month, $current_year);

$calendar = [];
while ($row = $transactions->fetch_assoc()) {
    $day = date('j', strtotime($row['date']));
    $calendar[$day][] = $row;
}

function generateCalendar($calendar, $month, $year) {
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    echo '<table border="1">';
    echo '<tr>';
    for ($day = 1; $day <= $daysInMonth; $day++) {
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
            var year = document.getElementById('year').value;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'transactions.php?day=' + day + '&month=' + month + '&year=' + year, true);
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
        <label for="year">Pilih Tahun:</label>
        <select id="year" name="year" onchange="this.form.submit()">
            <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                <option value="<?php echo $y; ?>" <?php if ($y == $current_year) echo 'selected'; ?>>
                    <?php echo $y; ?>
                </option>
            <?php endfor; ?>
        </select>
    </form>
    <?php generateCalendar($calendar, $current_month, $current_year); ?>
    <h3>Detail Transaksi</h3>
    <div id="transactions">Klik pada tanggal untuk melihat detail transaksi.</div>
</body>
</html>
