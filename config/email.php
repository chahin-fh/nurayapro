<?php
// Configuration de l'email pour PHPMailer
return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    'username' => 'malekfhima1@gmail.com',
    'password' => 'ormpvmdnkoyjaswa', // Spaces removed
    'from_email' => 'noreply@nuraya.com',
    'from_name' => 'Nuraya',
    'reply_to' => 'support@nuraya.com',
    'reply_to_name' => 'Support Nuraya',
    'smtp_options' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]
];
?>