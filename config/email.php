<?php
// Configuration de l'email pour PHPMailer
return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    'username' => 'votre-email@gmail.com',  // À configurer
    'password' => 'votre-mot-de-passe-app',  // Mot de passe d'application Gmail
    'from_email' => 'noreply@nuraya.com',
    'from_name' => 'Nuraya',
    'reply_to' => 'support@nuraya.com',
    'reply_to_name' => 'Support Nuraya'
];
?>