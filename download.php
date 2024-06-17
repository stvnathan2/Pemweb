<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("koneksiuser.php");
$conn = connection();
$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $month = $_GET["month"];
    $year = $_GET["year"];

    $sql = "SELECT category, SUM(amount) as total FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND type = 'pengeluaran' AND username = ? GROUP BY category";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $month, $year, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $pieData = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $totalExpenses = 0;
    $highestExpenseCategory = "";
    $lowestExpenseCategory = "";
    $highestExpenseAmount = 0;
    $lowestExpenseAmount = 10000000000000;

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

    $totalIncome = 0;
    foreach ($income as $row) {
        $totalIncome += $row['total'];
    }
    $difference = $totalIncome - $totalExpenses;

    $sql = "SELECT date, type, amount, category, account, description FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND username = ? ORDER BY date";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $month, $year, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="expenses_analysis_' . $month . '_' . $year . '.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['Analisis Pengeluaran']);
    fputcsv($output, ['Kategori', 'Total Pengeluaran']);
    foreach ($pieData as $row) {
        fputcsv($output, [$row['category'], $row['total']]);
    }

    fputcsv($output, []);
    fputcsv($output, ['Total Pemasukan', $totalIncome]);
    fputcsv($output, ['Total Pengeluaran', $totalExpenses]);
    fputcsv($output, ['Selisih', $difference]);
    fputcsv($output, ['Rata-rata Pengeluaran Harian', $averageDailyExpenses]);
    fputcsv($output, ['Kategori Pengeluaran Terbesar', $highestExpenseCategory . ': ' . $highestExpenseAmount]);
    fputcsv($output, ['Kategori Pengeluaran Terkecil', $lowestExpenseCategory . ': ' . $lowestExpenseAmount]);

    fputcsv($output, []);
    fputcsv($output, ['Data Pengeluaran Lengkap']);
    fputcsv($output, ['Tanggal', 'Tipe', 'Jumlah', 'Kategori', 'Akun', 'Deskripsi']);

    foreach ($expenses as $row) {
        fputcsv($output, [$row['date'], $row['type'], $row['amount'], $row['category'], $row['account'], $row['description']]);
    }

    fclose($output);
    exit();
}

$conn->close();
?>
