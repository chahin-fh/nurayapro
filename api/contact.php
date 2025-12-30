<?php
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../config/database.php';

// Charger PHPMailer
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Charger la configuration email
$emailConfig = require '../config/email.php';

// Récupérer l'action
$action = $_POST['action'] ?? '';

if ($action === 'send') {
    sendMessage();
}

function sendMessage()
{
    global $cnx, $emailConfig;

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? 'Message de contact');
    $message = trim($_POST['comment'] ?? $_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Nom, email et message sont obligatoires']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide']);
        return;
    }

    // Récupérer l'IP et le user agent
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    // Insérer le message
    $insert_query = "INSERT INTO contact_messages (name, email, phone, subject, message, status, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, 'new', ?, ?)";

    $stmt = mysqli_prepare($cnx, $insert_query);
    mysqli_stmt_bind_param($stmt, 'sssssss', $name, $email, $phone, $subject, $message, $ip_address, $user_agent);

    if (mysqli_stmt_execute($stmt)) {
        // Envoyer un email de notification à l'admin
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

            $mail->setFrom($emailConfig['from_email'], 'Nuraya Contact');
            $mail->addAddress('malekfhima1@gmail.com'); // Admin email
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Nouveau message de contact : ' . $subject;

            $email_body = "
            <html>
            <head>
                <style>
                    body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: #1C1C1C; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #BDA18A 0%, #C49D83 100%); padding: 30px 20px; text-align: center; border-radius: 10px 10px 0 0; }
                    .header h1 { color: #FAF7F2; margin: 0; font-size: 28px; font-weight: 700; }
                    .content { background: #FAF7F2; padding: 30px 20px; border-radius: 0 0 10px 10px; }
                    .info-box { background: #f5efe6; border-left: 4px solid #BDA18A; padding: 20px; margin: 20px 0; border-radius: 0 8px 8px 0; }
                    .info-item { margin: 10px 0; display: flex; align-items: flex-start; }
                    .info-label { font-weight: 600; color: #1C1C1C; min-width: 100px; flex-shrink: 0; }
                    .info-value { color: #7A7A7A; word-break: break-word; }
                    .message-box { background: #ffffff; border: 1px solid #E6B7C8; padding: 20px; margin: 20px 0; border-radius: 8px; }
                    .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #7A7A7A; }
                    
                    /* Responsive styles */
                    @media screen and (max-width: 600px) {
                        .container { padding: 10px; }
                        .header { padding: 20px 15px; }
                        .header h1 { font-size: 24px !important; }
                        .content { padding: 20px 15px; }
                        .info-box { padding: 15px; }
                        .info-item { flex-direction: column; align-items: flex-start; }
                        .info-label { margin-bottom: 5px; min-width: auto; }
                        .message-box { padding: 15px; }
                    }
                    
                    @media screen and (max-width: 480px) {
                        .container { padding: 5px; }
                        .header { padding: 15px 10px; }
                        .header h1 { font-size: 20px !important; }
                        .content { padding: 15px 10px; }
                        .info-box { padding: 12px; margin: 15px 0; }
                        .message-box { padding: 12px; margin: 15px 0; }
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <div style='text-align: center; margin-bottom: 20px;'>
                            <div style='display: inline-block; background: #FAF7F2; padding: 15px 30px; border-radius: 8px; margin-bottom: 15px;'>
                                <h1 style='color: #BDA18A; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: 3px;'>NURAYA</h1>
                            </div>
                        </div>
                        <h2 style='color: #FAF7F2; margin: 0; font-size: 24px; font-weight: 600;'>📬 Nouveau Message de Contact</h2>
                    </div>
                    <div class='content'>
                        <p>Bonjour,</p>
                        <p>Vous avez reçu un nouveau message via le formulaire de contact du site Nuraya.</p>
                        
                        <div class='info-box'>
                            <div class='info-item'>
                                <span class='info-label'>👤 Nom :</span>
                                <span class='info-value'>$name</span>
                            </div>
                            <div class='info-item'>
                                <span class='info-label'>📧 Email :</span>
                                <span class='info-value'>$email</span>
                            </div>";

            if (!empty($phone)) {
                $email_body .= "
                            <div class='info-item'>
                                <span class='info-label'>📱 Téléphone :</span>
                                <span class='info-value'>$phone</span>
                            </div>";
            }

            $email_body .= "
                            <div class='info-item'>
                                <span class='info-label'>📌 Sujet :</span>
                                <span class='info-value'>$subject</span>
                            </div>
                        </div>
                        
                        <div class='message-box'>
                            <h3 style='margin-top: 0; color: #BDA18A;'>💬 Message</h3>
                            <p style='white-space: pre-wrap; margin-bottom: 0;'>$message</p>
                        </div>
                        
                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='mailto:$email' style='display: inline-block; background: #BDA18A; color: #FAF7F2 !important; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600;'>📧 Répondre au client</a>
                        </div>
                        
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Nuraya. Tous droits réservés.</p>
                        <p style='font-size: 11px; margin-top: 10px;'>Cet email a été généré automatiquement depuis le formulaire de contact.</p>
                    </div>
                </div>
            </body>
            </html>";

            $mail->Body = $email_body;
            $mail->AltBody = "Nouveau message de contact - Nuraya\n\nNom: $name\nEmail: $email" . (!empty($phone) ? "\nTéléphone: $phone" : "") . "\nSujet: $subject\n\nMessage:\n$message";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email notification failed: " . $e->getMessage());
        }

        echo json_encode([
            'success' => true,
            'message' => 'Message envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du message: ' . mysqli_error($cnx)]);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($cnx);
?>