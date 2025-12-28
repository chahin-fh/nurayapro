<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Connexion à la base de données
require_once '../config/database.php';

// Récupérer l'action demandée
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addToWishlist();
        break;
    case 'remove':
        removeFromWishlist();
        break;
    case 'get':
        getWishlist();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

function addToWishlist()
{
    global $cnx;

    $user_id = $_SESSION['user_id'];
    $product_id = (int) $_POST['product_id'];

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID produit invalide']);
        return;
    }

    // Vérifier si le produit existe
    $product_query = "SELECT id FROM products WHERE product_id = $product_id AND is_active = 1";
    $product_result = mysqli_query($cnx, $product_query);

    if (mysqli_num_rows($product_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Produit non disponible']);
        return;
    }

    // Vérifier si déjà dans les favoris
    $existing_query = "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    $existing_result = mysqli_query($cnx, $existing_query);

    if (mysqli_num_rows($existing_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Déjà dans les favoris']);
        return;
    }

    // Ajouter aux favoris
    $insert_query = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)";

    if (mysqli_query($cnx, $insert_query)) {
        echo json_encode(['success' => true, 'message' => 'Ajouté aux favoris']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
    }
}

function removeFromWishlist()
{
    global $cnx;

    $user_id = $_SESSION['user_id'];
    $product_id = (int) $_POST['product_id'];

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID produit invalide']);
        return;
    }

    $delete_query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";

    if (mysqli_query($cnx, $delete_query)) {
        echo json_encode(['success' => true, 'message' => 'Retiré des favoris']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors du retrait']);
    }
}

function getWishlist()
{
    global $cnx;

    $user_id = $_SESSION['user_id'];

    $wishlist_query = "SELECT w.*, p.name, p.price, p.image_url, c.name as category_name, c.slug as category_slug
                      FROM wishlist w 
                      LEFT JOIN products p ON w.product_id = p.product_id 
                      LEFT JOIN categories c ON p.category_id = c.category_id 
                      WHERE w.user_id = $user_id AND p.is_active = 1
                      ORDER BY w.created_at DESC";
    $wishlist_result = mysqli_query($cnx, $wishlist_query);

    $items = [];

    while ($item = mysqli_fetch_assoc($wishlist_result)) {
        $items[] = [
            'id' => $item['id'],
            'product_id' => $item['product_id'],
            'name' => $item['name'],
            'price' => (float) $item['price'],
            'image_url' => $item['image_url'],
            'category_name' => $item['category_name'],
            'category_slug' => $item['category_slug'],
            'created_at' => $item['created_at']
        ];
    }

    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => count($items)
    ]);
}

mysqli_close($cnx);
?>