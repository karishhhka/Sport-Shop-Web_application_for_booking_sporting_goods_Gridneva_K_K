<?php
include "../database/db.php"; 

if (!isset($pdo)) {
    die(json_encode(['error' => 'Ошибка: объект PDO не создан.']));
}

$filters = [];
$params = [];

$sql = "SELECT p.id_product, p.name AS product_name, b.name AS brand_name, 
               c.name AS category_name, co.name AS color_name, 
               p.price, p.image_url
        FROM Products p
        JOIN Brand b ON p.id_brand = b.id_brand
        JOIN Categories c ON p.id_category = c.id_category
        JOIN Color co ON p.id_color = co.id_color";

$where = [];

// Фильтр по бренду
if (isset($_POST['brand']) && !empty($_POST['brand'])) {
    $where[] = "b.name = :brand";
    $params[':brand'] = $_POST['brand'];
}

// Фильтр по категории
if (isset($_POST['category']) && !empty($_POST['category'])) {
    $where[] = "c.name = :category";
    $params[':category'] = $_POST['category'];
}

// Фильтр по цвету
if (isset($_POST['color']) && !empty($_POST['color'])) {
    $where[] = "co.name = :color";
    $params[':color'] = $_POST['color'];
}

// Фильтр по цене (диапазон)
if (isset($_POST['min_price']) && isset($_POST['max_price'])) {
    $where[] = "p.price BETWEEN :min_price AND :max_price";
    $params[':min_price'] = $_POST['min_price'];
    $params[':max_price'] = $_POST['max_price'];
}

// Поиск по названию
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $where[] = "p.name LIKE :search";
    $params[':search'] = '%' . $_POST['search'] . '%';
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
?>