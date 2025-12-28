<?php
header('Content-Type: application/json');

// Connexion à la base de données
require_once $_SERVER['DOCUMENT_ROOT'] . '/nuraya_pro/src/Controllers/cnx.php';

// Charger PHPMailer
require $_SERVER['DOCUMENT_ROOT'] . '/nuraya_pro/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Charger la configuration email
$emailConfig = require $_SERVER['DOCUMENT_ROOT'] . '/nuraya_pro/config/email.php';

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
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Insérer le message
    $insert_query = "INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

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
            $mail->addAddress('malali3b@gmail.com'); // Admin email from send.php
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Nouveau message de contact : ' . $subject;
            $mail->Body = "<h3>Nouveau message reçu via le site Nuraya</h3>
                          <p><strong>Nom :</strong> $name</p>
                          <p><strong>Email :</strong> $email</p>
                          <p><strong>Téléphone :</strong> $phone</p>
                          <p><strong>Sujet :</strong> $subject</p>
                          <p><strong>Message :</strong><br>$message</p>";

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