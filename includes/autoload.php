<?php
/**
 * Autoloader pour l'application Nuraya
 */

spl_autoload_register(function ($class) {
    // Convertir les namespaces en chemins de fichiers
    $prefix = 'Nuraya\\';
    $base_dir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Charger les fichiers de configuration
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

// Charger les fonctions globales
require_once __DIR__ . '/functions.php';

// Charger la base de données
require_once __DIR__ . '/../core/Database.php';