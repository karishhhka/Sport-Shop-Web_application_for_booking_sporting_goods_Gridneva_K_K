<?php
header("Content-Type: application/json");
session_start();

include "database/db.php";
require_once "vendor/autoload.php";
require_once 'login_attempts.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

     $ip = $_SERVER['REMOTE_ADDR'];
    
    if (is_blocked($ip)) {
        echo json_encode([
            'success' => false,
            'error' => 'Слишком много неудачных попыток. Попробуйте позже.'
        ]);
        exit;
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'errors' => ['Заполните все поля']]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['pending_2fa'] = true;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['email'] = $user['email'];
            

            if (empty($user['two_factor_secret'])) {
                $twoFA = new RobThree\Auth\TwoFactorAuth('SportStop');
                $secret = $twoFA->createSecret();
                $pdo->prepare("UPDATE User SET two_factor_secret = ? WHERE email = ?")
                    ->execute([$secret, $email]);

                echo json_encode([
                    'success' => true,
                    'requires_2fa' => true,
                    'qr_code' => $twoFA->getQRCodeImageAsDataUri($email, $secret)
                ]);
                exit;
            }

            echo json_encode(['success' => true, 'requires_2fa' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'errors' => ['Неверная почта или пароль']]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'errors' => ['Ошибка базы данных']]);
    }
}
?>