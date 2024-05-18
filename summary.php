<?php
include("conn.php");
$conn = connection();

$expenses = [];
$pieData = [];
$totalExpenses = 0;
$totalIncome = 0;
$averageDailyExpenses = 0;
$highestExpenseCategory = "";
$highestExpenseAmount = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $month = $_POST["month"];
    $year = $_POST["year"];
    
    $sql = "SELECT category, SUM(amount) as total FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 'pengeluaran' GROUP BY category";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $pieData = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    $sql = "SELECT * FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 'pengeluaran'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $month, $year);
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
    }
    
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $averageDailyExpenses = $totalExpenses / $daysInMonth;

    $sql = "SELECT SUM(amount) as total FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 'pemasukan'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $income = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($income as $row) {
        $totalIncome += $row['total'];
    }
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
                title: 'Pengeluaran Bulanan Berdasarkan Kategori',
                is3D: false
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .table-container {
            margin-top: 20px;
        }
        #piechart {
            width: 100%;
            height: 500px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="my-4">Daily Expenses</h2>
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
            <div id="piechart"></div>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Pemasukan</h5>
                            <p class="card-text">Rp <?php echo number_format($totalIncome, 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Pengeluaran</h5>
                            <p class="card-text">Rp <?php echo number_format($totalExpenses, 2); ?></p>
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
            </div>
            <div class="table-container">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Payment Method</th>
                            <th>Account</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?php echo $expense["id"]; ?></td>
                                <td><?php echo $expense["amount"]; ?></td>
                                <td><?php echo $expense["type"]; ?></td>
                                <td><?php echo $expense["category"]; ?></td>
                                <td><?php echo date('d', strtotime($expense["date"])); ?></td>
                                <td><?php echo $expense["description"]; ?></td>
                                <td><?php echo $expense["payment_method"]; ?></td>
                                <td><?php echo $expense["account"]; ?></td>
                                <td><a href="detail.php?id=<?php echo $expense["id"]; ?>" class="btn btn-info btn-sm">Detail</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p>Tidak ada data ditemukan untuk bulan dan tahun yang dipilih.</p>
        <?php endif; ?>
    </div>
</body>
</html>
