<?php
session_start();
include("../cnx.php");
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Déplacer la fonction ici pour qu'elle soit accessible
function sendVerificationEmail($email, $code) {
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
        $mail->Subject = 'Nouveau code de vérification';
        $mail->Body = "Votre nouveau code est: <strong>$code</strong>";
        
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