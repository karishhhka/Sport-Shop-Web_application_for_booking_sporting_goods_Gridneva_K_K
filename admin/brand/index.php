<?php
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
require '../../database/db.php';
 $brands = $pdo->query("SELECT * FROM Brand")->fetchAll(PDO::FETCH_ASSOC); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>Админ панель</title>
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
                <a href="../adminpanel/adminka.php" class="infopole-text">Админ-панель</a>
                <p class="infopole-text">-</p>
                <a href="../index.html" class="infopole-text">Главная</a>
            </div>
        </div>
        <div class="admin-info">
        <div class="adminitems">
            <div class="adminitems-cont"><a class="adminitems-cont-a active" href="../bookings/index.php">Bookings</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../brand/index.php">Brand</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../categories/index.php">Categories</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../color/index.php">Color</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../products/index.php">Products</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../user/index.php">User</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../logout.php">Выйти</a></div>
        </div>
            <div class="admin-dashboard">
              <h2 class="dashboard-title">Бренды</h2>
              <a href="add.php">Добавить бренд</a>
                <table class="table">
                    <tr><th>ID</th><th>Название</th><th>Действия</th></tr>
                    <?php foreach($brands as $b): ?>
                    <tr>
                        <td><?=$b['id_brand']?></td>
                        <td><?=$b['name']?></td>
                        <td>
                            <a href="edit.php?id=<?=$b['id_brand']?>">Редактировать</a>
                            <a href="delete.php?id=<?=$b['id_brand']?>" onclick="return confirm('Удалить?')">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
   </div>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script src="../js/dashboard.js"></script>
</body>
</html>