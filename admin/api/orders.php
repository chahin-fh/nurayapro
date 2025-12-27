<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

require_once __DIR__ . '/../../../src/Controllers/cnx.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_status':
        updateOrderStatus();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action invalide']);
}

function updateOrderStatus()
{
    global $cnx;
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = mysqli_real_escape_string($cnx, $_POST['status'] ?? '');
    
    $valid_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Statut invalide']);
        return;
    }
    
    $query = "UPDATE orders SET status = '$status', updated_at = NOW() WHERE id = $order_id";
    
    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
    }
}
?>
