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

function sendVerificationEmail($email, $code) {
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
        $mail->Subject = 'Votre code de vérification';
        $mail->Body = "Votre code est: <strong>$code</strong> (valide 15 minutes)";
        $mail->AltBody = "Votre code est: $code (valide 15 minutes)";
        
        if(!$mail->send()) {
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