<?php 
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
require '../../database/db.php';
$users = $pdo->query("SELECT * FROM User")->fetchAll(PDO::FETCH_ASSOC);
$products = $pdo->query("SELECT * FROM Products")->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO Bookings (id_product, id_user, quantity, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['id_product'],
        $_POST['id_user'],
        $_POST['quantity'],
        $_POST['status']
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
    <title>Добавить бронирование</title>
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
            <a href="../../admin/index.php" class="infopole-text">Админ-панель</a>
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
            <h2 class="dashboard-title">Добавить бронирование</h2>
            <form method="post">
                <select name="id_user" required>
                    <option value="">Пользователь</option>
                    <?php foreach($users as $u): ?>
                    <option value="<?= $u['id_user'] ?>"><?= $u['name'] ?></option>
                    <?php endforeach; ?>
                </select><br>
                <select name="id_product" required>
                    <option value="">Товар</option>
                    <?php foreach($products as $p): ?>
                    <option value="<?= $p['id_product'] ?>"><?= $p['name'] ?></option>
                    <?php endforeach; ?>
                </select><br>
                <input type="number" name="quantity" placeholder="Количество" min="1" required><br>
                <select name="status" required>
                    <option value="в ожидании">в ожидании</option>
                    <option value="подтверждено">подтверждено</option>
                    <option value="отменено">отменено</option>
                </select><br>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>