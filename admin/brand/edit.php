<?php 
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
require '../../database/db.php';
$id = $_GET['id'];
$brand = $pdo->query("SELECT * FROM Brand WHERE id_brand = $id")->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("UPDATE Brand SET name = ? WHERE id_brand = ?");
    $stmt->execute([$_POST['name'], $id]);
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>Редактировать бренд</title>
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
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../bookings/index.php">Bookings</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a active" href="index.php">Brand</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../categories/index.php">Categories</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../color/index.php">Color</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../products/index.php">Products</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../user/index.php">User</a></div>
        </div>
        <div class="admin-dashboard">
            <h2 class="dashboard-title">Редактировать бренд</h2>
            <form method="post">
                <input type="text" name="name" value="<?= htmlspecialchars($brand['name']) ?>" required><br>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>