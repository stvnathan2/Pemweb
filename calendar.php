<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include("koneksiuser.php");
$conn = connection();

function getTransactions($conn, $month, $year) {
    $stmt = $conn->prepare('
        SELECT date, 
               SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as total_pemasukan,
               SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as total_pengeluaran
        FROM daily_expenses 
        WHERE MONTH(date) = ? AND YEAR(date) = ?
        GROUP BY date
    ');
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
    $calendar[$day] = [
        'total_pemasukan' => $row['total_pemasukan'],
        'total_pengeluaran' => $row['total_pengeluaran']
    ];
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
            $totalPemasukan = $calendar[$day]['total_pemasukan'];
            $totalPengeluaran = $calendar[$day]['total_pengeluaran'];
            echo '<br><span class="badge badge-success">Pemasukan: ' . $totalPemasukan . '</span>';
            echo '<br><span class="badge badge-danger">Pengeluaran: ' . $totalPengeluaran . '</span>';
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="calendar.css">
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
        <div class="form-box">
            <h2 class="cl-2">Kalender Keuangan Bulan Ini</h2>
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
            <h3 class="cl-2">Detail Transaksi</h3>
            <div id="transactions">Klik pada tanggal untuk melihat detail transaksi.</div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
