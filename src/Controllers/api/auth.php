<?php
// Désactiver l'affichage des erreurs HTML
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

// Importer PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Charger PHPMailer avec gestion d'erreur
try {
    require $_SERVER['DOCUMENT_ROOT'] . '/nurayapro/vendor/autoload.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'PHPMailer non disponible']);
    exit;
}

// Connexion à la base de données
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/nurayapro/src/Controllers/cnx.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion BDD']);
    exit;
}

// Charger la configuration email
$emailConfig = require $_SERVER['DOCUMENT_ROOT'] . '/nurayapro/config/email.php';

// Récupérer l'action demandée
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        register();
        break;
    case 'login':
        login();
        break;
    case 'logout':
        logout();
        break;
    case 'verify':
        verifyEmail();
        break;
    case 'verify_email':
        verifyEmailExists();
        break;
    case 'send_verification_code':
        sendVerificationCode();
        break;
    case 'register_with_verification':
        registerWithVerification();
        break;
    case 'forgot':
        forgotPassword();
        break;
    case 'reset':
        resetPassword();
        break;
    case 'check':
        checkAuth();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

function register()
{
    global $cnx;

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birth_date = trim($_POST['birth_date'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
        return;
    }

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
        return;
    }

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide']);
        return;
    }

    // Vérifier si l'email existe déjà
    $email_esc = mysqli_real_escape_string($cnx, $email);
    $check_query = "SELECT id FROM users WHERE email = '$email_esc'";
    $check_result = mysqli_query($cnx, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
        return;
    }

    // Hasher le mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Générer un code de vérification
    $verification_code = sprintf('%06d', mt_rand(0, 999999));
    $code_expires = date('Y-m-d H:i:s', strtotime('+16 minutes'));

    // Insérer l'utilisateur
    $first_name_esc = mysqli_real_escape_string($cnx, $first_name);
    $last_name_esc = mysqli_real_escape_string($cnx, $last_name);
    $birth_date_val = !empty($birth_date) ? "'" . mysqli_real_escape_string($cnx, $birth_date) . "'" : 'NULL';
    $phone_val = !empty($phone) ? "'" . mysqli_real_escape_string($cnx, $phone) . "'" : 'NULL';

    $insert_query = "INSERT INTO users (first_name, last_name, email, password_hash, birth_date, phone, verification_code, code_expires_at) 
                    VALUES ('$first_name_esc', '$last_name_esc', '$email_esc', '$password_hash', $birth_date_val, $phone_val, '$verification_code', '$code_expires')";

    if (mysqli_query($cnx, $insert_query)) {
        // Envoi de l'email de vérification
        $mail_sent = sendVerificationEmail($email, $verification_code);
        echo json_encode([
            'success' => true,
            'message' => $mail_sent ? 'Compte créé avec succès. Vérifiez votre email.' : 'Compte créé mais erreur d\'envoi d\'email. Code: ' . $verification_code,
            'verification_code' => $verification_code // Pour le développement
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du compte: ' . mysqli_error($cnx)]);
    }
}

function login()
{
    global $cnx;

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
        return;
    }

    // Récupérer l'utilisateur
    $query = "SELECT id, first_name, last_name, email, password_hash, is_verified, is_active, role 
               FROM users WHERE email = '$email'";
    $result = mysqli_query($cnx, $query);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
        return;
    }

    $user = mysqli_fetch_assoc($result);

    // Vérifier le mot de passe
    if (!password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
        return;
    }

    // Vérifier si le compte est actif
    if (!$user['is_active']) {
        echo json_encode(['success' => false, 'message' => 'Compte désactivé']);
        return;
    }

    // Vérifier si l'email est vérifié
    if (!$user['is_verified']) {
        echo json_encode(['success' => false, 'message' => 'Veuillez vérifier votre email']);
        return;
    }

    // Mettre à jour la date de dernière connexion
    $update_query = "UPDATE users SET last_login = NOW() WHERE id = " . $user['id'];
    mysqli_query($cnx, $update_query);

    // Créer la session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_role'] = $user['role'];

    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'user' => [
            'id' => $user['id'],
            'name' => $_SESSION['user_name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);
}

function logout()
{
    // Détruire la session
    session_destroy();

    // Rediriger vers la page de connexion
    header('Location: ../../../login.php');
    exit;
}

function verifyEmail()
{
    global $cnx;

    $email = trim($_POST['email'] ?? '');
    $code = trim($_POST['code'] ?? '');

    if (empty($email) || empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Email et code requis']);
        return;
    }

    // Vérifier le code de vérification
    $query = "SELECT id, code_expires_at FROM users 
              WHERE email = '$email' AND verification_code = '$code' AND is_verified = 0";
    $result = mysqli_query($cnx, $query);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Code invalide ou déjà utilisé']);
        return;
    }

    $user = mysqli_fetch_assoc($result);

    // Vérifier si le code n'a pas expiré
    if (strtotime($user['code_expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Code expiré']);
        return;
    }

    // Marquer l'email comme vérifié
    $update_query = "UPDATE users SET is_verified = 1, verified_at = NOW(), 
                    verification_code = NULL, code_expires_at = NULL WHERE id = " . $user['id'];
    mysqli_query($cnx, $update_query);

    echo json_encode(['success' => true, 'message' => 'Email vérifié avec succès']);
}

