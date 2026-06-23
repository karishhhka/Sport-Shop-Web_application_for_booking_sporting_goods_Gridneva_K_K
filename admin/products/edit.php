<?php 
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
require '../../database/db.php';

$id = $_GET['id'];
$product = $pdo->query("SELECT p.*, b.name as brand, c.name as category, cl.name as color 
                         FROM Products p 
                         JOIN Brand b ON p.id_brand = b.id_brand
                         JOIN Categories c ON p.id_category = c.id_category
                         JOIN Color cl ON p.id_color = cl.id_color
                         WHERE p.id_product = $id")->fetch(PDO::FETCH_ASSOC);

$brands = $pdo->query("SELECT * FROM Brand")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT * FROM Categories")->fetchAll(PDO::FETCH_ASSOC);
$colors = $pdo->query("SELECT * FROM Color")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("UPDATE Products SET id_brand=?, id_category=?, name=?, price=?, id_color=?, stock=?, image_url=? WHERE id_product=?");
    $stmt->execute([
        $_POST['id_brand'],
        $_POST['id_category'],
        $_POST['name'],
        $_POST['price'],
        $_POST['id_color'],
        $_POST['stock'],
        $_POST['image_url'],
        $id
    ]);
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../../style.css">
    <title>Редактировать товар</title>
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
            <h2 class="dashboard-title">Редактировать товар</h2>
            <form method="post">
                <select name="id_brand" required>
                    <?php foreach ($brands as $b): ?>
                    <option value="<?=$b['id_brand']?>" <?=$product['id_brand'] == $b['id_brand'] ? 'selected' : ''?>><?=$b['name']?></option>
                    <?php endforeach; ?>
                </select><br>

                <select name="id_category" required>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?=$c['id_category']?>" <?=$product['id_category'] == $c['id_category'] ? 'selected' : ''?>><?=$c['name']?></option>
                    <?php endforeach; ?>
                </select><br>

                <select name="id_color" required>
                    <?php foreach ($colors as $cl): ?>
                    <option value="<?=$cl['id_color']?>" <?=$product['id_color'] == $cl['id_color'] ? 'selected' : ''?>><?=$cl['name']?></option>
                    <?php endforeach; ?>
                </select><br>

                <input type="text" name="name" value="<?=$product['name']?>" placeholder="Название" required><br>
                <input type="number" step="0.01" name="price" value="<?=$product['price']?>" placeholder="Цена" required><br>
                <input type="number" name="stock" value="<?=$product['stock']?>" placeholder="На складе" required><br>
                <input type="text" name="image_url" value="<?=$product['image_url']?>" placeholder="URL изображения" required><br>
                <button type="submit">Сохранить</button>
            </form>
        </div>
       
    </div>
</div>
</body>
</html>