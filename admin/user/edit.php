<?php 
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
require '../../database/db.php';
$id = $_GET['id'];
$user = $pdo->query("SELECT * FROM User WHERE id_user = $id")->fetch(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("UPDATE User SET name = ?, surname = ?, email = ?, phone = ?, role = ? WHERE id_user = ?");
    $stmt->execute([
        $_POST['name'],
        $_POST['surname'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['role'],
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
    <title>Редактировать пользователя</title>
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
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../bookings/index.php">Bookings</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../brand/index.php">Brand</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../categories/index.php">Categories</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a active" href="index.php">User</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../products/index.php">Products</a></div>
            <div class="adminitems-cont"><a class="adminitems-cont-a" href="../bookings/index.php">Bookings</a></div>
        </div>
        <div class="admin-dashboard">
            <h2 class="dashboard-title">Редактировать пользователя</h2>
            <form method="post">
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required><br>
                <input type="text" name="surname" value="<?= htmlspecialchars($user['surname']) ?>" required><br>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required><br>
                <select name="role" required>
                    <option value="0" <?= $user['role'] == 0 ? 'selected' : '' ?>>Пользователь</option>
                    <option value="1" <?= $user['role'] == 1 ? 'selected' : '' ?>>Админ</option>
                </select><br>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>