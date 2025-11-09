<?php
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';
require '../vendor/phpmailer/src/Exception.php';
require_once '../includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendCheckoutEmail($recipient_email, $recipient_name, $reservation_details) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        // $mail->SMTPDebug = 2; // uncomment for verbose debug
        $mail->isSMTP();
        $mail->SMTPAuth = true;

        // Make sure these email strings are quoted (important)
        $mail->Host       = SMTP_HOST;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom('renatosplace2025@gmail.com', 'Renatos Place');
        // Pass the recipient variables (must not be empty)
        $mail->addAddress($recipient_email, $recipient_name);
        $mail->addReplyTo('reservation@renatosplace.online', 'My Name');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Reservation Checkout Details';
        // escape the recipient name when injecting into HTML
        $mail->Body    = 'Dear ' . htmlspecialchars($recipient_name, ENT_QUOTES, 'UTF-8') .
                         ',<br><br>Thank you for staying with us. Here are your checkout details:<br><br>' .
                         $reservation_details;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Optional: log $e->getMessage() somewhere for debugging
        return false;
    }
}
?>