<?php
session_start();
include("../cnx.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Méthode invalide";
    header("Location: login.php");
    exit();
}

if (!isset($_POST['verification_code'])) {
    $_SESSION['error'] = "Code requis";
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['email'], $_SESSION['verification_code'], $_SESSION['code_expires'])) {
    $_SESSION['error'] = "Session expirée";
    header("Location: index.php");
    exit();
}

if (time() > $_SESSION['code_expires']) {
    $_SESSION['error'] = "Code expiré";
    header("Location: login.php");
    exit();
}

$enteredCode = trim($_POST['verification_code']);
$email = $cnx->real_escape_string($_SESSION['email']);

if ($enteredCode !== $_SESSION['verification_code']) {
    $_SESSION['error'] = "Code incorrect";
    header("Location: login.php");
    exit();
}

try {
    // Mise à jour adaptée à votre structure
    $update = $cnx->query("UPDATE user SET 
        is_verified = 1,
        verification_code = NULL,
        verified_at = NOW(),
        code_expires_at = NULL
        WHERE email = '$email'");

    if (!$update) {
        throw new Exception("Erreur mise à jour: " . $cnx->error);
    }

    // Récupération ID utilisateur
    $result = $cnx->query("SELECT id FROM user WHERE email = '$email'");
    if ($result->num_rows === 1) {
        $_SESSION['user_id'] = $result->fetch_assoc()['id'];
    }

    $_SESSION['authenticated'] = true;
    unset($_SESSION['verification_code']);
    
    header("Location: ../index.php");
    exit();

} catch (Exception $e) {
    $_SESSION['error'] = "Erreur vérification";
    error_log($e->getMessage());
    header("Location: login.php");
    exit();
}
?>