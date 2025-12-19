<?php
session_start();
include("cnx.php");

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    // Requête préparée pour éviter les injections SQL
    $stmt = $cnx->prepare("SELECT id, is_verified FROM user WHERE verification_code = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si un utilisateur a été trouvé avec ce token
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row['is_verified'] == 0) {
            $user_id = $row['id'];

            $update_stmt = $cnx->prepare("UPDATE user SET is_verified = 1 WHERE id = ?");
            $update_stmt->bind_param("i", $user_id);

            if ($update_stmt->execute()) {
                $_SESSION['status'] = "Votre compte a été vérifié avec succès !";
            } else {
                $_SESSION['status'] = "La vérification a échoué. Veuillez réessayer.";
            }
        } else {
            $_SESSION['status'] = "Votre email est déjà vérifié. Veuillez vous connecter.";
        }
    } else {
        $_SESSION['status'] = "Lien de vérification invalide ou expiré.";
    }

    header("Location: login.php");
    mysqli_close($cnx);
    exit();
} else {
    $_SESSION['status'] = "Accès non autorisé.";
    header("Location: login.php");
    mysqli_close($cnx);
    exit();
}
?>
