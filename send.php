<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $comment = htmlspecialchars($_POST["comment"]);

    $mail = new PHPMailer(true);

    try {
        // Charger la configuration email
        $emailConfig = require 'config/email.php';

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

        // Expéditeur et destinataire
        $mail->setFrom($emailConfig['from_email'], $name);
        $mail->addAddress('malekfhima1@gmail.com');
        $mail->addReplyTo($email, $name);

        // Contenu HTML
        $mail->isHTML(true);
        $mail->Subject = 'Nouveau message du formulaire de contact';

        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: #1C1C1C; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #BDA18A 0%, #C49D83 100%); padding: 30px 20px; text-align: center; border-radius: 10px 10px 0 0; }
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
                        .content { padding: 20px 15px; }
                        .info-box { padding: 15px; }
                        .info-item { flex-direction: column; align-items: flex-start; }
                        .info-label { margin-bottom: 5px; min-width: auto; }
                        .message-box { padding: 15px; }
                    }
                    
                    @media screen and (max-width: 480px) {
                        .container { padding: 5px; }
                        .header { padding: 15px 10px; }
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
                                <span class='info-value'>{$name}</span>
                            </div>
                            <div class='info-item'>
                                <span class='info-label'>📧 Email :</span>
                                <span class='info-value'>{$email}</span>
                            </div>
                            <div class='info-item'>
                                <span class='info-label'>📱 Téléphone :</span>
                                <span class='info-value'>{$phone}</span>
                            </div>
                        </div>
                        
                        <div class='message-box'>
                            <h3 style='margin-top: 0; color: #BDA18A;'>💬 Message</h3>
                            <p style='white-space: pre-wrap; margin-bottom: 0;'>{$comment}</p>
                        </div>
                        
                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='mailto:{$email}' style='display: inline-block; background: #BDA18A; color: #FAF7F2 !important; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600;'>📧 Répondre au client</a>
                        </div>
                        
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Nuraya. Tous droits réservés.</p>
                        <p style='font-size: 11px; margin-top: 10px;'>Cet email a été généré automatiquement depuis le formulaire de contact.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        $mail->AltBody = "Nom: $name\nEmail: $email\nTéléphone: $phone\n\nMessage:\n$comment";

        $mail->send();
        echo "<script>alert('Message envoyé avec succès ! Nous vous répondrons rapidement.');</script>";
        header('Location: contact_us.php');
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Erreur lors de l\\'envoi du message : {$mail->ErrorInfo}');</script>";
        header('Location: contact_us.php');
        exit;
    }
}
?>