<?php
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';
require '../vendor/phpmailer/src/Exception.php';
require_once '../includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendRescheduleEmail($recipient_email, $recipient_name, $new_checkin_date, $original_checkin_date) {
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
        $mail->Subject = 'Reservation Rescheduled';
        // escape the recipient name when injecting into HTML
        $mail->Body    = 'Dear ' . htmlspecialchars($recipient_name, ENT_QUOTES, 'UTF-8') .
                         ',<br><br>Your reservation has been rescheduled. Here are the updated details:<br><br>' .
                         'Original Check-in Date: ' . $original_checkin_date . '<br>' .
                         'New Check-in Date: ' . $new_checkin_date;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Optional: log $e->getMessage() somewhere for debugging
        return false;
    }
}
?>