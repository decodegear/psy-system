<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMailPitMessage(string $toEmail, string $toName, string $subject, string $bodyText): bool
{
    loadEnv(__DIR__ . '/../.env');

    $mail = new PHPMailer(true);

    try {
        $host = getenv('MAIL_HOST') ?: '127.0.0.1';
        $port = (int) (getenv('MAIL_PORT') ?: 1025);
        $secure = getenv('MAIL_SECURE') ?: '';
        $user = getenv('MAIL_USER') ?: '';
        $pass = getenv('MAIL_PASS') ?: '';
        $fromEmail = getenv('MAIL_FROM') ?: 'suporte@nosnaweb.com.br';
        $fromName = getenv('MAIL_FROM_NAME') ?: 'Suporte';

        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->SMTPAuth = $user !== '';
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->SMTPSecure = $secure;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail, $toName);

        $mail->Subject = $subject;
        $mail->Body = $bodyText;
        $mail->AltBody = $bodyText;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erro ao enviar email: ' . $mail->ErrorInfo);
        return false;
    }
}
