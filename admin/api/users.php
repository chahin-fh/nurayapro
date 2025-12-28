<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

require_once '../../config/database.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'toggle_status':
        toggleUserStatus();
        break;
    case 'update_role':
        updateUserRole();
        break;
    case 'delete':
        deleteUser();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action invalide']);
}

function toggleUserStatus()
{
    global $cnx;
    $user_id = (int) ($_POST['user_id'] ?? 0);
    $status = (int) ($_POST['status'] ?? 1);

    $query = "UPDATE users SET is_active = $status WHERE id = $user_id AND role != 'admin'";

    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
    }
}

function updateUserRole()
{
    global $cnx;
    $user_id = (int) ($_POST['user_id'] ?? 0);
    $role = mysqli_real_escape_string($cnx, $_POST['role'] ?? 'user');

    if (!in_array($role, ['user', 'admin'])) {
        echo json_encode(['success' => false, 'message' => 'Rôle invalide']);
        return;
    }

    $query = "UPDATE users SET role = '$role' WHERE id = $user_id";

    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Rôle mis à jour']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
    }
}

function deleteUser()
{
    global $cnx;
    $user_id = (int) ($_POST['user_id'] ?? 0);

    // Empêcher la suppression de son propre compte ou d'un autre admin si besoin
    if ($user_id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte']);
        return;
    }

    $query = "DELETE FROM users WHERE id = $user_id AND role != 'admin'";

    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }
}
?>