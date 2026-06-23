<?php
session_start();


include "../database/db.php"; 

if (!isset($pdo)) {
    die(json_encode(['error' => 'Ошибка: объект PDO не создан.']));
}

// Получение списков для фильтров
$brands_stmt = $pdo->query("SELECT name FROM Brand");
$categories_stmt = $pdo->query("SELECT name FROM Categories");
$colors_stmt = $pdo->query("SELECT name FROM Color");

$brands = $brands_stmt->fetchAll(PDO::FETCH_COLUMN);
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);
$colors = $colors_stmt->fetchAll(PDO::FETCH_COLUMN);

// Базовый SQL-запрос
$sql = "SELECT p.id_product, p.name AS product_name, b.name AS brand_name, 
               c.name AS category_name, co.name AS color_name, 
               p.price, p.image_url
        FROM Products p
        JOIN Brand b ON p.id_brand = b.id_brand
        JOIN Categories c ON p.id_category = c.id_category
        JOIN Color co ON p.id_color = co.id_color";

$params = [];
$where = [];

// Используем GET вместо POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['brand'])) {
        $where[] = "b.name = :brand";
        $params[':brand'] = $_GET['brand'];
    }
    
    if (!empty($_GET['category'])) {
        $where[] = "c.name = :category";
        $params[':category'] = $_GET['category'];
    }
    
    if (!empty($_GET['color'])) {
        $where[] = "co.name = :color";
        $params[':color'] = $_GET['color'];
    }
    
    if (!empty($_GET['min_price']) && !empty($_GET['max_price'])) {
        $where[] = "p.price BETWEEN :min_price AND :max_price";
        $params[':min_price'] = $_GET['min_price'];
        $params[':max_price'] = $_GET['max_price'];
    }
    
    if (!empty($_GET['search'])) {
        $where[] = "p.name LIKE :search";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Каталог</title>
    
</head>
<body>
    <header>
        <div class="container">
            <div class="head">
            <nav class="menu">
                    <ul class="menu-top">
                    <a href="../index.html"><li class="menu-item"><img src="../img/SPORT STOPE.png" alt=""></li></a>
                        <li class="menu-items"><a class ="link" href="../index.html">Главная</a></li>
                        <li class="menu-items"><a class ="link" href="../catalog/index.php">Каталог</a></li>
                        <li class="menu-items"><a class ="link" href="../сompanyHistory/index.html">История компании</a></li>
                        <li class="menu-items"><a class ="link" href="../contacts/index.html">Контакты</a></li>
                        <div class="menu-it">
                            <li class="menu-it-el"><img id="open-reg-modal" src="../img/Customer.png" alt=""></li>
                            <div id="reg-modal" class="modal">
                              
                                <div class="modal-content">
                                    <span  id="close" class="close">&times;</span>
                                      <h2 class="modal_avtor">Регистрация</h2>
                                      <div id="error-container" style="color: red; margin-bottom: 10px;"></div> <!-- Контейнер для ошибок -->
                                      <form class="modal_form" action="/registration.php" method="post">
                                        <input class="input__form" type="text" placeholder="Имя:" name="name">
                                        <input class="input__form" type="text" placeholder="Фамилия:" name="surname">
                                        <input class="input__form" type="tel" placeholder="Номер телефона:" name="phone">
                                        <input class="input__form" type="email" placeholder="Почта:" name="email">
                                        <input class="input__form" type="password" placeholder="Пароль:" name="pass" >
                                        <input class="input__form" type="password" placeholder="Подтверждение пароля:" name="doppass" >
                                        <button class="modal__button" type="submit">Регистрация</button>
                                    </form>
                                  <button id="open-login-modal" class="open-login-modal">Уже есть аккаунт? Войдите</button>
                                </div>
                              </div>
                            </div>
                             <div id="login-modal" class="modal">
                                <div class="modal-content">
                                    <span id="closeTwo" class="close">&times;</span>
                                    <h2 class="modal_avtor">Авторизация</h2>
                                    
                                    <div id="auth-error-container" style="color: red; margin-bottom: 10px;"></div>
                                    
                                    <form id="login-form">
                                        <input class="input__form" type="email" placeholder="Почта" name="email" required>
                                        <input class="input__form" type="password" placeholder="Пароль:" name="password" required>
                                        <button class="modal__button" id="signIn">Вход</button>
                                    </form>
                                    
                                    <div id="qr-code-section" style="display: none; text-align: center;">
                                        <img id="qr-code" src="" alt="QR Code" style="max-width: 200px;">
                                        <input class="input__form" type="text" id="2fa-code" placeholder="Введите 6-значный код" maxlength="6" style="margin-top: 15px;">
                                        <button class="modal__button" id="verify-2fa-btn" style="margin-top: 10px;">Подтвердить</button>
                                    </div>
                                    
                                    <button id="open-inReg-modal" class="open-inReg-modal">Нет аккаунта? Зарегистрируйтесь</button>
                                </div>
                            </div>
                        </div>
                    </ul>  
                </nav> 
            </div>
        </div>
    </header>
    <div class="container">
        <div class="infopole">
            <div class="infopole-blok"> 
                <a href="../index.html" class="infopole-text">Главная</a>
                <p class="infopole-text">-</p>
                <a href="../catalog/index.html" class="infopole-text">Каталог</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="catalog">
            <h1 class="catalog_text">Каталог</h1>
            <div class="catalog_items">
            <div class="filter">
                    <p class="filter-header">Фильтры</p>
                    <form class="filter-form" id="filter-form" method="GET">
                        <label class="filter-form-text">Бренд:</label><br>
                        <select class="filter-form-input" name="brand">
                            <option value="">Все бренды</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= htmlspecialchars($brand) ?>" <?= ($_GET['brand'] ?? '') === $brand ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($brand) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label class="filter-form-text">Категория:</label><br>
                        <select class="filter-form-input" name="category">
                            <option value="">Все категории</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category) ?>" <?= ($_GET['category'] ?? '') === $category ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label class="filter-form-text">Цвет:</label><br>
                        <select class="filter-form-input" name="color">
                            <option value="">Все цвета</option>
                            <?php foreach ($colors as $color): ?>
                                <option value="<?= htmlspecialchars($color) ?>" <?= ($_GET['color'] ?? '') === $color ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($color) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label class="filter-form-text">Цена:</label><br>
                        <input class="filter-form-input" type="number" name="min_price" placeholder="Минимальная цена" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" />
                        <input class="filter-form-input" type="number" name="max_price" placeholder="Максимальная цена" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" />
                        <button class="filter-form-button" type="submit">Применить фильтры</button>
                    </form>
                </div>
                <div class="xz">
                <div class="form_poisk">
                    <form method="GET" action="">
                        <input class="poisk" type="text" name="search" placeholder="Искать здесь..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="poisk_btn" type="submit"><img src="../img/Search.png" alt="Поиск"></button>
                    </form>
                </div>
                <div class="products" id="products">
                    <?php if (empty($products)): ?>
                        <p>Товары не найдены.</p>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="products_card">
                                <img class="products_card_img" src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                                <p class="products_card_text"><?= htmlspecialchars($product['product_name']) ?></p>
                                <p class="products_card_two"><?= htmlspecialchars($product['brand_name']) ?></p>
                                <p class="products_card_price"><?= htmlspecialchars($product['price']) ?> ₽</p>
                                <button class="products_card_btn" data-id="<?= htmlspecialchars($product['id_product']) ?>">Бронировать</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>                   
            </div>
        </div>
    </div>
    <footer>
        <div class="container">
            <div class="foot">
                <div class="foot-items">
                    <div>
                        <img class="foot-items-img" src="../img/SPORT STOPEfoot.png" alt="">
                    </div>
                    <div class="foot-items-link">
                        <a class="foot-items-links-text" href="../index.html">Главная</a>
                        <a class="foot-items-links-text" href="../catalog/index.php">Каталог</a>
                    </div>
                    <div class="foot-items-links">
                        <a class="foot-items-links-text" href="../contacts/index.html">Контакты</a>
                        <a class="foot-items-links-text" href="../сompanyHistory/index.html">Компания</a>
                    </div>
                    <div class="foot-items-links">
                        <a class="foot-items-links-text" href="../catalog/index.php">Товары</a>
                        <a class="foot-items-links-text" href="../index.html">Обратная связь</a>
                    </div>
                    <div class="foot-items-icons">
                       <a href="https://vk.com/kgridneva4"><img  class="foot-items-icons-img" src="../img/VK com.png" alt=""></a>
                        <a href="https://vk.com/kgridneva4"><img class="foot-items-icons-img" src="../img/Telegram App.png" alt=""></a>
                        <a href="https://vk.com/kgridneva4"><img class="foot-items-icons-img" src="../img/WhatsApp.png" alt=""></a>
                    </div>
                </div>
               
            </div>
        </div>
    </footer>
    <script src="../js/modalwindow.js"></script>
    <script src="../js/booking.js"></script>
</body>
</html>