<?php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
include $_SERVER['DOCUMENT_ROOT'] . '/nurayapro/config/database.php';

// Récupérer l'action demandée
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addToCart();
        break;
    case 'update':
        updateCart();
        break;
    case 'remove':
        removeFromCart();
        break;
    case 'clear':
        clearCart();
        break;
    case 'get':
        getCart();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

function addToCart()
{
    global $cnx;

    $product_id = (int) $_POST['product_id'];
    $quantity = (int) ($_POST['quantity'] ?? 1);

    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        return;
    }

    // Vérifier si le produit existe et est en stock
    $product_query = "SELECT name, price, stock_quantity, image_url FROM products WHERE product_id = $product_id AND is_active = 1";
    $product_result = mysqli_query($cnx, $product_query);

    if (mysqli_num_rows($product_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Produit non disponible']);
        return;
    }

    $product = mysqli_fetch_assoc($product_result);

    if ($product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
        return;
    }

    // Créer un identifiant de session si nécessaire
    if (!isset($_SESSION['cart_session_id'])) {
        $_SESSION['cart_session_id'] = session_id() . '_' . time();
    }

    $session_id = $_SESSION['cart_session_id'];
    $user_id = $_SESSION['user_id'] ?? null;

    // Vérifier si le produit est déjà dans le panier
    $existing_query = "SELECT id, quantity FROM cart WHERE session_id = '$session_id' AND product_id = $product_id";
    $existing_result = mysqli_query($cnx, $existing_query);

    if (mysqli_num_rows($existing_result) > 0) {
        $existing = mysqli_fetch_assoc($existing_result);
        $new_quantity = $existing['quantity'] + $quantity;

        if ($product['stock_quantity'] < $new_quantity) {
            echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
            return;
        }

        $update_query = "UPDATE cart SET quantity = $new_quantity WHERE id = " . $existing['id'];
        mysqli_query($cnx, $update_query);
    } else {
        $insert_query = "INSERT INTO cart (user_id, session_id, product_id, quantity) VALUES ($user_id, '$session_id', $product_id, $quantity)";
        mysqli_query($cnx, $insert_query);
    }

    echo json_encode(['success' => true, 'message' => 'Produit ajouté au panier']);
}

function updateCart()
{
    global $cnx;

    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        return;
    }

    $session_id = $_SESSION['cart_session_id'] ?? '';

    // Vérifier le stock
    $stock_query = "SELECT stock_quantity FROM products WHERE product_id = $product_id";
    $stock_result = mysqli_query($cnx, $stock_query);
    $stock = mysqli_fetch_assoc($stock_result)['stock_quantity'];

    if ($stock < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
        return;
    }

    $update_query = "UPDATE cart SET quantity = $quantity WHERE session_id = '$session_id' AND product_id = $product_id";
    mysqli_query($cnx, $update_query);

    echo json_encode(['success' => true, 'message' => 'Panier mis à jour']);
}

function removeFromCart()
{
    global $cnx;

    $product_id = (int) $_POST['product_id'];
    $session_id = $_SESSION['cart_session_id'] ?? '';

    $delete_query = "DELETE FROM cart WHERE session_id = '$session_id' AND product_id = $product_id";
    mysqli_query($cnx, $delete_query);

    echo json_encode(['success' => true, 'message' => 'Produit retiré du panier']);
}

function clearCart()
{
    global $cnx;

    $session_id = $_SESSION['cart_session_id'] ?? '';

    $clear_query = "DELETE FROM cart WHERE session_id = '$session_id'";
    mysqli_query($cnx, $clear_query);

    echo json_encode(['success' => true, 'message' => 'Panier vidé']);
}

function getCart()
{
    global $cnx;

    $session_id = $_SESSION['cart_session_id'] ?? '';

    $cart_query = "SELECT c.*, p.name, p.price, p.image_url, p.stock_quantity 
                  FROM cart c 
                  LEFT JOIN products p ON c.product_id = p.product_id 
                  WHERE c.session_id = '$session_id'";
    $cart_result = mysqli_query($cnx, $cart_query);

    $items = [];
    $total = 0;
    $total_items = 0;

    while ($item = mysqli_fetch_assoc($cart_result)) {
        $item_total = $item['price'] * $item['quantity'];
        $total += $item_total;
        $total_items += $item['quantity'];

        $items[] = [
            'id' => $item['id'],
            'product_id' => $item['product_id'],
            'name' => $item['name'],
            'price' => (float) $item['price'],
            'quantity' => $item['quantity'],
            'image_url' => $item['image_url'],
            'stock_quantity' => $item['stock_quantity'],
            'total' => $item_total
        ];
    }

    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'total_items' => $total_items
    ]);
}

mysqli_close($cnx);
?>