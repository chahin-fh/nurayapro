<?php
/**
 * Configuration principale de l'application Nuraya
 */

return [
    // Configuration de l'application
    'app' => [
        'name' => 'Nuraya',
        'version' => '2.0.0',
        'debug' => true,
        'timezone' => 'Europe/Paris',
        'charset' => 'UTF-8'
    ],

    // Configuration de la base de données
    'database' => [
        'host' => 'localhost',
        'name' => 'nuraya_pro',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ],

    // Configuration des emails
    'mail' => [
        'driver' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'malekhima1f@gmail.com',
        'password' => 'nbgv ezhd qpkb btmk',
        'from' => [
            'address' => 'noreply@nuraya.com',
            'name' => 'Nuraya'
        ],
        'reply_to' => [
            'address' => 'support@nuraya.com',
            'name' => 'Support Nuraya'
        ]
    ],

    // Configuration de la session
    'session' => [
        'lifetime' => 120, // minutes
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true
    ],

    // Configuration des uploads
    'uploads' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'path' => 'uploads/'
    ],

    // Configuration des URLs
    'urls' => [
        'base' => 'http://localhost/nuraya_pro',
        'assets' => 'http://localhost/nuraya_pro/assets',
        'uploads' => 'http://localhost/nuraya_pro/uploads'
    ],

    // Configuration des logs
    'logging' => [
        'enabled' => true,
        'level' => 'INFO',
        'file' => 'logs/app.log'
    ]
];
