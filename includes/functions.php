<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Fonctions utilitaires pour le site Nuraya
 */

// Démarrer la session au début des fonctions
if (!isset($_SESSION)) {
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

/**
 * Envoyer un email d'anniversaire à un utilisateur
 * @param string $userEmail Email de l'utilisateur
 * @param string $userName Nom de l'utilisateur
 * @return bool Succès de l'envoi
 */
function sendBirthdayEmail($userEmail, $userName)
{
    $subject = "Joyeux Anniversaire de la part de Nuraya ! 🎂";
    
    $message = "
    <html>
    <head>
        <title>Joyeux Anniversaire - Nuraya</title>
    </head>
    <body style='font-family: Montserrat, sans-serif; background-color: #F5EFE6; margin: 0; padding: 20px;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #FAF7F2; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 25px rgba(200, 182, 166, 0.15);'>
            <!-- Header -->
            <div style='background: linear-gradient(135deg, #1C1C1C 0%, #2a2a2a 100%); color: #FAF7F2; text-align: center; padding: 30px 20px;'>
                <h1 style='margin: 0; font-size: 32px; font-weight: 800; letter-spacing: 3px;'>NURAYA</h1>
                <p style='margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;'>Joyeux Anniversaire !</p>
            </div>
            
            <!-- Content -->
            <div style='padding: 40px 30px; text-align: center;'>
                <div style='font-size: 48px; margin-bottom: 20px;'>🎂</div>
                <h2 style='color: #1C1C1C; font-size: 24px; margin-bottom: 16px;'>Cher(ère) $userName</h2>
                <p style='color: #7A7A7A; font-size: 16px; line-height: 1.6; margin-bottom: 30px;'>
                    Toute l'équipe de Nuraya vous souhaite un très joyeux anniversaire !<br>
                    Pour célébrer cette journée spéciale, nous vous offrons une réduction de <strong>15%</strong> sur votre prochaine commande.
                </p>
                
                <!-- Gift Code -->
                <div style='background: #C8B6A6; color: #FAF7F2; padding: 20px; border-radius: 12px; margin: 30px 0;'>
                    <p style='margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;'>Votre code cadeau</p>
                    <p style='margin: 10px 0 0 0; font-size: 28px; font-weight: 700; letter-spacing: 2px;'>ANNIV2024</p>
                </div>
                
                <!-- CTA Button -->
                <div style='margin: 30px 0;'>
                    <a href='http://localhost/nurayapro/src/Controllers/produits/index.php' 
                       style='display: inline-block; background: #1C1C1C; color: #FAF7F2; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; transition: all 0.3s ease;'>
                        Profiter de mon cadeau
                    </a>
                </div>
                
                <p style='color: #7A7A7A; font-size: 14px; margin-top: 30px;'>
                    Ce code est valable 7 jours.<br>
                    Merci de faire partie de la famille Nuraya ! 💕
                </p>
            </div>
            
            <!-- Footer -->
            <div style='background: rgba(0, 0, 0, 0.1); padding: 20px; text-align: center; border-top: 1px solid rgba(200, 182, 166, 0.1);'>
                <p style='margin: 0; color: #7A7A7A; font-size: 12px;'>
                    &copy; " . date('Y') . " NURAYA. Tous droits réservés.<br>
                    123 Avenue Habib Bourguiba, Tunis | contact@nuraya.tn
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Configuration PHPMailer
    require_once $_SERVER['DOCUMENT_ROOT'] . '/nurayapro/vendor/autoload.php';
    $emailConfig = require $_SERVER['DOCUMENT_ROOT'] . '/nurayapro/config/email.php';

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $emailConfig['host'];
        $mail->SMTPAuth = $emailConfig['smtp_auth'];
        $mail->Username = $emailConfig['username'];
        $mail->Password = $emailConfig['password'];
        $mail->SMTPSecure = $emailConfig['smtp_secure'];
        $mail->Port = $emailConfig['port'];

        if (isset($emailConfig['smtp_options'])) {
            $mail->SMTPOptions = $emailConfig['smtp_options'];
        }

        $mail->setFrom($emailConfig['from_email'], 'Nuraya');
        $mail->addAddress($userEmail, $userName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Birthday email error: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifier et envoyer les emails d'anniversaire du jour
 * @return array Résultats des envois
 */
function sendDailyBirthdayEmails()
{
    global $cnx;
    
    $today = date('m-d'); // Format mois-jour pour ignorer l'année
    
    $query = "SELECT id, first_name, email, birth_date 
              FROM users 
              WHERE DATE_FORMAT(birth_date, '%m-%d') = '$today' 
              AND birthday_email_sent = 0 
              AND is_active = 1";
    
    $result = mysqli_query($cnx, $query);
    $sentEmails = [];
    $failedEmails = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($user = mysqli_fetch_assoc($result)) {
            $userName = $user['first_name'];
            $userEmail = $user['email'];
            $userId = $user['id'];
            
            if (sendBirthdayEmail($userEmail, $userName)) {
                // Marquer l'email comme envoyé
                $updateQuery = "UPDATE users SET birthday_email_sent = 1 WHERE id = $userId";
                mysqli_query($cnx, $updateQuery);
                $sentEmails[] = $userEmail;
            } else {
                $failedEmails[] = $userEmail;
            }
        }
    }
    
    return [
        'sent' => $sentEmails,
        'failed' => $failedEmails,
        'total' => count($sentEmails) + count($failedEmails)
    ];
}

/**
 * Réinitialiser les flags d'envoi d'email d'anniversaire (à exécuter quotidiennement)
 */
function resetBirthdayEmailFlags()
{
    global $cnx;
    
    // Réinitialiser pour les utilisateurs dont ce n'est plus leur anniversaire aujourd'hui
    $query = "UPDATE users 
              SET birthday_email_sent = 0 
              WHERE DATE_FORMAT(birth_date, '%m-%d') != DATE_FORMAT(NOW(), '%m-%d')";
    
    return mysqli_query($cnx, $query);
}
?>