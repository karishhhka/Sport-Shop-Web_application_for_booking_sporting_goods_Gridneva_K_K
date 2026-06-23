<?php
$host = '127.0.0.1'; // или localhost
$dbname = 'SportProduct';
$username = 'root'; // ваше имя пользователя MySQL
$password = ''; // ваш пароль MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}
?>