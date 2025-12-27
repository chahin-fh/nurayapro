<?php
/**
 * Fonctions utilitaires pour le site Nuraya
 */

// Démarrer la session au début des fonctions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Générer l'URL des assets statiques (CSS, JS, images)
 * @param string $path Chemin relatif depuis le dossier assets
 * @return string URL complète
 */
function asset_url($path)
{
    $base_url = '/nurayapro/assets';
    return $base_url . '/' . ltrim($path, '/');
}

/**
 * Générer l'URL des images produits
 * @param string $filename Nom du fichier image
 * @return string URL complète
 */
function product_image_url($filename)
{
    if (empty($filename) || !file_exists(__DIR__ . '/img/products/' . $filename)) {
        return 'https://via.placeholder.com/280x340/F5EFE6/C8B6A6?text=Produit';
    }
    return '/nurayapro/img/products/' . $filename;
}

/**
 * Générer l'URL des images de collection
 * @param string $filename Nom du fichier image
 * @return string URL complète
 */
function collection_image_url($filename)
{
    if (empty($filename) || !file_exists(__DIR__ . '/img/collections/' . $filename)) {
        return 'https://via.placeholder.com/400x300/F5EFE6/C8B6A6?text=Collection';
    }
    return '/nurayapro/img/collections/' . $filename;
}

/**
 * Générer l'URL des images par défaut
 * @param string $filename Nom du fichier image
 * @param string $type Type d'image (products, collections, etc.)
 * @return string URL complète
 */
function image_url($filename, $type = 'products')
{
    $folder = $type === 'collections' ? 'collections' : 'products';
    $default_size = $type === 'collections' ? '400x300' : '280x340';
    $default_text = $type === 'collections' ? 'Collection' : 'Produit';

    if (empty($filename) || !file_exists(__DIR__ . '/img/' . $folder . '/' . $filename)) {
        return "https://via.placeholder.com/{$default_size}/F5EFE6/C8B6A6?text={$default_text}";
    }
    return "/nurayapro/img/{$folder}/{$filename}";
}

/**
 * Formater le prix en DT
 * @param float $price Prix à formater
 * @return string Prix formaté
 */
function format_price($price)
{
    return number_format((float) $price, 3) . ' DT';
}

/**
 * Générer un slug à partir d'une chaîne
 * @param string $text Texte à transformer
 * @return string Slug
 */
function generate_slug($text)
{
    // Utiliser une approche plus simple pour générer des slugs
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Valider un email
 * @param string $email Email à valider
 * @return bool
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sécuriser les entrées utilisateur
 * @param string $input Texte à sécuriser
 * @return string Texte sécurisé
 */
function sanitize_input($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Générer un token CSRF
 * @return string Token
 */
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier un token CSRF
 * @param string $token Token à vérifier
 * @return bool
 */
function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Logger les erreurs
 * @param string $message Message d'erreur
 * @param string $type Type d'erreur (error, warning, info)
 */
function log_error($message, $type = 'error')
{
    $log_file = __DIR__ . '/logs/' . $type . '.log';
    $log_dir = dirname($log_file);

    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$message}" . PHP_EOL;

    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Rediriger avec message de succès/erreur
 * @param string $url URL de destination
 * @param string $message Message à afficher
 * @param string $type Type de message (success, error, warning)
 */
function redirect_with_message($url, $message, $type = 'success')
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: {$url}");
    exit;
}

/**
 * Afficher les messages flash
 * @return string HTML du message
 */
function display_flash_message()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);

        $class = $type === 'error' ? 'alert-error' :
            ($type === 'warning' ? 'alert-warning' : 'alert-success');

        return "<div class='alert show {$class}'>{$message}</div>";
    }
    return '';
}

/**
 * Vérifier si l'utilisateur est connecté
 * @return bool
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Obtenir l'ID de l'utilisateur connecté
 * @return int|null
 */
function get_current_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Limiter le texte à un certain nombre de caractères
 * @param string $text Texte à limiter
 * @param int $length Nombre de caractères maximum
 * @param string $suffix Suffixe à ajouter si tronqué
 * @return string Texte limité
 */
function limit_text($text, $length = 100, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length - strlen($suffix)) . $suffix;
}

/**
 * Obtenir la date formatée
 * @param string $date Date à formater
 * @param string $format Format de sortie
 * @return string Date formatée
 */
function format_date($date, $format = 'd/m/Y H:i')
{
    return date($format, strtotime($date));
}

/**
 * Vérifier si une page est active pour la navigation
 * @param string $page Nom de la page à vérifier
 * @return string Classe CSS active ou vide
 */
function is_active_page($page)
{
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $page ? 'active' : '';
}

/**
 * Obtenir les catégories pour le menu
 * @param mysqli $cnx Connexion BDD
 * @return array Liste des catégories
 */
function get_navigation_categories($cnx)
{
    $query = "SELECT name, slug FROM categories WHERE is_active = 1 ORDER BY sort_order, name";
    $result = mysqli_query($cnx, $query);

    $categories = [];
    while ($cat = mysqli_fetch_assoc($result)) {
        $categories[] = $cat;
    }

    return $categories;
}

/**
 * Nettoyer les anciennes sessions expirées
 * @param mysqli $cnx Connexion BDD
 * @param int $days Nombre de jours avant expiration
 */
function cleanup_expired_sessions($cnx, $days = 30)
{
    $expiry_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
    $query = "DELETE FROM cart WHERE created_at < '$expiry_date'";
    mysqli_query($cnx, $query);
}

/**
 * Obtenir les statistiques du site
 * @param mysqli $cnx Connexion BDD
 * @return array Statistiques
 */
function get_site_stats($cnx)
{
    $stats = [];

    // Nombre de produits
    $result = mysqli_query($cnx, "SELECT COUNT(*) as count FROM products WHERE is_active = 1");
    $stats['products'] = mysqli_fetch_assoc($result)['count'];

    // Nombre d'utilisateurs
    $result = mysqli_query($cnx, "SELECT COUNT(*) as count FROM users");
    $stats['users'] = mysqli_fetch_assoc($result)['count'];

    // Nombre de commandes
    $result = mysqli_query($cnx, "SELECT COUNT(*) as count FROM orders");
    $stats['orders'] = mysqli_fetch_assoc($result)['count'];

    // Chiffre d'affaires
    $result = mysqli_query($cnx, "SELECT SUM(total) as total FROM orders WHERE status != 'cancelled'");
    $stats['revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;

    return $stats;
}
?>