<?php
session_start();
include "../database/db.php";

if (!isset($_SESSION['id_user'])) {
    die(json_encode(['error' => 'Требуется авторизация']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'] ?? null;
    $user_id = $_SESSION['id_user'];
    $quantity = 1;

    if (!$product_id) {
        echo json_encode(['error' => 'ID товара не указан']);
        exit();
    }

  
    $check_stmt = $pdo->prepare("SELECT stock FROM Products WHERE id_product = ?");
    $check_stmt->execute([$product_id]);
    $product = $check_stmt->fetch();

    if (!$product) {
        echo json_encode(['error' => 'Товар не найден']);
        exit();
    }

    if ((int)$product['stock'] >= $quantity) {
        $booking_stmt = $pdo->prepare("
            INSERT INTO Bookings (id_product, id_user, quantity)
            VALUES (?, ?, ?)
        ");

        if ($booking_stmt->execute([$product_id, $user_id, $quantity])) {
            $update_stmt = $pdo->prepare("
                UPDATE Products SET stock = stock - ? WHERE id_product = ?
            ");
            $update_stmt->execute([$quantity, $product_id]);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Ошибка при бронировании']);
        }
    } else {
        echo json_encode(['error' => 'Недостаточно товара на складе']);
    }
}
?>