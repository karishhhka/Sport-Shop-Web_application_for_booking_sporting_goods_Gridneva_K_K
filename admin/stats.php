<?php
include "../database/db.php"; 

header('Content-Type: application/json');

// Всего пользователей
$stmt = $pdo->query("SELECT COUNT(*) FROM User");
$totalUsers = $stmt->fetchColumn();

// Всего товаров
$stmt = $pdo->query("SELECT COUNT(*) FROM Products");
$totalProducts = $stmt->fetchColumn();

// Активные заказы
$stmt = $pdo->query("SELECT COUNT(*) FROM Bookings WHERE status = 'в ожидании'");
$activeBookings = $stmt->fetchColumn();

// Новые пользователи по месяцам
$stmt = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS count 
    FROM User 
    GROUP BY month 
    ORDER BY month DESC LIMIT 6
");
$usersByMonth = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Бронирования по месяцам
$stmt = $pdo->query("
    SELECT DATE_FORMAT(booking_date, '%Y-%m') AS month, COUNT(*) AS count 
    FROM Bookings 
    GROUP BY month 
    ORDER BY month DESC LIMIT 6
");
$bookingsByMonth = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Товары по брендам
$stmt = $pdo->query("
    SELECT b.name AS brand, COUNT(*) AS count 
    FROM Products p 
    JOIN Brand b ON p.id_brand = b.id_brand 
    GROUP BY b.id_brand
");
$productsByBrand = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'totalUsers' => $totalUsers,
    'totalProducts' => $totalProducts,
    'activeBookings' => $activeBookings,
    'usersByMonth' => array_reverse($usersByMonth),
    'bookingsByMonth' => array_reverse($bookingsByMonth),
    'productsByBrand' => $productsByBrand
]);