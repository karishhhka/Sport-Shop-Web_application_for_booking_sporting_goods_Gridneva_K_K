<?php 
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
require '../../database/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO User (name, surname, password, email, phone, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([
        $_POST['name'],
        $_POST['surname'],
        $password,
        $_POST['email'],
        $_POST['phone'],
        $_POST['role']
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
    <title>Добавить пользователя</title>
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
            <h2 class="dashboard-title">Добавить пользователя</h2>
            <form method="post">
                <input type="text" name="name" placeholder="Имя" required><br>
                <input type="text" name="surname" placeholder="Фамилия" required><br>
                <input type="password" name="password" placeholder="Пароль" required><br>
                <input type="email" name="email" placeholder="Email" required><br>
                <input type="text" name="phone" placeholder="Телефон" required><br>
                <select name="role" required>
                    <option value="">Выберите роль</option>
                    <option value="0">Пользователь</option>
                    <option value="1">Админ</option>
                </select><br>
                <button type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>