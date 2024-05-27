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
    $firstDayOfMonth = date('w', strtotime("$year-$month-01"));
    
    echo '<div class="table-responsive"><table class="table table-bordered table-custom">';
    echo '<thead class="thead-light"><tr>';
    echo '<th>Minggu</th><th>Senin</th><th>Selasa</th><th>Rabu</th><th>Kamis</th><th>Jumat</th><th>Sabtu</th>';
    echo '</tr></thead>';
    echo '<tbody><tr>';
    
    for ($i = 0; $i < $firstDayOfMonth; $i++) {
        echo '<td></td>';
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $currentDay = ($day + $firstDayOfMonth - 1) % 7;
        echo '<td class="calendar-day" onclick="showTransactions(' . $day . ')">' . $day;
        if (isset($calendar[$day])) {
            foreach ($calendar[$day] as $transaction) {
                echo '<br><span class="badge badge-' . ($transaction['type'] === 'pemasukan' ? 'success' : 'danger') . '">' . $transaction['type'] . ': ' . $transaction['amount'] . '</span>';
            }
        }
        echo '</td>';
        if ($currentDay == 6) {
            echo '</tr><tr>';
        }
    }
    
    $remainingDays = (7 - (($daysInMonth + $firstDayOfMonth) % 7)) % 7;
    for ($i = 0; $i < $remainingDays; $i++) {
        echo '<td></td>';
    }
    
    echo '</tr></tbody>';
    echo '</table></div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kalender Keuangan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        <?php include 'styles.css'; ?>
    </style>
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
    <style>
        .calendar-day {
            cursor: pointer;
            position: relative;
        }
        .calendar-day:hover {
            background-color: #f0f8ff;
        }
        .table-custom th, .table-custom td {
            vertical-align: middle;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="form-box">
            <h2>Kalender Keuangan Bulan Ini</h2>
            <form method="GET" action="calendar.php" class="form-inline mb-4">
                <label for="month" class="mr-2">Pilih Bulan:</label>
                <select id="month" name="month" class="form-control mr-2" onchange="this.form.submit()">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php if ($m == $current_month) echo 'selected'; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <label for="year" class="mr-2">Pilih Tahun:</label>
                <select id="year" name="year" class="form-control" onchange="this.form.submit()">
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
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
