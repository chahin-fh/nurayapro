<?php
// Configuration pour éviter les erreurs HTML dans le JSON
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Démarrer la session et configurer les headers
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Importer PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Charger PHPMailer
require '../vendor/autoload.php';

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
    require_once '../config/database.php';
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
        jsonResponse(false, 'Adresse e-mail invalide');
    }

    try {
        $email = mysqli_real_escape_string($cnx, $email);
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($cnx, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            jsonResponse(false, 'Cette adresse e-mail est déjà utilisée');
        } else {
            jsonResponse(true, 'Adresse e-mail disponible');
        }
    } catch (Exception $e) {
        jsonResponse(false, 'Erreur de vérification de l\'adresse e-mail');
    }
}

function sendVerificationCode()
{
    global $cnx;

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Adresse e-mail invalide');
    }

    try {
        // Générer un code de vérification
        $verification_code = sprintf('%06d', mt_rand(0, 999999));
        $code_expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Stocker en session
        $_SESSION['temp_verification_code'] = $verification_code;
        $_SESSION['temp_verification_email'] = $email;
        $_SESSION['temp_verification_expires'] = $code_expires;

        // Essayer PHPMailer d'abord, puis fallback vers mail() natif
        $mail_sent = false;
        $error_msg = '';

        try {
            $mail_sent = sendPHPMailerEmail($email, $verification_code, $first_name);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
            error_log('PHPMailer failed: ' . $error_msg);
        }

        // Fallback vers email natif si PHPMailer échoue
        if (!$mail_sent) {
            $mail_sent = sendNativeEmail($email, $verification_code, $first_name);
            if ($mail_sent) {
                jsonResponse(true, 'Code de vérification envoyé par e-mail (mode natif)');
            } else {
                jsonResponse(false, 'Erreur lors de l\'envoi de l\'e-mail. Veuillez réessayer.');
            }
        } else {
            jsonResponse(true, 'Code de vérification envoyé par e-mail');
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
    $birth_date = trim($_POST['birth_date'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $verification_code = trim($_POST['verification_code'] ?? '');

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($birth_date)) {
        jsonResponse(false, 'Tous les champs obligatoires doivent être remplis');
    }

    if ($password !== $confirm_password) {
        jsonResponse(false, 'Les mots de passe ne correspondent pas');
    }

    if (strlen($password) < 8) {
        jsonResponse(false, 'Le mot de passe doit contenir au moins 8 caractères');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Adresse e-mail invalide');
    }

    // Valider la date de naissance
    if (!empty($birth_date)) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $birth_date);
        if (!$date_obj || $date_obj->format('Y-m-d') !== $birth_date) {
            jsonResponse(false, 'Date de naissance invalide');
        }
        // Vérifier que la personne n'est pas trop jeune (ex: moins de 13 ans)
        $min_age = 13;
        $max_date = new DateTime();
        $max_date->sub(new DateInterval("P{$min_age}Y"));
        if ($date_obj > $max_date) {
            jsonResponse(false, 'Vous devez avoir au moins 13 ans pour vous inscrire');
        }
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
        jsonResponse(false, 'L\'adresse e-mail ne correspond pas');
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
        $birth_date = mysqli_real_escape_string($cnx, $birth_date);
        $phone = !empty($phone) ? "'" . mysqli_real_escape_string($cnx, $phone) . "'" : 'NULL';
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Vérifier si l'email existe déjà
        $check_query = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($cnx, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            jsonResponse(false, 'Cette adresse e-mail est déjà utilisée');
        }

        // Insérer l'utilisateur avec tous les champs
        $insert_query = "INSERT INTO users (first_name, last_name, email, password_hash, birth_date, phone, is_verified, verified_at, created_at) 
                        VALUES ('$first_name', '$last_name', '$email', '$password_hash', " .
            (!empty($birth_date) ? "'$birth_date'" : 'NULL') . ", $phone, 1, NOW(), NOW())";

        if (mysqli_query($cnx, $insert_query)) {
            // Nettoyer la session
            unset($_SESSION['temp_verification_code']);
            unset($_SESSION['temp_verification_email']);
            unset($_SESSION['temp_verification_expires']);

            jsonResponse(true, 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
        } else {
            jsonResponse(false, 'Erreur lors de la création du compte: ' . mysqli_error($cnx));
        }
    } catch (Exception $e) {
        jsonResponse(false, 'Erreur lors de l\'inscription: ' . $e->getMessage());
    }
}

function sendPHPMailerEmail($email, $verification_code, $first_name = '')
{
    try {
        $mail = new PHPMailer(true);

        // Configuration du serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'malekhima1f@gmail.com';
        $mail->Password = 'nbgv ezhd qpkb btmk';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Configuration de l'email
        $mail->setFrom('noreply@nuraya.com', 'Nuraya');
        $mail->addAddress($email);
        $mail->addReplyTo('support@nuraya.com', 'Support Nuraya');

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Code de vérification - Nuraya';

        // Email HTML
        $mail->Body = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Code de Vérification - Nuraya</title>
            <style>
                body {
                    font-family: 'Montserrat', Arial, sans-serif;
                    line-height: 1.6;
                    color: #1C1C1C;
                    background-color: #F5EFE6;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .email-card {
                    background: #FAF7F2;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 8px 25px rgba(200, 182, 166, 0.15);
                }
                .header {
                    background: linear-gradient(135deg, #1C1C1C 0%, #2a2a2a 100%);
                    color: #FAF7F2;
                    padding: 30px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 32px;
                    font-weight: 800;
                    letter-spacing: 3px;
                }
                .header p {
                    margin: 10px 0 0 0;
                    font-size: 16px;
                    opacity: 0.9;
                }
                .content {
                    padding: 40px 30px;
                    text-align: center;
                }
                .welcome-text {
                    font-size: 18px;
                    margin-bottom: 25px;
                    color: #7A7A7A;
                }
                .code-box {
                    background: linear-gradient(135deg, #C8B6A6 0%, #d4c4b0 100%);
                    color: #FAF7F2;
                    border-radius: 12px;
                    padding: 25px;
                    text-align: center;
                    margin: 30px 0;
                    box-shadow: 0 4px 15px rgba(200, 182, 166, 0.2);
                }
                .code-label {
                    font-size: 12px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    opacity: 0.8;
                    margin-bottom: 10px;
                }
                .code {
                    font-size: 36px;
                    font-weight: 700;
                    letter-spacing: 6px;
                    font-family: 'Courier New', monospace;
                    background: rgba(255, 255, 255, 0.1);
                    padding: 15px;
                    border-radius: 8px;
                    display: inline-block;
                }
                .info-box {
                    background: #E6B7C8;
                    color: #1C1C1C;
                    padding: 20px;
                    border-radius: 12px;
                    margin: 30px 0;
                    font-size: 14px;
                    border-left: 4px solid #1C1C1C;
                }
                .footer {
                    background: rgba(0, 0, 0, 0.05);
                    padding: 20px;
                    text-align: center;
                    border-top: 1px solid rgba(200, 182, 166, 0.1);
                }
                .footer p {
                    margin: 5px 0;
                    font-size: 12px;
                    color: #7A7A7A;
                }
                .security-notice {
                    background: #FFF3CD;
                    border: 1px solid #FFEAA7;
                    color: #856404;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                    font-size: 13px;
                }
                @media (max-width: 600px) {
                    .container { padding: 10px; }
                    .content { padding: 25px 20px; }
                    .code { font-size: 28px; letter-spacing: 4px; }
                }
            </meta>
        </head>
        <body>
            <div class='container'>
                <div class='email-card'>
                    <div class='header'>
                        <h1>NURAYA</h1>
                        <p>Vérification de votre compte</p>
                    </div>
                    <div class='content'>
                        <p class='welcome-text'>
                            Bonjour " . htmlspecialchars($first_name) . ",<br>
                            Merci de vous être inscrit sur Nuraya ! Pour finaliser votre inscription, veuillez utiliser le code de vérification ci-dessous :
                        </p>
                        
                        <div class='code-box'>
                            <div class='code-label'>Votre code de vérification</div>
                            <div class='code'>" . $verification_code . "</div>
                        </div>
                        
                        <div class='security-notice'>
                            <strong> Sécurité importante :</strong> Ce code expire dans 5 minutes pour des raisons de sécurité. Ne le partagez avec personne.
                        </div>
                        
                        <p style='color: #7A7A7A; font-size: 14px; margin-top: 20px;'>
                            Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet e-mail en toute sécurité.
                        </p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " NURAYA. Tous droits réservés.</p>
                        <p>Cet e-mail a été envoyé automatiquement, merci de ne pas y répondre.</p>
                        <p style='margin-top: 10px;'>
                            <strong>Besoin d'aide ?</strong> 
                            <a href='mailto:support@nuraya.com' style='color: #C8B6A6; text-decoration: none;'>Contactez notre support</a>
                        </p>
                    </div>
                </div>
            </div>
        </body>
        </html>";

        // Version texte alternative
        $mail->AltBody = "Bonjour " . $first_name . ",\n\n" .
            "Merci de vous être inscrit sur Nuraya !\n\n" .
            "Votre code de vérification est : " . $verification_code . "\n\n" .
            "Ce code expire dans 5 minutes.\n\n" .
            "Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet e-mail.\n\n" .
            "© " . date('Y') . " Nuraya. Tous droits réservés.";

        return $mail->send();

    } catch (Exception $e) {
        // Log l'erreur pour debug
        error_log('PHPMailer Error: ' . $e->getMessage());
        return false;
    }
}

function sendNativeEmail($email, $verification_code, $first_name = '')
{
    $to = $email;
    $subject = 'Code de vérification - Nuraya';

    $message = "Bonjour " . $first_name . ",\n\n";
    $message .= "Merci de vous être inscrit sur Nuraya !\n\n";
    $message .= "Votre code de vérification est : " . $verification_code . "\n\n";
    $message .= "Ce code expire dans 5 minutes.\n\n";
    $message .= "Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet e-mail.\n\n";
    $message .= "© " . date('Y') . " Nuraya. Tous droits réservés.";

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