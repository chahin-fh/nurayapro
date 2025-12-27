<?php
session_start();
header('Content-Type: application/json');

include '../cnx.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        addAddress();
        break;
    case 'delete':
        deleteAddress();
        break;
    case 'set_default':
        setDefaultAddress();
        break;
    case 'update':
        updateAddress();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

function addAddress()
{
    global $cnx, $user_id;

    $title = trim($_POST['title'] ?? 'Mon adresse');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address_line1 = trim($_POST['address_line1'] ?? '');
    $address_line2 = trim($_POST['address_line2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? 'Tunisie');

    if (empty($first_name) || empty($last_name) || empty($phone) || empty($address_line1) || empty($city) || empty($postal_code)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis']);
        return;
    }

    // Réinitialiser par défaut si c'est la première adresse
    $check_query = "SELECT count(*) as count FROM user_addresses WHERE user_id = $user_id";
    $result = mysqli_query($cnx, $check_query);
    $count = mysqli_fetch_assoc($result)['count'];
    $is_default = ($count == 0) ? 1 : 0;

    $stmt = mysqli_prepare($cnx, "INSERT INTO user_addresses (user_id, title, first_name, last_name, phone, address_line1, address_line2, city, postal_code, country, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssssssssi", $user_id, $title, $first_name, $last_name, $phone, $address_line1, $address_line2, $city, $postal_code, $country, $is_default);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Adresse ajoutée avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . mysqli_error($cnx)]);
    }
}

function deleteAddress()
{
    global $cnx, $user_id;

    $address_id = (int)($_POST['address_id'] ?? 0);

    if ($address_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        return;
    }

    // Vérifier que l'adresse appartient à l'utilisateur
    $check_query = "SELECT is_default FROM user_addresses WHERE id = $address_id AND user_id = $user_id";
    $result = mysqli_query($cnx, $check_query);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Adresse introuvable']);
        return;
    }

    $is_default = mysqli_fetch_assoc($result)['is_default'];

    if ($is_default) {
        echo json_encode(['success' => false, 'message' => 'Impossible de supprimer l\'adresse par défaut']);
        return;
    }

    $delete_query = "DELETE FROM user_addresses WHERE id = $address_id AND user_id = $user_id";
    if (mysqli_query($cnx, $delete_query)) {
        echo json_encode(['success' => true, 'message' => 'Adresse supprimée']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de suppression']);
    }
}

function setDefaultAddress()
{
    global $cnx, $user_id;

    $address_id = (int)($_POST['address_id'] ?? 0);

    if ($address_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        return;
    }

    mysqli_begin_transaction($cnx);

    try {
        // Enlever le défaut des autres adresses
        mysqli_query($cnx, "UPDATE user_addresses SET is_default = 0 WHERE user_id = $user_id");

        // Mettre la nouvelle adresse par défaut
        $update_query = "UPDATE user_addresses SET is_default = 1 WHERE id = $address_id AND user_id = $user_id";
        mysqli_query($cnx, $update_query);

        mysqli_commit($cnx);
        echo json_encode(['success' => true, 'message' => 'Adresse par défaut mise à jour']);
    } catch (Exception $e) {
        mysqli_rollback($cnx);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
    }
}
?>

<?php
function updateAddress()
{
    global $cnx, $user_id;

    $address_id = (int)($_POST['address_id'] ?? 0);
    $title = trim($_POST['title'] ?? 'Mon adresse');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address_line1 = trim($_POST['address_line1'] ?? '');
    $address_line2 = trim($_POST['address_line2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? 'Tunisie');

    if ($address_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        return;
    }

    if (empty($first_name) || empty($last_name) || empty($phone) || empty($address_line1) || empty($city) || empty($postal_code)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis']);
        return;
    }

    // Vérifier que l'adresse appartient à l'utilisateur
    $check_query = "SELECT id FROM user_addresses WHERE id = $address_id AND user_id = $user_id";
    $result = mysqli_query($cnx, $check_query);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Adresse introuvable']);
        return;
    }

    $stmt = mysqli_prepare($cnx, "UPDATE user_addresses SET title=?, first_name=?, last_name=?, phone=?, address_line1=?, address_line2=?, city=?, postal_code=?, country=? WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($stmt, "sssssssssii", $title, $first_name, $last_name, $phone, $address_line1, $address_line2, $city, $postal_code, $country, $address_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Adresse mise à jour avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . mysqli_error($cnx)]);
    }
}
?>
