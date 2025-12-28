<?php
require_once 'includes/autoload.php';

$session_id = $_SESSION['cart_session_id'] ?? '';
$cart_query = "SELECT c.*, p.name, p.price, p.image_url, p.stock_quantity
               FROM cart c 
               LEFT JOIN products p ON c.product_id = p.product_id 
               WHERE c.session_id = '$session_id' AND p.is_active = 1";
$cart_result = mysqli_query($cnx, $cart_query);

$cart_items = [];
$total = 0;
$total_items = 0;

while ($item = mysqli_fetch_assoc($cart_result)) {
    $item_total = $item['price'] * $item['quantity'];
    $total += $item_total;
    $total_items += $item['quantity'];

    $cart_items[] = [
        'id' => $item['id'],
        'product_id' => $item['product_id'],
        'name' => $item['name'],
        'price' => (float) $item['price'],
        'quantity' => $item['quantity'],
        'image_url' => get_image_url($item['image_url'], 'Produit'),
        'stock_quantity' => $item['stock_quantity'],
        'total' => $item_total
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier — Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --bg-light: #F5EFE6;
        --bg-white: #FAF7F2;
        --beige-dark: #C8B6A6;
        --text-dark: #1C1C1C;
        --text-gray: #7A7A7A;
        --accent-pink: #E6B7C8;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background: var(--bg-light);
        color: var(--text-dark)
    }

    .cart-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 24px;
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px
    }

    .cart-main {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
    }

    .page-header {
        margin-bottom: 30px
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px
    }

    .page-subtitle {
        color: var(--text-gray);
        font-size: 14px
    }

    .cart-items {
        margin-bottom: 30px
    }

    .cart-item {
        display: flex;
        gap: 20px;
        padding: 20px 0;
        border-bottom: 1px solid rgba(200, 182, 166, 0.1)
    }

    .cart-item:last-child {
        border-bottom: none
    }

    .item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        background: var(--bg-light)
    }

    .item-details {
        flex: 1
    }

    .item-name {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
        font-size: 16px;
        line-height: 1.4
    }

    .item-price {
        color: var(--beige-dark);
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 12px
    }

    .item-actions {
        display: flex;
        align-items: center;
        gap: 16px
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        border: 1px solid rgba(200, 182, 166, 0.3);
        border-radius: 8px;
        overflow: hidden
    }

    .quantity-btn {
        background: var(--bg-light);
        border: none;
        width: 32px;
        height: 32px;
        cursor: pointer;
        font-size: 16px;
        color: var(--text-dark);
        transition: background 0.3s
    }

    .quantity-btn:hover {
        background: var(--beige-dark);
        color: var(--bg-white)
    }

    .quantity-input {
        width: 50px;
        height: 32px;
        border: none;
        text-align: center;
        font-size: 14px;
        font-weight: 600;
        background: var(--bg-white)
    }

    .remove-btn {
        background: none;
        border: none;
        color: var(--text-gray);
        cursor: pointer;
        font-size: 14px;
        transition: color 0.3s
    }

    .remove-btn:hover {
        color: var(--accent-pink)
    }

    .cart-sidebar {
        position: sticky;
        top: 100px
    }

    .order-summary {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
    }

    .summary-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 20px
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(200, 182, 166, 0.1)
    }

    .summary-row:last-child {
        border-bottom: none
    }

    .summary-label {
        color: var(--text-gray);
        font-size: 14px
    }

    .summary-value {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 16px
    }

    .summary-total {
        padding-top: 12px;
        border-top: 2px solid var(--beige-dark);
        margin-top: 12px
    }

    .summary-total .summary-label {
        font-weight: 700;
        font-size: 16px;
        color: var(--text-dark)
    }

    .summary-total .summary-value {
        font-size: 24px;
        color: var(--beige-dark)
    }

    .checkout-btn {
        width: 100%;
        padding: 16px;
        background: var(--beige-dark);
        color: var(--bg-white);
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px
    }

    .checkout-btn:hover {
        background: var(--text-dark);
        transform: translateY(-2px)
    }

    .checkout-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none
    }

    .continue-shopping {
        text-align: center;
        margin-top: 20px
    }

    .continue-shopping a {
        color: var(--beige-dark);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px
    }

    .continue-shopping a:hover {
        text-decoration: underline
    }

    .empty-cart {
        text-align: center;
        padding: 80px 20px;
        background: var(--bg-white);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
    }

    .empty-cart i {
        font-size: 64px;
        color: var(--beige-dark);
        margin-bottom: 24px
    }

    .empty-cart h2 {
        font-size: 24px;
        color: var(--text-dark);
        margin-bottom: 12px
    }

    .empty-cart p {
        color: var(--text-gray);
        margin-bottom: 24px
    }

    .shop-now-btn {
        background: var(--beige-dark);
        color: var(--bg-white);
        text-decoration: none;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease
    }

    .shop-now-btn:hover {
        background: var(--text-dark);
        transform: translateY(-2px)
    }

    @media (max-width:992px) {
        .cart-container {
            grid-template-columns: 1fr;
            gap: 20px
        }

        .cart-sidebar {
            position: static;
        }

        .cart-main {
            padding: 20px;
        }
    }

    @media (max-width:768px) {
        .cart-item {
            flex-direction: row;
            align-items: center;
        }

        .item-image {
            width: 80px;
            height: 80px;
        }

        .item-name {
            font-size: 14px;
        }

        .item-price {
            font-size: 16px;
        }
    }

    @media (max-width:576px) {
        .cart-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .item-image {
            width: 100%;
            height: 200px;
        }

        .item-actions {
            justify-content: space-between;
            width: 100%;
        }

        .quantity-selector {
            flex: 1;
            justify-content: space-between;
            max-width: 150px;
        }
    }
    </style>
