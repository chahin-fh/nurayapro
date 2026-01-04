<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';

// Générer ou récupérer l'ID de session du panier
if (!isset($_SESSION['cart_session_id'])) {
    $_SESSION['cart_session_id'] = session_id();
}

$session_id = $_SESSION['cart_session_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        addToCart();
        break;
    case 'remove':
        removeFromCart();
        break;
    case 'update':
        updateCart();
        break;
    case 'count':
        getCartCount();
        break;
    case 'get':
        getCart();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action invalide']);
}

function addToCart()
{
    global $cnx, $session_id;

    $product_id = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);
    $size = trim((string) ($_POST['size'] ?? ''));
    $size = $size === '' ? null : $size;

    if ($product_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
        return;
    }

    // Vérifier que le produit existe et est actif
    $product_query = "SELECT product_id, stock_quantity FROM products WHERE product_id = $product_id AND is_active = 1";
    $product_result = mysqli_query($cnx, $product_query);

    if (!$product_result || mysqli_num_rows($product_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Produit introuvable']);
        return;
    }

    $product = mysqli_fetch_assoc($product_result);

    // Vérifier si le produit a des tailles définies
    $size_rows_result = mysqli_query($cnx, "SELECT size FROM product_sizes WHERE product_id = $product_id ORDER BY sort_order ASC, id ASC");
    if ($size_rows_result && mysqli_num_rows($size_rows_result) > 0) {
        if ($size === null) {
            echo json_encode(['success' => false, 'message' => 'Veuillez choisir une taille']);
            return;
        }

        $escaped_size = mysqli_real_escape_string($cnx, $size);
        $check_size = mysqli_query($cnx, "SELECT id FROM product_sizes WHERE product_id = $product_id AND size = '$escaped_size' LIMIT 1");
        if (!$check_size || mysqli_num_rows($check_size) === 0) {
            echo json_encode(['success' => false, 'message' => 'Taille invalide']);
            return;
        }
    } else {
        // Aucun système de tailles pour ce produit
        $size = null;
    }

    // Vérifier le stock
    if ($product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
        return;
    }

    // Vérifier si le produit est déjà dans le panier
    $check_size_sql = $size === null
        ? 'IS NULL'
        : "= '" . mysqli_real_escape_string($cnx, $size) . "'";
    $check_query = "SELECT id, quantity FROM cart WHERE session_id = '$session_id' AND product_id = $product_id AND size $check_size_sql";
    $check_result = mysqli_query($cnx, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Mettre à jour la quantité
        $existing = mysqli_fetch_assoc($check_result);
        $new_quantity = $existing['quantity'] + $quantity;

        if ($new_quantity > $product['stock_quantity']) {
            echo json_encode(['success' => false, 'message' => 'Stock insuffisant pour cette quantité']);
            return;
        }

        $update_query = "UPDATE cart SET quantity = $new_quantity WHERE id = " . $existing['id'];
        mysqli_query($cnx, $update_query);
    } else {
        // Insérer un nouveau produit dans le panier
        $insert_size = $size === null ? 'NULL' : "'" . mysqli_real_escape_string($cnx, $size) . "'";
        $insert_query = "INSERT INTO cart (session_id, product_id, size, quantity) VALUES ('$session_id', $product_id, $insert_size, $quantity)";
        mysqli_query($cnx, $insert_query);
    }

    echo json_encode(['success' => true, 'message' => 'Produit ajouté au panier']);
}

function removeFromCart()
{
    global $cnx, $session_id;

    $cart_item_id = (int) ($_POST['cart_item_id'] ?? 0);
    $product_id = (int) ($_POST['product_id'] ?? 0);

    if ($cart_item_id <= 0 && $product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
        return;
    }

    if ($cart_item_id > 0) {
        $delete_query = "DELETE FROM cart WHERE session_id = '$session_id' AND id = $cart_item_id";
    } else {
        $delete_query = "DELETE FROM cart WHERE session_id = '$session_id' AND product_id = $product_id";
    }

    if (mysqli_query($cnx, $delete_query)) {
        echo json_encode(['success' => true, 'message' => 'Produit supprimé du panier']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }
}

function updateCart()
{
    global $cnx, $session_id;

    $cart_item_id = (int) ($_POST['cart_item_id'] ?? 0);
    $product_id = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);

    if ($cart_item_id <= 0 && $product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
        return;
    }

    if ($quantity <= 0) {
        // Supprimer si la quantité est 0 ou négative
        removeFromCart();
        return;
    }

    if ($cart_item_id > 0) {
        $cart_row_result = mysqli_query($cnx, "SELECT product_id FROM cart WHERE session_id = '$session_id' AND id = $cart_item_id");
        $cart_row = $cart_row_result ? mysqli_fetch_assoc($cart_row_result) : null;
        if (!$cart_row) {
            echo json_encode(['success' => false, 'message' => 'Article introuvable']);
            return;
        }
        $product_id = (int) $cart_row['product_id'];
    }

    // Vérifier le stock
    $product_query = "SELECT stock_quantity FROM products WHERE product_id = $product_id AND is_active = 1";
    $product_result = mysqli_query($cnx, $product_query);
    $product = mysqli_fetch_assoc($product_result);

    if (!$product || $product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
        return;
    }

    if ($cart_item_id > 0) {
        $update_query = "UPDATE cart SET quantity = $quantity WHERE session_id = '$session_id' AND id = $cart_item_id";
    } else {
        $update_query = "UPDATE cart SET quantity = $quantity WHERE session_id = '$session_id' AND product_id = $product_id";
    }

    if (mysqli_query($cnx, $update_query)) {
        echo json_encode(['success' => true, 'message' => 'Panier mis à jour']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
    }
}

function getCartCount()
{
    global $cnx, $session_id;

    $count_query = "SELECT SUM(quantity) as total FROM cart WHERE session_id = '$session_id'";
    $count_result = mysqli_query($cnx, $count_query);
    $count_data = mysqli_fetch_assoc($count_result);

    $count = $count_data['total'] ?? 0;

    echo json_encode(['success' => true, 'count' => (int) $count]);
}

function getCart()
{
    global $cnx, $session_id;

    $cart_query = "SELECT c.*, p.name, p.price, p.image_url, p.stock_quantity
                   FROM cart c 
                   LEFT JOIN products p ON c.product_id = p.product_id 
                   WHERE c.session_id = '$session_id' AND p.is_active = 1";
    $cart_result = mysqli_query($cnx, $cart_query);

    $cart_items = [];
    $total = 0;

    while ($item = mysqli_fetch_assoc($cart_result)) {
        $item_total = $item['price'] * $item['quantity'];
        $total += $item_total;

        $cart_items[] = [
            'id' => $item['id'],
            'product_id' => $item['product_id'],
            'size' => $item['size'],
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
        'items' => $cart_items,
        'total' => $total,
        'count' => array_sum(array_column($cart_items, 'quantity'))
    ]);
}
?>