function forgotPassword()
{
    global $cnx, $emailConfig;

    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email requis']);
        return;
    }

    // Vérifier si l'email existe
    $query = "SELECT id, first_name, last_name FROM users WHERE email = '$email'";
    $result = mysqli_query($cnx, $query);

    if (mysqli_num_rows($result) === 0) {
        // Pour des raisons de sécurité, on peut dire que l'email a été envoyé même s'il n'existe pas
        // Mais ici, on va rester simple pour le debug utilisateur
        echo json_encode(['success' => false, 'message' => 'Email non trouvé']);
        return;
    }

    $user = mysqli_fetch_assoc($result);
    $full_name = $user['first_name'] . ' ' . $user['last_name'];

    // Générer un token de réinitialisation
    $reset_token = bin2hex(random_bytes(32));
    $token_expires = date('Y-m-d H:i:s', strtotime('+16 minutes'));

    // Mettre à jour l'utilisateur
    $update_query = "UPDATE users SET reset_token = '$reset_token', reset_token_expires = '$token_expires' 
                    WHERE id = " . $user['id'];
    
    if (mysqli_query($cnx, $update_query)) {
        // Préparer le lien de réinitialisation
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $reset_link = "$protocol://$host/nurayapro/reset-password.php?token=$reset_token";

        // Envoyer l'email
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

            $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
            $mail->addAddress($email);
            $mail->addReplyTo($emailConfig['reply_to'], $emailConfig['reply_to_name']);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe - Nuraya';

            $email_body = "
            <html>
            <head>
                <style>
                    body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: #1C1C1C; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #C8B6A6 0%, #d4c4b0 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .header h1 { color: #FAF7F2; margin: 0; font-size: 28px; font-weight: 700; }
                    .content { background: #FAF7F2; padding: 30px; border-radius: 0 0 10px 10px; }
                    .info { background: #F5EFE6; border-left: 4px solid #C8B6A6; padding: 15px; margin: 20px 0; font-size: 14px; }
                    .btn { display: inline-block; background: #C8B6A6; color: #FAF7F2 !important; padding: 14px 28px; text-decoration: none; border-radius: 8px; margin: 20px 0; font-weight: 700; }
                    .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #7A7A7A; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🔑 Réinitialisation Nuraya</h1>
                    </div>
                    <div class='content'>
                        <p>Bonjour $full_name,</p>
                        <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Nuraya.</p>
                        
                        <div style='text-align: center;'>
                            <a href='$reset_link' class='btn'>Réinitialiser mon mot de passe</a>
                        </div>
                        
                        <div class='info'>
                            ⏰ <strong>Note :</strong> Ce lien est valable pendant 16 minutes seulement.
                        </div>
                        
                        <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité. Votre mot de passe restera inchangé.</p>
                        
                        
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Nuraya. Tous droits réservés.</p>
                    </div>
                </div>
            </body>
            </html>";

            $mail->Body = $email_body;
            $mail->AltBody = "Bonjour $full_name,\n\nVous avez demandé la réinitialisation de votre mot de passe Nuraya.\n\nCopiez-collez ce lien dans votre navigateur : $reset_link\n\nCe lien est valable 16 minutes.";

            $mail->send();

            echo json_encode([
                'success' => true,
                'message' => 'Un email de réinitialisation a été envoyé.'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur d\'envoi d\'email : ' . $mail->ErrorInfo
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur technique']);
    }
}

function resetPassword()
{
    global $cnx;

    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($token) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Token et mot de passe requis']);
        return;
    }

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
        return;
    }

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
        return;
    }

    // Vérifier le token
    $query = "SELECT id FROM users WHERE reset_token = '$token' AND reset_token_expires > NOW()";
    $result = mysqli_query($cnx, $query);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Token invalide ou expiré']);
        return;
    }

    $user = mysqli_fetch_assoc($result);

    // Hasher le nouveau mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe
    $update_query = "UPDATE users SET password_hash = '$password_hash', 
                    reset_token = NULL, reset_token_expires = NULL WHERE id = " . $user['id'];
    mysqli_query($cnx, $update_query);

    echo json_encode(['success' => true, 'message' => 'Mot de passe réinitialisé avec succès']);
}

