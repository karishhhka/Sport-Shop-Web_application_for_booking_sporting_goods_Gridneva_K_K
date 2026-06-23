<?php
session_start();
include "../database/db.php";

if (!isset($pdo)) {
    die(json_encode(['error' => 'Ошибка: объект PDO не создан.']));
}


$user_id = $_SESSION['id_user'];

try {
    $user_stmt = $pdo->prepare("SELECT name, surname, email FROM User WHERE id_user = ?");
    $user_stmt->execute([$user_id]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_unset();
        session_destroy();
        header("Location: ../login.php?error=user_not_found");
        exit("Пользователь не найден.");
    }
} catch (PDOException $e) {
    die(json_encode(['error' => 'Ошибка базы данных: ' . $e->getMessage()]));
}

// Получение списка бронирований пользователя
$bookings_stmt = $pdo->prepare("
    SELECT b.id_booking, p.name AS product_name, b.quantity, b.booking_date
    FROM Bookings b
    JOIN Products p ON b.id_product = p.id_product
    WHERE b.id_user = ?
");
$bookings_stmt->execute([$user_id]);
$bookings = $bookings_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Личный кабинет</title>
</head>
<body>
    <div class="container">
    <header>
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
                                      <div id="error-container" style="color: red; margin-bottom: 10px;"></div> 
                                      <form class="modal_form" action="../registration.php" method="post">
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
    </header>
   <div class="profile">
        <div class="profile-info">
            <h1 class="profile-info-h1">Личный кабинет</h1>
            <hr class="profile-info-hr">
        </div>
        <div class="profile-items">
            <div class="profile-items-container">
                <img class="profile-items-container-img" src="../img/Male User.png" alt="">
                <div>
                    <h2 class="profile-items-container-h2">Имя:</h2>
                    <p class="profile-items-container-p"><?= htmlspecialchars($user['name']) ?></p>
                </div>
                <div>
                    <h2 class="profile-items-container-h2">Фамилия:</h2>
                    <p class="profile-items-container-p"><?= htmlspecialchars($user['surname']) ?></p>
                </div>
                <div>
                    <h2 class="profile-items-container-h2">Почта:</h2>
                    <p class="profile-items-container-p"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div>
                    <p class="profile-items-container-p"><a href="../logout.php">Выйти</a></p>
                </div>
            </div>
            <div class="profile-items-container">
                <img class="profile-items-container-img" src="../img/Event Accepted.png" alt="">
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <div>
                            <h2 class="profile-items-container-h2">Товар:</h2>
                            <p class="profile-items-container-p"><?= htmlspecialchars($booking['product_name']) ?></p>
                        </div>
                        <div>
                            <h2 class="profile-items-container-h2">Количество:</h2>
                            <p class="profile-items-container-p"><?= htmlspecialchars($booking['quantity']) ?></p>
                        </div>
                        <div>
                            <h2 class="profile-items-container-h2">Дата оформления:</h2>
                            <p class="profile-items-container-p"><?= htmlspecialchars($booking['booking_date']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="profile-items-container-p">У вас пока нет бронирований.</p>
                <?php endif; ?>
            </div>
        </div>
   </div>
    <footer>
            <div class="foot">
                <div class="foot-items">
                    <img class="foot-items-img" src="../img/SPORT STOPEfoot.png" alt="">
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
                        <a class="foot-items-links-text" href="./index.html">Обратная связь</a>
                    </div>
                    <div class="foot-items-icons">
                       <a href="https://vk.com/kgridneva4"><img  class="foot-items-icons-img" src="../img/VK com.png" alt=""></a>
                        <a href="https://vk.com/kgridneva4"><img class="foot-items-icons-img" src="../img/Telegram App.png" alt=""></a>
                        <a href="https://vk.com/kgridneva4"><img class="foot-items-icons-img" src="../img/WhatsApp.png" alt=""></a>
                    </div>
                </div>
            </div>
    </footer>
    <script src="../js/modalwindow.js"></script>
    <script src="../js/slider.js"></script>
    <script src="../js/script.js"></script>
    </div>
</body>
</html>