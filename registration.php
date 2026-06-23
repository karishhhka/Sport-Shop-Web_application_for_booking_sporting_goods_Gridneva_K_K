<?php
header("Content-Type: application/json");
session_start();
include "./database/db.php"; 
require_once 'login_attempts.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

function validate_input($data) {
    $data = filter_var($data, FILTER_DEFAULT);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'];
    
  if (is_blocked($ip)) {
        echo json_encode([
            'success' => false,
            'error' => 'Слишком много неудачных попыток. Попробуйте позже.'
        ]);
        exit;
    }
    $errors = [];

    $name = validate_input($_POST['name']);
    $surname = validate_input($_POST['surname']);
    $phone = validate_input($_POST['phone']);
    $email = validate_input($_POST['email']);
    $pass = validate_input($_POST['pass']);
    $doppass = validate_input($_POST['doppass']);

    if (empty($name) || empty($surname) || empty($phone) || empty($email) || empty($pass) || empty($doppass)) {
        $errors[] = 'Заполните все поля';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный формат email адреса';
    }
    if (!preg_match("#[a-zA-Z]#", $pass) || !preg_match("#\d#", $pass) || !preg_match("#\W#", $pass)) {
        $errors[] = 'Пароль должен содержать буквы латинского алфавита, цифры и хотя бы один спецсимвол.';
    }
    if ($pass !== $doppass) {
        $errors[] = 'Пароли не совпадают';
    }

    if (empty($errors)) {
        try {
            $selectEmail = $pdo->prepare("SELECT * FROM `User` WHERE `email` = :email");
            $selectEmail->bindParam(':email', $email);
            $selectEmail->execute();

            if ($selectEmail->fetch()) {
                $errors[] = 'Такая почта уже существует';
            } else {
                $selectPhone = $pdo->prepare("SELECT * FROM `User` WHERE `phone` = :phone");
                $selectPhone->bindParam(':phone', $phone);
                $selectPhone->execute();

                if ($selectPhone->fetch()) {
                    $errors[] = 'Такой номер телефона уже зарегистрирован';
                } else {

                    $password = password_hash($pass, PASSWORD_DEFAULT);
                    $created_at = date('Y-m-d H:i:s');
                    $updated_at = $created_at;

                    $stmt = $pdo->prepare("
                        INSERT INTO `User` (
                            `name`, `surname`, `password`, `email`, `phone`, `role`, `created_at`, `updated_at`
                        ) VALUES (
                            :name, :surname, :password, :email, :phone, '0', :created_at, :updated_at
                        )
                    ");
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':surname', $surname);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':phone', $phone);
                    $stmt->bindParam(':created_at', $created_at);
                    $stmt->bindParam(':updated_at', $updated_at);

                    if ($stmt->execute()) {
                        if ($stmt->rowCount() > 0) {
                            $_SESSION['success_message'] = 'Вы успешно зарегистрировались!';
                            echo json_encode(['success' => true]); // Успешная регистрация
                            exit();
                        } else {
                            $errors[] = 'Данные не были добавлены в базу данных.';
                        }
                    } else {
                        $errors[] = 'Ошибка при регистрации. Попробуйте позже.';
                    }
                }
            }
        } catch (PDOException $e) {
            $errors[] = 'Ошибка базы данных: ' . $e->getMessage();
        }
    }

    echo json_encode(['success' => false, 'errors' => $errors]);
}
?>