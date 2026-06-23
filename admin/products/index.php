<?php 
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
require '../../database/db.php';
$stmt = $pdo->query("SELECT p.*, b.name as brand, c.name as category, cl.name as color 
                     FROM Products p 
                     JOIN Brand b ON p.id_brand = b.id_brand
                     JOIN Categories c ON p.id_category = c.id_category
                     JOIN Color cl ON p.id_color = cl.id_color");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>Товары</title>
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
            <a href="../index.html" class="infopole-text">Главная</a>
        </div>
    </div>
    <div class="admin-info">
        <div class="adminitems">
            <!-- Ссылки на разделы -->
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../bookings/index.php">Bookings</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../brand/index.php">Brand</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../categories/index.php">Categories</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../color/index.php">Color</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a active" href="index.php">Products</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../user/index.php">User</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../logout.php">Выйти</a></div>
        </div>
        <div class="admin-dashboard">
            <h2 class="dashboard-title">Товары</h2>
            <a href="add.php" class="btn-add">Добавить товар</a>
            <table class="table">
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Бренд</th>
                    <th>Категория</th>
                    <th>Цвет</th>
                    <th>Цена</th>
                    <th>На складе</th>
                    <th>Дата создания</th>
                    <th>Действия</th>
                </tr>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?=$p['id_product']?></td>
                    <td><?=$p['name']?></td>
                    <td><?=$p['brand']?></td>
                    <td><?=$p['category']?></td>
                    <td><?=$p['color']?></td>
                    <td><?=$p['price']?> ₽</td>
                    <td><?=$p['stock']?></td>
                    <td><?=$p['created_at']?></td>
                    <td>
                        <a href="edit.php?id=<?=$p['id_product']?>" class="btn-edit">Редактировать</a>
                        <a href="delete.php?id=<?=$p['id_product']?>" onclick="return confirm('Удалить?')" class="btn-delete">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>