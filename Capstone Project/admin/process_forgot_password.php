<?php
require_once '../includes/connect.php';
require_once '../includes/config.php';
require '../vendor/phpmailer/src/Exception.php';
require '../vendor/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);

    // Look for the admin requesting the reset
    $query = $conn->query("SELECT * FROM `admin` WHERE `username` = '$username'");
    $user = $query ? $query->fetch_assoc() : null;

    if ($user) {
        // Look for Super Admin
        $superAdminQuery = $conn->query("SELECT * FROM `admin` WHERE `role` = 'Super Admin' LIMIT 1");
        $superAdmin = $superAdminQuery ? $superAdminQuery->fetch_assoc() : null;

        if ($superAdmin) {
            $mail = new PHPMailer(true);
            
            try {
                // Server settings
                $mail->isSMTP();
                $mail->SMTPAuth   = true;
                
               $mail->Host       = SMTP_HOST;
                $mail->Username   = SMTP_USERNAME;
                $mail->Password   = SMTP_PASSWORD;
                $mail->SMTPSecure = 'tls';
                $mail->Port       = SMTP_PORT;

                // Recipients
                $mail->setFrom(FROM_EMAIL, FROM_NAME);
                $mail->addAddress($superAdmin['email'], $superAdmin['name']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request - Renato\'s Place';
                $mail->Body    = '
                    <p>Hello Super Admin,</p>
                    <p>The following admin has requested a password reset:</p>
                    <ul>
                        <li><strong>Name:</strong> ' . htmlspecialchars($user['name']) . '</li>
                        <li><strong>Username:</strong> ' . htmlspecialchars($user['username']) . '</li>
                        <li><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</li>
                    </ul>
                    <p>Please take appropriate action in the Admin Panel.</p>
                    <p><em>This is an automated message from Renato\'s Place Management System.</em></p>
                ';

                $mail->send();
                $message = "<div class='alert alert-success'>✅ A notification has been sent to the Super Admin. Please wait for assistance.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger'>❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>❌ Super Admin not found in the system.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>❌ Username not found.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password Result</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/admin/style.css" />
    <link rel="icon" href="../assets/favicon.ico">
</head>
<body>
    <nav style="background-color:rgba(0, 0, 0, 0.1);" class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand">Renato's Place Private Resort and Events</a>
            </div>
        </div>
    </nav>

    <div class="container text-center">
        <br><br>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h4>Password Reset Request</h4>
                </div>
                <div class="panel-body">
                    <?php echo $message; ?>
                    <a href="index.php" class="btn btn-primary btn-block">Back to Login</a>
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>

    <div style="text-align:right; margin-right:10px;" class="navbar navbar-default navbar-fixed-bottom">
        <label>&copy; Renato's Place Private Resort and Events </label>
    </div>
</body>
</html>