</head>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/nuraya_pro/templates/navbar_updated.php'; ?>

    <div class="cart-container">
        <?php if (!empty($cart_items)): ?>
        <div class="cart-main">
            <div class="page-header">
                <h1 class="page-title">Mon Panier</h1>
                <p class="page-subtitle"><?php echo $total_items; ?> article(s)</p>
            </div>

            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"
                        class="item-image"
                        onerror="this.src='https://via.placeholder.com/100x100/F5EFE6/C8B6A6?text=P'">

                    <div class="item-details">
                        <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <div class="item-price"><?php echo number_format($item['price'], 3); ?> DT</div>

                        <div class="item-actions">
                            <div class="quantity-selector">
                                <button class="quantity-btn"
                                    onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">−</button>
                                <input type="number" class="quantity-input" id="quantity-<?php echo $item['id']; ?>"
                                    value="<?php echo $item['quantity']; ?>" min="1"
                                    max="<?php echo $item['stock_quantity']; ?>"
                                    onchange="updateCartQuantity(<?php echo $item['id']; ?>)">
                                <button class="quantity-btn"
                                    onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                            </div>

                            <button class="remove-btn" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="continue-shopping">
                <a href="produits/index.php">
                    <i class="fas fa-arrow-left"></i> Continuer mes achats
                </a>
            </div>
        </div>

        <div class="cart-sidebar">
            <div class="order-summary">
                <h2 class="summary-title">Récapitulatif</h2>

                <div class="summary-row">
                    <span class="summary-label">Sous-total</span>
                    <span class="summary-value"><?php echo number_format($total, 3); ?> DT</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Livraison</span>
                    <span class="summary-value">7.000 DT</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">TVA (19%)</span>
                    <span class="summary-value"><?php echo number_format($total * 0.19, 3); ?> DT</span>
                </div>

                <div class="summary-total">
                    <span class="summary-label">Total</span>
                    <span class="summary-value"><?php echo number_format($total + 7 + ($total * 0.19), 3); ?> DT</span>
                </div>

                <button class="checkout-btn" onclick="proceedToCheckout()">
                    Procéder au paiement
                </button>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-bag"></i>
            <h2>Votre panier est vide</h2>
            <p>Ajoutez des produits à votre panier pour continuer vos achats</p>
            <a href="/nuraya_pro/src/Controllers/produits/index.php" class="shop-now-btn">
                Découvrir les produits
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function updateQuantity(itemId, change) {
        const input = document.getElementById(`quantity-${itemId}`);
        const newValue = parseInt(input.value) + change;
        const maxValue = parseInt(input.max);

        if (newValue >= 1 && newValue <= maxValue) {
            input.value = newValue;
            updateCartQuantity(itemId);
        }
    }

    function updateCartQuantity(itemId) {
        const input = document.getElementById(`quantity-${itemId}`);
        const quantity = parseInt(input.value);

        fetch('api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&product_id=${itemId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Erreur lors de la mise à jour du panier', 'error');
            });
    }

    function removeFromCart(itemId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet article de votre panier ?')) {
            return;
        }

        fetch('api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&product_id=${itemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Erreur lors de la suppression du panier', 'error');
            });
    }

    function proceedToCheckout() {
        // Vérifier si l'utilisateur est connecté
        <?php if (!isset($_SESSION['user_id'])): ?>
        if (confirm('Vous devez être connecté pour procéder au paiement. Voulez-vous vous connecter maintenant ?')) {
            window.location.href = 'login.php?redirect=cart.php';
        }
        return;
        <?php endif; ?>

        // Rediriger vers la page de paiement
        window.location.href = 'checkout.php';
    }
    </script>
</body>

</html>