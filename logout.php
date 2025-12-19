<?php
session_start();
require_once 'cnx.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
mysqli_autocommit($cnx, false);

$success = true;

try {
    $query = "DELETE FROM user WHERE id = ?";
    $stmt = mysqli_prepare($cnx, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        $success = false;
        throw new Exception("Erreur lors de la suppression de l'utilisateur");
    }
    if ($success) {
        mysqli_commit($cnx);
    } else {
        mysqli_rollback($cnx);
    }

} catch (Exception $e) {
    mysqli_rollback($cnx);
    error_log("Erreur de base de données: " . $e->getMessage());
} finally {
    mysqli_autocommit($cnx, true);
}


$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

setcookie('remember_token', '', time() - 3600, '/');

mysqli_close($cnx);
header("Location: index.php");
exit();
?>