<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../includes/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../includes/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../includes/PHPMailer-master/src/SMTP.php';

function sendVerificationEmail($resetEmail, $verificationCode) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP for Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'lusianonuro@gmail.com'; // Your Gmail email
        $mail->Password = 'ypojhldaxbwmgqqh'; // Your Gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('lusianonuro@gmail.com', 'Your Website');
        $mail->addAddress($resetEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your Password Reset Verification Code';
        $mail->Body    = "Your verification code is: <strong>$verificationCode</strong>. It will expire in 15 minutes.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Verification email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendWelcomeEmail($toEmail, $toName) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'lusianonuro@gmail.com';
        $mail->Password = 'ypojhldaxbwmgqqh';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('lusianonuro@gmail.com', 'Your Website');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Tirana Unplugged!';
        $mail->Body    = "<h2>Welcome, $toName!</h2><p>Thank you for signing up at Tirana Unplugged. We're excited to have you join our community!</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Welcome email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
