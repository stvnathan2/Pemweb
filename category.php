<?php
session_start();
include("koneksiuser.php");
$conn = connection();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$selected_month = isset($_GET['month']) ? $_GET['month'] : date('n');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';

$username = $_SESSION['username'];
$expenses = [];
$message = "";

if ($selected_category) {
    $sql = "SELECT * FROM daily_expenses WHERE category = ? AND MONTH(date) = ? AND YEAR(date) = ? AND username = ? LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("siisii", $selected_category, $selected_month, $selected_year, $username, $limit, $offset);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $expenses = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            $message = "Terjadi kesalahan saat mengambil data.";
        }
    } else {
        $message = "Terjadi kesalahan dalam menyiapkan statement.";
    }
} else {
    $sql = "SELECT * FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND username = ? LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iisii", $selected_month, $selected_year, $username, $limit, $offset);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $expenses = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            $message = "Terjadi kesalahan saat mengambil data.";
        }
    } else {
        $message = "Terjadi kesalahan dalam menyiapkan statement.";
    }
}

if ($selected_category) {
    $total_sql = "SELECT COUNT(*) as count FROM daily_expenses WHERE category = ? AND MONTH(date) = ? AND YEAR(date) = ? AND username = ?";
    $stmt = $conn->prepare($total_sql);
    $stmt->bind_param("siis", $selected_category, $selected_month, $selected_year, $username);
} else {
    $total_sql = "SELECT COUNT(*) as count FROM daily_expenses WHERE MONTH(date) = ? AND YEAR(date) = ? AND username = ?";
    $stmt = $conn->prepare($total_sql);
    $stmt->bind_param("iis", $selected_month, $selected_year, $username);
}
$stmt->execute();
$result = $stmt->get_result();
$total_records = $result->fetch_assoc()['count'];
$total_pages = ceil($total_records / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Pengeluaran</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="category.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
    </style>
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

    <div class="container">
        <h2 class="my-4">Kategori Pengeluaran</h2>

        <?php if ($message): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="get" action="category.php" class="form-inline mb-4">
        <input type="hidden" name="month" value="<?php echo $selected_month; ?>">
        <input type="hidden" name="year" value="<?php echo $selected_year; ?>">
        <div class="form-group mr-2">
            <label for="category" class="mr-2">Pilih Kategori:</label>
            <select name="category" id="category" class="form-control" required>
                <option value="">...</option>
                <option value="rumah" <?php echo ($selected_category == 'rumah') ? 'selected' : ''; ?>>Rumah</option>
                <option value="transportasi" <?php echo ($selected_category == 'transportasi') ? 'selected' : ''; ?>>Transportasi</option>
                <option value="Makan" <?php echo ($selected_category == 'Makan') ? 'selected' : ''; ?>>Makan</option>
                <option value="Kesehatan" <?php echo ($selected_category == 'Kesehatan') ? 'selected' : ''; ?>>Kesehatan</option>
                <option value="Pendidikan" <?php echo ($selected_category == 'Pendidikan') ? 'selected' : ''; ?>>Pendidikan</option>
                <option value="Hiburan" <?php echo ($selected_category == 'Hiburan') ? 'selected' : ''; ?>>Hiburan</option>
                <option value="Pakaian" <?php echo ($selected_category == 'Pakaian') ? 'selected' : ''; ?>>Pakaian</option>
                <option value="Komunikasi" <?php echo ($selected_category == 'Komunikasi') ? 'selected' : ''; ?>>Komunikasi</option>
                <option value="Keuangan" <?php echo ($selected_category == 'Keuangan') ? 'selected' : ''; ?>>Keuangan</option>
                <option value="Gaji" <?php echo ($selected_category == 'Gaji') ? 'selected' : ''; ?>>Gaji</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mr-2">Tampilkan</button>
        <a href="category.php?month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>" class="btn btn-info">Tampilkan Semua Data</a>
        <a href="summary.php" class="btn btn-secondary ml-2">Back</a>
        </form>


        <div class="table-container">
            <?php if (!empty($expenses)): ?>
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Metode Pembayaran</th>
                            <th>Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense["date"]); ?></td>
                                <td><?php echo htmlspecialchars($expense["type"]); ?></td>
                                <td><?php echo htmlspecialchars($expense["amount"]); ?></td>
                                <td><?php echo htmlspecialchars($expense["category"]); ?></td>
                                <td><?php echo htmlspecialchars($expense["description"]); ?></td>
                                <td><?php echo htmlspecialchars($expense["payment_method"]); ?></td>
                                <td><?php echo htmlspecialchars($expense["account"]); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&category=<?php echo $selected_category; ?>&month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>">Previous</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $selected_category; ?>&month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>"><?php echo $i; ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&category=<?php echo $selected_category; ?>&month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "GET" && empty($selected_category)): ?>
                <p>Belum ada data untuk bulan dan tahun ini.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
