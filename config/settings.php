<?php
/**
 * Configuration des assets statiques pour Nuraya
 */

// Configuration de base
define('SITE_NAME', 'Nuraya');
define('SITE_URL', '/nurayapro');
define('ASSETS_URL', '/nurayapro/assets');
define('IMAGES_URL', '/nurayapro/img');

// Configuration des chemins
define('ROOT_PATH', __DIR__);
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('IMAGES_PATH', ROOT_PATH . '/img');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Configuration des images
define('IMAGE_PLACEHOLDER_PRODUCTS', 'https://via.placeholder.com/280x340/F5EFE6/C8B6A6?text=Produit');
define('IMAGE_PLACEHOLDER_COLLECTIONS', 'https://via.placeholder.com/400x300/F5EFE6/C8B6A6?text=Collection');
define('IMAGE_PLACEHOLDER_AVATAR', 'https://via.placeholder.com/80x80/F5EFE6/C8B6A6?text=Avatar');

// Configuration des emails
define('EMAIL_FROM', 'malekfhima1@gmail.com');
define('EMAIL_FROM_NAME', 'Nuraya');
define('EMAIL_SUPPORT', 'support@nuraya.example');

// Configuration de la boutique
define('CURRENCY', 'TND');
define('CURRENCY_SYMBOL', 'TND');
define('TAX_RATE', 0.19); // 19%
define('SHIPPING_COST', 7.000);

// Configuration de pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 10);
define('REVIEWS_PER_PAGE', 5);

// Configuration de sécurité
define('SESSION_LIFETIME', 86400); // 24 heures
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 600); // 10 minutes

// Configuration des logs
define('LOGS_PATH', ROOT_PATH . '/logs');
define('ERROR_LOG', LOGS_PATH . '/error.log');
define('ACCESS_LOG', LOGS_PATH . '/access.log');
define('DEBUG_MODE', false);

// Configuration des API externes
define('GOOGLE_MAPS_API_KEY', '');
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// Configuration du cache
define('CACHE_ENABLED', false);
define('CACHE_LIFETIME', 3600); // 1 heure

// Configuration des réseaux sociaux
$social_links = [
    'facebook' => 'https://facebook.com/nuraya',
    'instagram' => 'https://instagram.com/nuraya',
    // 'twitter' => 'https://twitter.com/nuraya',
    // 'linkedin' => 'https://linkedin.com/company/nuraya'
];

// Configuration des couleurs du thème
$theme_colors = [
    'bg_light' => '#F5EFE6',
    'bg_white' => '#FAF7F2',
    'beige_dark' => '#C8B6A6',
    'text_dark' => '#1C1C1C',
    'text_gray' => '#7A7A7A',
    'accent_pink' => '#E6B7C8'
];

// Configuration des tailles d'images
$image_sizes = [
    'thumbnail' => [150, 150],
    'small' => [280, 340],
    'medium' => [400, 400],
    'large' => [800, 800]
];

// Configuration des statuts de commande
$order_statuses = [
    'pending' => 'En attente',
    'processing' => 'En traitement',
    'shipped' => 'Expédiée',
    'delivered' => 'Livrée',
    'cancelled' => 'Annulée'
];

// Configuration des méthodes de paiement
$payment_methods = [
    'cash_on_delivery' => 'Paiement à la livraison',
    // 'bank_transfer' => 'Virement bancaire',
    // 'credit_card' => 'Carte de crédit'
];

// Configuration des méthodes de livraison
$shipping_methods = [
    'standard' => 'Livraison standard (3-5 jours)',
    'express' => 'Livraison express (1-2 jours)',
    'pickup' => 'Retrait en magasin'
];

// Retourner la configuration
return [
    'site' => [
        'name' => SITE_NAME,
        'url' => SITE_URL,
        'email' => EMAIL_FROM,
        'currency' => CURRENCY,
        'currency_symbol' => CURRENCY_SYMBOL
    ],
    'paths' => [
        'root' => ROOT_PATH,
        'assets' => ASSETS_PATH,
        'images' => IMAGES_PATH,
        'uploads' => UPLOADS_PATH,
        'logs' => LOGS_PATH
    ],
    'urls' => [
        'assets' => ASSETS_URL,
        'images' => IMAGES_URL
    ],
    'shop' => [
        'tax_rate' => TAX_RATE,
        'shipping_cost' => SHIPPING_COST,
        'products_per_page' => PRODUCTS_PER_PAGE
    ],
    'security' => [
        'session_lifetime' => SESSION_LIFETIME,
        'max_login_attempts' => MAX_LOGIN_ATTEMPTS,
        'login_lockout_time' => LOGIN_LOCKOUT_TIME
    ],
    'social' => $social_links,
    'theme' => $theme_colors,
    'image_sizes' => $image_sizes,
    'order_statuses' => $order_statuses,
    'payment_methods' => $payment_methods,
    'shipping_methods' => $shipping_methods
];
?>