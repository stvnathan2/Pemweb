<?php
include("conn.php");
$conn = connection();

$selected_category = isset($_POST['category']) ? $_POST['category'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$expenses = [];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $selected_category) {
    $sql = "SELECT * FROM daily_expenses WHERE category = ? LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sii", $selected_category, $limit, $offset);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $expenses = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            if (empty($expenses)) {
                $message = "Belum ada yang pakai kategori ini.";
            }
        } else {
            $message = "Terjadi kesalahan saat mengambil data.";
        }
    } else {
        $message = "Terjadi kesalahan dalam menyiapkan statement.";
    }
} else {
    $sql = "SELECT * FROM daily_expenses LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ii", $limit, $offset);
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

$total_sql = $selected_category ? "SELECT COUNT(*) as count FROM daily_expenses WHERE category = ?" : "SELECT COUNT(*) as count FROM daily_expenses";
$stmt = $conn->prepare($total_sql);
if ($selected_category) {
    $stmt->bind_param("s", $selected_category);
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
    <div class="container">
        <h2 class="my-4">Kategori Pengeluaran</h2>

        <?php if ($message): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="category.php" class="form-inline mb-4">
            <div class="form-group mr-2">
                <label for="category" class="mr-2">Pilih Kategori:</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="">.....</option>
                    <option value="Rumah">Rumah</option>
                    <option value="Transportasi">Transportasi</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Kesehatan">Kesehatan</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Hiburan">Hiburan</option>
                    <option value="Pakaian">Pakaian</option>
                    <option value="Komunikasi">Komunikasi</option>
                    <option value="Keuangan">Keuangan</option>
                    <option value="Darurat">Darurat</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>

        <form method="post" action="category.php" class="form-inline mb-4">
            <button type="submit" class="btn btn-info mr-2">Tampilkan Semua Kategori</button>
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
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <p>Belum ada yang pakai kategori ini.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
