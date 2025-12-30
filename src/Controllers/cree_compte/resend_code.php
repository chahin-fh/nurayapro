<?php
session_start();
include("../cnx.php");
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Déplacer la fonction ici pour qu'elle soit accessible
function sendVerificationEmail($email, $code)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'malekfhima1@gmail.com';
        $mail->Password = 'hvvj xmfl lvzu qbzb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('malekfhima1@gmail.com', 'Nuraya');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Nouveau code de vérification - Nuraya';
        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: #1C1C1C; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #BDA18A 0%, #C49D83 100%); padding: 30px 20px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #FAF7F2; padding: 30px 20px; border-radius: 0 0 10px 10px; }
                    .code-box { background: #f5efe6; border: 2px solid #BDA18A; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0; }
                    .code { font-size: 32px; font-weight: 700; color: #BDA18A; letter-spacing: 8px; font-family: monospace; }
                    .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #7A7A7A; }
                    
                    /* Responsive styles */
                    @media screen and (max-width: 600px) {
                        .container { padding: 10px; }
                        .header { padding: 20px 15px; }
                        .content { padding: 20px 15px; }
                        .code-box { padding: 15px; margin: 15px 0; }
                        .code { font-size: 28px; letter-spacing: 6px; }
                    }
                    
                    @media screen and (max-width: 480px) {
                        .container { padding: 5px; }
                        .header { padding: 15px 10px; }
                        .content { padding: 15px 10px; }
                        .code-box { padding: 12px; margin: 12px 0; }
                        .code { font-size: 24px; letter-spacing: 4px; }
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
                        <h2 style='color: #FAF7F2; margin: 0; font-size: 24px; font-weight: 600;'> Nouveau Code de Vérification</h2>
                    </div>
                    <div class='content'>
                        <p>Bonjour,</p>
                        <p>Voici votre nouveau code de vérification pour Nuraya :</p>
                        
                        <div class='code-box'>
                            <div class='code'>$code</div>
                        </div>
                        
                        <p style='text-align: center; color: #7A7A7A; font-size: 14px;'> Ce code est valable pendant 15 minutes.</p>
                        
                        <p>Si vous n'avez pas demandé ce renvoi, vous pouvez ignorer cet email.</p>
                    </div>
                    <div class='footer'>
                        <p> " . date('Y') . " Nuraya. Tous droits réservés.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Erreur mail: " . $e->getMessage());
        return false;
    }
}

// Vérification session
if (!isset($_SESSION['email'])) {
    $_SESSION['error'] = "Session invalide";
    header("Location: index.php");
    exit();
}

try {
    // Génération nouveau code
    $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $newExpires = date('Y-m-d H:i:s', time() + 900);

    // Mise à jour BDD
    $stmt = $cnx->prepare("UPDATE user SET 
        verification_code = ?, 
        code_expires_at = ? 
        WHERE email = ?");

    $stmt->bind_param("sss", $newCode, $newExpires, $_SESSION['email']);
    $stmt->execute();

    // Mise à jour session
    $_SESSION['verification_code'] = $newCode;
    $_SESSION['code_expires'] = strtotime($newExpires);

    // Envoi email
    if (sendVerificationEmail($_SESSION['email'], $newCode)) {
        $_SESSION['success'] = "Nouveau code envoyé !";
    } else {
        throw new Exception("Échec d'envoi du email");
    }

} catch (Exception $e) {
    $_SESSION['error'] = "Erreur système: " . $e->getMessage();
    error_log($e->getMessage());
}

header("Location: login.php");
exit();
?>