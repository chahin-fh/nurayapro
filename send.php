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
        // Configuration SMTP Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'malekfhima1@gmail.com';
        $mail->Password = 'hvvj xmfl lvzu qbzb';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Expéditeur et destinataire
        $mail->setFrom($email, $name);
        $mail->addAddress('malali3b@gmail.com'); // Change recipient to malali3b@gmail.com

        // Contenu HTML
        $mail->isHTML(true);
        $mail->Subject = 'Nouveau message du formulaire de contact';

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333; padding: 20px;'>
                <h2 style='color: #007BFF;'>Nouveau message reçu</h2>
                <p><strong>Nom :</strong> {$name}</p>
                <p><strong>Email :</strong> {$email}</p>
                <p><strong>Téléphone :</strong> {$phone}</p>
                <p><strong>Message :</strong><br>
                <span style='display: inline-block; margin-top: 10px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #007BFF;'>{$comment}</span></p>
            </div>
        ";

        $mail->AltBody = "Nom: $name\nEmail: $email\nTéléphone: $phone\n\nMessage:\n$comment";

        $mail->send();
        echo "<script>alert('Message envoyé avec succès ! Nous vous répondrons rapidement.');</script>";
        header('Location: contact');
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Erreur lors de l\\'envoi du message : {$mail->ErrorInfo}');</script>";
        header('Location: contact');
        exit;
    }
}
?>
