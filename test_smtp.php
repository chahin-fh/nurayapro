<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require 'config/email.php';

echo "Testing Email Config...\n";
echo "User: " . $config['username'] . "\n";
echo "Pass length: " . strlen($config['password']) . "\n";

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['username'];
    $mail->Password = $config['password'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port = $config['port'];
    
    if (isset($config['smtp_options'])) {
        $mail->SMTPOptions = $config['smtp_options'];
    }

    $mail->setFrom($config['from_email'], 'Test');
    $mail->addAddress('malekfhima1@gmail.com');
    $mail->Subject = 'Test Auth';
    $mail->Body = 'Test';
    $mail->send();
    echo "SUCCESS";
} catch (Exception $e) {
    echo "ERROR: " . $mail->ErrorInfo;
}
