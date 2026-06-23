<?php
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../index.html"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
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
                <a href="../admin/index.php" class="infopole-text">Админ-панель</a>
                <p class="infopole-text">-</p>
                <a href="../index.html" class="infopole-text">Главная</a>
            </div>
        </div>
        <div class="admin-info">
        <div class="adminitems">
                <div class="adminitems-cont"> 
                    <a class="adminitems-cont-a" href="bookings/index.php">Bookings</a>
                </div>
                <div class="adminitems-cont">
                    <a class="adminitems-cont-a" href="brand/index.php">Brand</a>
                </div>
                <div class="adminitems-cont">
                    <a class="adminitems-cont-a" href="categories/index.php">Categories</a>
                </div>
                <div class="adminitems-cont">
                    <a class="adminitems-cont-a" href="color/index.php">Color</a>
                </div>
                <div class="adminitems-cont">
                    <a class="adminitems-cont-a" href="products/index.php">Products</a>
                </div>
                <div class="adminitems-cont">
                    <a class="adminitems-cont-a" href="user/index.php">User</a>
                </div>
                <div class="adminitems-cont"><a class="adminitems-cont-a" href="../logout.php">Выйти</a></div>
            </div>
            <div class="admin-dashboard">
              <h2 class="dashboard-title">Статистика</h2>

              <div class="dashboard-grid">
                <div class="charts-row">
                  <div class="cards">
                    <h3>Новые пользователи</h3>
                    <canvas id="usersChart"  height="200"></canvas>
                  </div>

                  <div class="cards">
                    <h3>Бронирования</h3>
                    <canvas id="bookingsChart"  height="200"></canvas>
                  </div>

                  <div class="cards">
                    <h3>Товары по брендам</h3>
                    <canvas id="productsByBrandChart" height="200"></canvas>
                  </div>
                </div>

                <div class="stats-row">
                  <div class="cards stats">
                    <h3>Всего пользователей</h3>
                    <p id="totalUsers" class="stat-number">...</p>
                  </div>

                  <div class="cards stats">
                    <h3>Всего товаров</h3>
                    <p id="totalProducts" class="stat-number">...</p>
                  </div>

                  <div class="cards stats">
                    <h3>Активные бронирования</h3>
                    <p id="activeBookings" class="stat-number">...</p>
                  </div>
                </div>
              </div>
            </div>
        </div>
   </div>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script src="../js/dashboard.js"></script>
</body>
</html>