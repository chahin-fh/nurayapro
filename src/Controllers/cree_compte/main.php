<?php
session_start();
include("../cnx.php");

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($cnx->connect_error) {
    die("Erreur de connexion: " . $cnx->connect_error);
}

function sendVerificationEmail($email, $code)
{
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP plus détaillée
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Active le débogage détaillé
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'malekfhima1@gmail.com';
        $mail->Password = 'hvvj xmfl lvzu qbzb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Timeout = 30; // Augmente le timeout

        // Options de sécurité supplémentaires
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('malekfhima1@gmail.com', 'Nuraya');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Votre code de vérification - Nuraya';
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
                        <h2 style='color: #FAF7F2; margin: 0; font-size: 24px; font-weight: 600;'>🔐 Code de Vérification</h2>
                    </div>
                    <div class='content'>
                        <p>Bonjour,</p>
                        <p>Merci de vous être inscrit sur Nuraya ! Voici votre code de vérification :</p>
                        
                        <div class='code-box'>
                            <div class='code'>$code</div>
                        </div>
                        
                        <p style='text-align: center; color: #7A7A7A; font-size: 14px;'>⏰ Ce code est valable pendant 15 minutes.</p>
                        
                        <p>Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet email.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Nuraya. Tous droits réservés.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Votre code de vérification Nuraya est : $code\n\nCe code expire dans 15 minutes.";

        if (!$mail->send()) {
            error_log("Erreur d'envoi à $email: " . $mail->ErrorInfo);
            return false;
        }

        return true;
    } catch (Exception $e) {
        error_log("Erreur mail pour $email: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['mail']);

    // Validation email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email invalide";
        header("Location: index.php");
        mysqli_close($cnx);
        exit();
    }

    try {
        // Vérification existence email
        $stmt = $cnx->prepare("SELECT id FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['error'] = "Email déjà utilisé";
            header("Location: index.php");
            mysqli_close($cnx);
            exit();
        }

        // Déterminer le rôle
        $role = (strtolower($email) === 'malekfhima1@gmail.com') ? 'admin' : 'user';

        // Génération code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 900);

        // Insertion
        $insert = $cnx->prepare("INSERT INTO user 
            (email, verification_code, is_verified, code_expires_at, role) 
            VALUES (?, ?, 0, ?, ?)");

        $insert->bind_param("ssss", $email, $code, $expires, $role);

        if (!$insert->execute()) {
            throw new Exception("Erreur d'insertion: " . $insert->error);
        }

        // Stockage session
        $_SESSION['email'] = $email;
        $_SESSION['verification_code'] = $code;
        $_SESSION['code_expires'] = strtotime($expires);
        $_SESSION['role'] = $role;

        // Envoi email avec gestion d'erreur améliorée
        if (sendVerificationEmail($email, $code)) {
            header("Location: login.php");
            exit();
        } else {
            // Si l'envoi échoue, supprimer l'utilisateur créé
            $cnx->query("DELETE FROM user WHERE email = '$email'");
            throw new Exception("Échec de l'envoi du code de vérification. Veuillez réessayer.");
        }

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        error_log("Erreur système: " . $e->getMessage());
        header("Location: index.php");
        mysqli_close($cnx);
        exit();
    }
}
?>