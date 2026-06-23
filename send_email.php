<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 
include "database/db.php";

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';

$stmt = $pdo->prepare("SELECT id_user FROM User WHERE email = ?");
$stmt->execute([$email]);

if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Этот email не зарегистрирован в системе.']);
    exit;
}

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';

try {
    // Настройки SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; 
    $mail->SMTPAuth   = true;           
    $mail->Username   = 'gridnevakk05@gmail.com';
    $mail->Password   = 'lvkq aozn rpgy madk';    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587;             

    // Отправитель и получатель
    $mail->setFrom('gridnevakk05@gmail.com', 'Каришка');
    $mail->addAddress('gridnevakk05@gmail.com', 'Каришка');
    $mail->addReplyTo($email, $name); 

    // Содержимое письма
    $mail->isHTML(true);                                 
    $mail->Subject = 'Новое сообщение с формы';
    $mail->Body    = "Имя: $name<br>Email: $email<br>Сообщение: $message";
    $mail->AltBody = "Имя: $name\nEmail: $email\nСообщение: $message";

    $mail->send();

    $mail->clearAddresses(); 
    $mail->addAddress($email, $name); 
    $mail->Subject = 'Копия вашего сообщения';
    $mail->Body    = "Спасибо за ваше сообщение!<br><br>Вот копия:<br>$message";
    $mail->AltBody = "Спасибо за ваше сообщение!\n\nВот копия:\n$message";
    $mail->send();


    echo json_encode(['success' => true, 'message' => 'Письмо успешно отправлено!']);
} catch (Exception $e) {

    echo json_encode(['success' => false, 'message' => "Ошибка при отправке письма: {$mail->ErrorInfo}"]);
}
?>