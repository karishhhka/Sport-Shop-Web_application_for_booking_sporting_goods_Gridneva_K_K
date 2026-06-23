<?php
session_start();
header("Content-Type: application/json");
include "database/db.php";
require_once "vendor/autoload.php";

if (!isset($_SESSION['pending_2fa']) || !$_SESSION['pending_2fa']) {
    header("Location: index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    
    if (empty($code) || !preg_match('/^\d{6}$/', $code)) {
        echo json_encode(['success' => false, 'error' => 'Неверный код']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM User WHERE id_user = ?");
        $stmt->execute([$_SESSION['id_user']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Пользователь не найден']);
            exit;
        }

        $twoFA = new RobThree\Auth\TwoFactorAuth('SportStop');
        $isValid = $twoFA->verifyCode($user['two_factor_secret'], $code);

        if ($isValid) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['authenticated'] = true;


            if ($user['role'] === '1') {
                $_SESSION['is_admin'] = true; 
                $redirect = '/admin/index.php';
            } else {
                $_SESSION['is_admin'] = false;
                $redirect = '/persaccount/profile.php';
            }

            
            echo json_encode(['success' => true, 'redirect' => $redirect]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Неверный код подтверждения']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Ошибка базы данных']);
    }
    exit;
}
?>