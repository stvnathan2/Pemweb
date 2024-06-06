<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("koneksiuser.php");
$conn = connection();
$username = $_SESSION['username'];
$expenses = [];
$pieData = [];
$totalExpenses = 0;
$totalIncome = 0;
$difference = 0;
$averageDailyExpenses = 0;
$highestExpenseCategory = "";
$lowestExpenseCategory = "";
$highestExpenseAmount = 0;
$lowestExpenseAmount = 10000000000000;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $month = $_POST["month"];
    $year = $_POST["year"];
    
    $sql = "SELECT category, SUM(amount) as total FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 'pengeluaran' AND username = ? GROUP BY category";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $month, $year, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $pieData = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    $sql = "SELECT * FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 'pengeluaran' AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $month, $year, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($pieData as $row) {
        $totalExpenses += $row['total'];
        if ($row['total'] > $highestExpenseAmount) {
            $highestExpenseAmount = $row['total'];
            $highestExpenseCategory = $row['category'];
        }
        if ($row['total'] < $lowestExpenseAmount) {
            $lowestExpenseAmount = $row['total'];
            $lowestExpenseCategory = $row['category'];
        }
    }
    
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $averageDailyExpenses = $totalExpenses / $daysInMonth;

    $sql = "SELECT SUM(amount) as total FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 'pemasukan' AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $month, $year, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $income = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($income as $row) {
        $totalIncome += $row['total'];
    }
    $difference = $totalIncome - $totalExpenses;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Expenses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawPieChart);

        function drawPieChart() {
            var data = google.visualization.arrayToDataTable([
                ['Category', 'Total'],
                <?php
                foreach ($pieData as $row) {
                    echo "['" . $row['category'] . "', " . $row['total'] . "],";
                }
                ?>
            ]);

            var options = {
                is3D: false,
                pieSliceText: 'percentage',
                height: 300,
                chartArea: {
                    top: 10,
                    bottom: 10,
                    width: '100%',
                    height: '100%'
                },
                legend: {
                    alignment: 'center',
                    position: 'right',
                    textStyle: {
                    fontSize: 25,
                    color: '#333'
                    }
                },
                pieStartAngle: 45,
                backgroundColor: 'transparent'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            google.visualization.events.addListener(chart, 'select', function() {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem) {
                    var category = data.getValue(selectedItem.row, 0);
                    window.location.href = 'category.php?category=' + encodeURIComponent(category) + '&month=<?php echo $month; ?>&year=<?php echo $year; ?>';
                }
            });

            chart.draw(data, options);
        }   
    </script>
</head>
<body>
    <header>
        <a href="home.php" class="logo">Money Mastery</a>
        <nav class="navigation">
            <a href="#tips">Tips</a>
            <a href="#konsultasi">Konsultasi</a>
            <a href="#tentang">Tentang</a>
            <a href="#bantuan">Bantuan</a>
        </nav>
    </header>
    
    <div class="container">
        <form method="post" action="summary.php" class="form-inline">
            <div class="form-group mb-2">
                <label for="month" class="mr-2">Bulan:</label>
                <select name="month" id="month" class="form-control mr-2" required>
                    <option value="">Pilih Bulan</option>
                    <?php
                    for ($m = 1; $m <= 12; $m++) {
                        echo '<option value="' . $m . '">' . date('F', mktime(0, 0, 0, $m, 10)) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="year" class="mr-2">Tahun:</label>
                <select name="year" id="year" class="form-control mr-2" required>
                    <option value="">Pilih Tahun</option>
                    <?php
                    for ($y = date("Y"); $y >= 2000; $y--) {
                        echo '<option value="' . $y . '">' . $y . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Tampilkan</button>
        </form>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($expenses)): ?>
            <div class="chart-container">
                <div id="piechart"></div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Pemasukan <i class="fa fa-arrow-down"></i></h5>
                            <p class="card-text">Rp <?php echo number_format($totalIncome, 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Pengeluaran <i class="fa fa-arrow-up"></i></h5>
                            <p class="card-text">Rp <?php echo number_format($totalExpenses, 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Selisih</h5>
                            <p class="card-text">Rp <?php echo number_format($difference, 2); ?> / <?php echo number_format($totalExpenses / $totalIncome * 100, 0); ?>% dari pemasukan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Rata-rata Pengeluaran Harian</h5>
                            <p class="card-text">Rp <?php echo number_format($averageDailyExpenses, 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Kategori Pengeluaran Terbesar</h5>
                            <p class="card-text"><?php echo $highestExpenseCategory; ?>: Rp <?php echo number_format($highestExpenseAmount, 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Kategori Pengeluaran Terkecil</h5>
                            <p class="card-text"><?php echo $lowestExpenseCategory; ?>: Rp <?php echo number_format($lowestExpenseAmount, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p>Tidak ada data ditemukan untuk bulan dan tahun yang dipilih.</p>
        <?php endif; ?>
    </div>
</body>
</html>
