<?php
session_start();
header('Content-Type: application/json');

// Vérifier l'authentification admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

require_once __DIR__ . '/../../../src/Controllers/cnx.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'delete':
        deleteProduct();
        break;
    case 'toggle_status':
        toggleStatus();
        break;
    case 'update_stock':
        updateStock();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action invalide']);
}

function deleteProduct()
{
    global $cnx;
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        return;
    }
    
    // Vérifier si le produit a des commandes
    $check_query = "SELECT COUNT(*) as count FROM order_items WHERE product_id = $product_id";
    $result = mysqli_query($cnx, $check_query);
    $count = mysqli_fetch_assoc($result)['count'];
    
    if ($count > 0) {
        // Ne pas supprimer, juste désactiver
        $update_query = "UPDATE products SET is_active = 0 WHERE product_id = $product_id";
        mysqli_query($cnx, $update_query);
        echo json_encode(['success' => true, 'message' => 'Produit désactivé (présent dans des commandes)']);
    } else {
        // Supprimer complètement
        $delete_query = "DELETE FROM products WHERE product_id = $product_id";
        if (mysqli_query($cnx, $delete_query)) {
            echo json_encode(['success' => true, 'message' => 'Produit supprimé']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur de suppression']);
        }
    }
}

function toggleStatus()
{
    global $cnx;
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    $query = "UPDATE products SET is_active = NOT is_active WHERE product_id = $product_id";
    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
    }
}

function updateStock()
{
    global $cnx;
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    
    $query = "UPDATE products SET stock_quantity = $quantity WHERE product_id = $product_id";
    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Stock mis à jour']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
    }
}
?>