function checkAuth()
{
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'authenticated' => false
        ]);
    }
}

// Nouvelles fonctions pour la vérification par email
function verifyEmailExists()
{
    global $cnx;

    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide']);
        return;
    }

    // Vérifier si l'email existe déjà
    $check_query = "SELECT id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($cnx, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Email disponible']);
    }
}

function sendVerificationCode()
{
    global $cnx;

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Générer un code de vérification
    $verification_code = sprintf('%06d', mt_rand(0, 999999));
    $code_expires = date('Y-m-d H:i:s', strtotime('+16 minutes'));

    // Stocker le code en session (temporaire pour l'inscription)
    $_SESSION['temp_verification_code'] = $verification_code;
    $_SESSION['temp_verification_email'] = $email;
    $_SESSION['temp_verification_expires'] = $code_expires;

    // Envoyer l'email avec PHPMailer
    $mail_result = sendVerificationEmail($email, $verification_code);

    if ($mail_result === true) {
        echo json_encode([
            'success' => true,
            'message' => 'Code de vérification envoyé par email'
        ]);
    } else {
        // $mail_result contains the error message
        echo json_encode([
            'success' => false,
            'message' => 'Erreur email: ' . $mail_result
        ]);
    }
}

// Fonction pour envoyer l'email de vérification
function sendVerificationEmail($email, $verification_code)
{
    global $emailConfig;
    try {
        $mail = new PHPMailer(true);

        // Configuration du serveur SMTP
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

        // Configuration de l'email
        $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
        $mail->addAddress($email);
        $mail->addReplyTo($emailConfig['reply_to'], $emailConfig['reply_to_name']);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Code de vérification - Nuraya';

        $email_body = "
        <html>
        <head>
            <style>
                body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: #1C1C1C; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #C8B6A6 0%, #d4c4b0 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .header h1 { color: #FAF7F2; margin: 0; font-size: 28px; font-weight: 700; }
                .content { background: #FAF7F2; padding: 30px; border-radius: 0 0 10px 10px; }
                .code-box { background: #F5EFE6; border: 2px solid #C8B6A6; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0; }
                .code { font-size: 32px; font-weight: 700; color: #C8B6A6; letter-spacing: 8px; font-family: monospace; }
                .info { background: #E6B7C8; color: #1C1C1C; padding: 15px; border-radius: 8px; margin: 20px 0; font-size: 14px; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #7A7A7A; }
                .btn { display: inline-block; background: #C8B6A6; color: #FAF7F2; padding: 12px 24px; text-decoration: none; border-radius: 8px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Vérification Nuraya</h1>
                </div>
                <div class='content'>
                    <p>Bonjour,</p>
                    <p>Merci de vous être inscrit sur Nuraya ! Pour finaliser votre inscription, veuillez utiliser le code de vérification ci-dessous :</p>
                    
                    <div class='code-box'>
                        <div class='code'>$verification_code</div>
                    </div>
                    
                    <div class='info'>
                        ⏰ <strong>Important :</strong> Ce code expire dans 16 minutes.
                    </div>
                    
                    <p>Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet email.</p>
                </div>
                <div class='footer'>
                    <p>Cet email a été envoyé automatiquement. Merci de ne pas répondre.</p>
                    <p>© " . date('Y') . " Nuraya. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->Body = $email_body;
        $mail->AltBody = "Votre code de vérification Nuraya est : $verification_code\n\nCe code expire dans 16 minutes.";

        return $mail->send();

    } catch (Exception $e) {
        $errorMessage = "Erreur PHPMailer: " . $e->getMessage();
        error_log($errorMessage);
        return $errorMessage; // Return error message string
    }
}

function registerWithVerification()
{
    global $cnx;

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birth_date = trim($_POST['birth_date'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $verification_code = trim($_POST['verification_code'] ?? '');

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
        return;
    }

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
        return;
    }

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide']);
        return;
    }

    // Vérifier le code de vérification
    if (empty($verification_code)) {
        echo json_encode(['success' => false, 'message' => 'Code de vérification requis']);
        return;
    }

    if (
        !isset($_SESSION['temp_verification_code']) ||
        !isset($_SESSION['temp_verification_email']) ||
        !isset($_SESSION['temp_verification_expires'])
    ) {
        echo json_encode(['success' => false, 'message' => 'Aucun code de vérification en attente']);
        return;
    }

    if ($_SESSION['temp_verification_email'] !== $email) {
        echo json_encode(['success' => false, 'message' => 'Email ne correspond pas']);
        return;
    }

    if ($_SESSION['temp_verification_code'] !== $verification_code) {
        echo json_encode(['success' => false, 'message' => 'Code de vérification invalide']);
        return;
    }

    if (strtotime($_SESSION['temp_verification_expires']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Code de vérification expiré']);
        return;
    }

    // Vérifier si l'email existe déjà
    $email_esc = mysqli_real_escape_string($cnx, $email);
    $check_query = "SELECT id FROM users WHERE email = '$email_esc'";
    $check_result = mysqli_query($cnx, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
        return;
    }

    // Hasher le mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Échapper les autres champs
    $first_name_esc = mysqli_real_escape_string($cnx, $first_name);
    $last_name_esc = mysqli_real_escape_string($cnx, $last_name);
    $birth_date_val = !empty($birth_date) ? "'" . mysqli_real_escape_string($cnx, $birth_date) . "'" : 'NULL';
    $phone_val = !empty($phone) ? "'" . mysqli_real_escape_string($cnx, $phone) . "'" : 'NULL';

    // Insérer l'utilisateur avec email vérifié
    $insert_query = "INSERT INTO users (first_name, last_name, email, password_hash, birth_date, phone, is_verified, verified_at, created_at) 
                    VALUES ('$first_name_esc', '$last_name_esc', '$email_esc', '$password_hash', $birth_date_val, $phone_val, 1, NOW(), NOW())";

    if (mysqli_query($cnx, $insert_query)) {
        // Nettoyer la session temporaire
        unset($_SESSION['temp_verification_code']);
        unset($_SESSION['temp_verification_email']);
        unset($_SESSION['temp_verification_expires']);

        echo json_encode([
            'success' => true,
            'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du compte: ' . mysqli_error($cnx)]);
    }
}

mysqli_close($cnx);
?>