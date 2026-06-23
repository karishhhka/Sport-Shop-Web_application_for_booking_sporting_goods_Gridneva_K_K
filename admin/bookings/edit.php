<?php
session_start();


if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
} 
require '../../database/db.php';
$id = $_GET['id'];

$booking = $pdo->query("SELECT * FROM Bookings WHERE id_booking = $id")->fetch(PDO::FETCH_ASSOC);

$users = $pdo->query("SELECT id_user, name, surname FROM User")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT id_product, name FROM Products")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("UPDATE Bookings SET id_product = ?, id_user = ?, quantity = ?, status = ? WHERE id_booking = ?");
    $stmt->execute([
        $_POST['id_product'],
        $_POST['id_user'],
        $_POST['quantity'],
        $_POST['status'],
        $id
    ]);
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>Редактировать бронирование</title>
</head>
<body>
<div class="container">
    <header>
        <div class="adpanel">
            <h1 class="adpanel-h1">Админ-панель</h1>
        </div>
    </header>
    <div class="infopole">
        <div class="infopole-blok"> 
            <a href="../../adminpanel/adminka.php" class="infopole-text">Админ-панель</a>
            <p class="infopole-text">-</p>
            <a href="../../index.html" class="infopole-text">Главная</a>
        </div>
    </div>
    <div class="admin-info">
        <div class="adminitems">
            <div class="adminitems-cont"><a class="adminitems-cont-a active" href="index.php">Bookings</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../brand/index.php">Brand</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../categories/index.php">Categories</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../color/index.php">Color</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../products/index.php">Products</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../user/index.php">User</a></div>
        </div>
        <div class="admin-dashboard">
            <h2 class="dashboard-title">Редактировать бронирование</h2>
            <form method="post">
                <select name="id_user" required>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id_user'] ?>" <?= $booking['id_user'] == $u['id_user'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name'] . ' ' . $u['surname']) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>
                <select name="id_product" required>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id_product'] ?>" <?= $booking['id_product'] == $p['id_product'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>
                <input type="number" name="quantity" value="<?= $booking['quantity'] ?>" min="1" required><br>

                <select name="status" required>
                    <option value="в ожидании" <?= $booking['status'] == 'в ожидании' ? 'selected' : '' ?>>в ожидании</option>
                    <option value="подтверждено" <?= $booking['status'] == 'подтверждено' ? 'selected' : '' ?>>подтверждено</option>
                    <option value="отменено" <?= $booking['status'] == 'отменено' ? 'selected' : '' ?>>отменено</option>
                </select><br>

                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>