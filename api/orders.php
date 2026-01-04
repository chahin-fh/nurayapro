<?php
require_once dirname(__DIR__) . '/includes/autoload.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

// Récupérer l'action
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        createOrder();
        break;
    case 'get':
        getOrders();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

function createOrder()
{
    global $cnx;

    $user_id = $_SESSION['user_id'];

    // Récupérer les données du formulaire
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    $subtotal = (float) ($_POST['subtotal'] ?? 0);
    $shipping = (float) ($_POST['shipping'] ?? 0);
    $tax = (float) ($_POST['tax'] ?? 0);
    $total = (float) ($_POST['total'] ?? 0);

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($address) || empty($city)) {
        echo json_encode(['success' => false, 'message' => 'Informations incomplètes']);
        return;
    }

    // Récupérer le panier
    $session_id = $_SESSION['cart_session_id'] ?? '';
    $cart_query = "SELECT c.*, p.name, p.price, p.stock_quantity 
                   FROM cart c 
                   LEFT JOIN products p ON c.product_id = p.product_id 
                   WHERE c.session_id = '$session_id' AND p.is_active = 1";
    $cart_result = mysqli_query($cnx, $cart_query);

    if (mysqli_num_rows($cart_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Panier vide']);
        return;
    }

    // Vérifier le stock
    $cart_items = [];
    while ($item = mysqli_fetch_assoc($cart_result)) {
        if ($item['stock_quantity'] < $item['quantity']) {
            echo json_encode(['success' => false, 'message' => 'Stock insuffisant pour: ' . $item['name']]);
            return;
        }
        $cart_items[] = $item;
    }

    // Générer un numéro de commande unique
    $order_number = 'ORD' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // Démarrer la transaction
    mysqli_begin_transaction($cnx);

    try {
        // Insérer la commande
        $insert_order = "INSERT INTO orders (user_id, order_number, first_name, last_name, email, phone, address, city, postal_code, 
                        payment_method, subtotal, shipping_amount, tax_amount, total_amount, status, order_date) 
                        VALUES ($user_id, '$order_number', '$first_name', '$last_name', '$email', '$phone', '$address', 
                        '$city', '$postal_code', '$payment_method', $subtotal, $shipping, $tax, $total, 
                        'pending', NOW())";

        if (!mysqli_query($cnx, $insert_order)) {
            throw new Exception('Erreur lors de la création de la commande');
        }

        $order_id = mysqli_insert_id($cnx);

        // Insérer les articles de la commande
        foreach ($cart_items as $item) {
            $insert_size = empty($item['size']) ? 'NULL' : "'" . mysqli_real_escape_string($cnx, $item['size']) . "'";
            $insert_item = "INSERT INTO order_items (order_id, product_id, size, quantity, price, total) 
                           VALUES ($order_id, {$item['product_id']}, $insert_size, {$item['quantity']}, {$item['price']}, 
                           " . ($item['price'] * $item['quantity']) . ")";

            if (!mysqli_query($cnx, $insert_item)) {
                throw new Exception('Erreur lors de l\'ajout des articles');
            }

            // Mettre à jour le stock
            $update_stock = "UPDATE products SET stock_quantity = stock_quantity - {$item['quantity']} 
                            WHERE product_id = {$item['product_id']}";

            if (!mysqli_query($cnx, $update_stock)) {
                throw new Exception('Erreur lors de la mise à jour du stock');
            }
        }

        // Vider le panier
        $clear_cart = "DELETE FROM cart WHERE session_id = '$session_id'";
        mysqli_query($cnx, $clear_cart);

        // Valider la transaction
        mysqli_commit($cnx);

        echo json_encode([
            'success' => true,
            'message' => 'Commande créée avec succès',
            'order_id' => $order_id,
            'order_number' => $order_number
        ]);

    } catch (Exception $e) {
        // Annuler la transaction
        mysqli_rollback($cnx);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getOrders()
{
    global $cnx;

    $user_id = $_SESSION['user_id'];
    $page = (int) ($_GET['page'] ?? 1);
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $query = "SELECT o.*, COUNT(oi.id) as item_count 
              FROM orders o 
              LEFT JOIN order_items oi ON o.id = oi.order_id 
              WHERE o.user_id = $user_id 
              GROUP BY o.id 
              ORDER BY o.order_date DESC 
              LIMIT $limit OFFSET $offset";

    $result = mysqli_query($cnx, $query);

    $orders = [];
    while ($order = mysqli_fetch_assoc($result)) {
        $orders[] = [
            'id' => $order['id'],
            'order_number' => $order['order_number'],
            'status' => $order['status'],
            'total' => (float) $order['total'],
            'order_date' => $order['order_date'],
            'item_count' => $order['item_count']
        ];
    }

    // Compter le total
    $count_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id";
    $count_result = mysqli_query($cnx, $count_query);
    $total = mysqli_fetch_assoc($count_result)['total'];

    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_orders' => $total
        ]
    ]);
}
?>
