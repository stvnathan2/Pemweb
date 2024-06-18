<?php
include 'koneksiuser.php';

$conn = connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $date = $_POST['date'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $payment_method = $_POST['payment_method'];

    $stmt = $conn->prepare('UPDATE daily_expenses SET date = ?, type = ?, category = ?, amount = ?, description = ?, payment_method = ? WHERE id = ?');
    $stmt->bind_param('sssdssi', $date, $type, $category, $amount, $description, $payment_method, $id);

    if ($stmt->execute()) {
        header("Location: detail.php?id=" . $id);
        exit();
    } else {
        echo 'Gagal menyimpan perubahan.';
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: edit.php?id=" . $id); 
    exit();
}
?>
