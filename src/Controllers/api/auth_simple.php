<?php
// Configuration pour éviter les erreurs HTML dans le JSON
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Démarrer la session et configurer les headers
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Fonction pour envoyer une réponse JSON propre
function jsonResponse($success, $message, $data = null)
{
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Connexion BDD sécurisée
try {
    include '../cnx.php';
} catch (Exception $e) {
    jsonResponse(false, 'Erreur de connexion à la base de données');
}

// Récupérer l'action
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Router
switch ($action) {
    case 'verify_email':
        verifyEmailExists();
        break;
    case 'send_verification_code':
        sendVerificationCode();
        break;
    case 'register_with_verification':
        registerWithVerification();
        break;
    default:
        jsonResponse(false, 'Action non valide');
}

function verifyEmailExists()
{
    global $cnx;

    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Email invalide');
    }

    try {
        $email = mysqli_real_escape_string($cnx, $email);
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($cnx, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            jsonResponse(false, 'Cet email est déjà utilisé');
        } else {
            jsonResponse(true, 'Email disponible');
        }
    } catch (Exception $e) {
        jsonResponse(false, 'Erreur de vérification email');
    }
}

function sendVerificationCode()
{
    global $cnx;

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Email invalide');
    }

    try {
        // Générer un code de vérification
        $verification_code = sprintf('%06d', mt_rand(0, 999999));
        $code_expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Stocker en session
        $_SESSION['temp_verification_code'] = $verification_code;
        $_SESSION['temp_verification_email'] = $email;
        $_SESSION['temp_verification_expires'] = $code_expires;

        // Envoyer l'email (version simplifiée)
        $mail_sent = sendSimpleEmail($email, $verification_code, $first_name);

        if ($mail_sent) {
            jsonResponse(true, 'Code de vérification envoyé par email');
        } else {
            jsonResponse(false, 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.');
        }
    } catch (Exception $e) {
        jsonResponse(false, 'Erreur lors de l\'envoi du code de vérification');
    }
}

function registerWithVerification()
{
    global $cnx;

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $verification_code = trim($_POST['verification_code'] ?? '');

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        jsonResponse(false, 'Tous les champs sont obligatoires');
    }

    if ($password !== $confirm_password) {
        jsonResponse(false, 'Les mots de passe ne correspondent pas');
    }

    if (strlen($password) < 8) {
        jsonResponse(false, 'Le mot de passe doit contenir au moins 8 caractères');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Email invalide');
    }

    // Vérifier le code de vérification
    if (empty($verification_code)) {
        jsonResponse(false, 'Code de vérification requis');
    }

    if (
        !isset($_SESSION['temp_verification_code']) ||
        !isset($_SESSION['temp_verification_email']) ||
        !isset($_SESSION['temp_verification_expires'])
    ) {
        jsonResponse(false, 'Aucun code de vérification en attente');
    }

    if ($_SESSION['temp_verification_email'] !== $email) {
        jsonResponse(false, 'Email ne correspond pas');
    }

    if ($_SESSION['temp_verification_code'] !== $verification_code) {
        jsonResponse(false, 'Code de vérification invalide');
    }

    if (strtotime($_SESSION['temp_verification_expires']) < time()) {
        jsonResponse(false, 'Code de vérification expiré');
        return;
    }

    try {
        $email = mysqli_real_escape_string($cnx, $email);
        $first_name = mysqli_real_escape_string($cnx, $first_name);
        $last_name = mysqli_real_escape_string($cnx, $last_name);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Vérifier si l'email existe déjà
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($cnx, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            jsonResponse(false, 'Cet email est déjà utilisé');
        }

        // Insérer l'utilisateur
        $insert_query = "INSERT INTO users (first_name, last_name, email, password_hash, is_verified, verified_at, created_at) 
                        VALUES ('$first_name', '$last_name', '$email', '$password_hash', 1, NOW(), NOW())";

        if (mysqli_query($cnx, $insert_query)) {
            // Nettoyer la session
            unset($_SESSION['temp_verification_code']);
            unset($_SESSION['temp_verification_email']);
            unset($_SESSION['temp_verification_expires']);

            jsonResponse(true, 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
        } else {
            jsonResponse(false, 'Erreur lors de la création du compte');
        }
    } catch (Exception $e) {
        jsonResponse(false, 'Erreur lors de l\'inscription');
    }
}

function sendSimpleEmail($email, $verification_code, $first_name = '')
{
    $to = $email;
    $subject = 'Code de vérification - Nuraya';

    $message = "Bonjour $first_name,\n\n";
    $message .= "Merci de vous être inscrit sur Nuraya !\n\n";
    $message .= "Votre code de vérification est : $verification_code\n\n";
    $message .= "Ce code expire dans 5 minutes.\n\n";
    $message .= "Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet email.\n\n";
    $message .= "© 2025 Nuraya. Tous droits réservés.";

    $headers = [
        'From: Nuraya <noreply@nuraya.com>',
        'Reply-To: Support Nuraya <support@nuraya.com>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion()
    ];

    $headers_string = implode("\r\n", $headers);
    $subject_encoded = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    return mail($to, $subject_encoded, $message, $headers_string);
}

// Fermer la connexion BDD
if (isset($cnx)) {
    mysqli_close($cnx);
}
?>