<?php
require_once 'includes/autoload.php';

// Vérifier si l'utilisateur est connecté
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Récupérer l'ID de commande depuis l'URL
$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($order_id <= 0) {
    header('Location: account.php');
    exit;
}

// Récupérer les détails de la commande
$order_query = "SELECT o.*, u.first_name, u.last_name, u.email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id = $order_id AND o.user_id = " . $_SESSION['user_id'];
$order_result = mysqli_query($cnx, $order_query);

if (mysqli_num_rows($order_result) === 0) {
    header('Location: account.php');
    exit;
}

$order = mysqli_fetch_assoc($order_result);

// Ensure shipping, tax, and total are set (with defaults if missing)
$order['shipping'] = isset($order['shipping']) ? (float)$order['shipping'] : 0;
$order['tax'] = isset($order['tax']) ? (float)$order['tax'] : 0;
$order['total'] = isset($order['total']) ? (float)$order['total'] : 0;
$order['subtotal'] = isset($order['subtotal']) ? (float)$order['subtotal'] : 0;

// Récupérer les articles de la commande
$items_query = "SELECT oi.*, p.name, p.image_url 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.product_id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($cnx, $items_query);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Commande — Nuraya</title>
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

        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 24px
        }

        .success-card {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(200, 182, 166, 0.15);
            text-align: center;
            margin-bottom: 30px
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--beige-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: var(--bg-white);
            font-size: 32px
        }

        .success-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 12px
        }

        .success-message {
            color: var(--text-gray);
            font-size: 16px;
            margin-bottom: 24px;
            line-height: 1.6
        }

        .order-number {
            display: inline-block;
            background: var(--bg-light);
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 24px
        }

        .order-details {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
            margin-bottom: 30px
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px
        }

        .detail-section h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(200, 182, 166, 0.2)
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px
        }

        .detail-label {
            color: var(--text-gray)
        }

        .detail-value {
            color: var(--text-dark);
            font-weight: 600
        }

        .order-items {
            margin-top: 30px
        }

        .items-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(200, 182, 166, 0.2)
        }

        .order-item {
            display: flex;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid rgba(200, 182, 166, 0.1)
        }

        .order-item:last-child {
            border-bottom: none
        }

        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background: var(--bg-light)
        }

        .item-info {
            flex: 1
        }

        .item-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
            font-size: 14px
        }

        .item-quantity {
            color: var(--text-gray);
            font-size: 13px
        }

        .item-price {
            font-weight: 700;
            color: var(--beige-dark);
            font-size: 16px
        }

        .order-summary {
            background: var(--bg-light);
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px
        }

        .summary-row:last-child {
            margin-bottom: 0;
            padding-top: 8px;
            border-top: 1px solid rgba(200, 182, 166, 0.2);
            font-weight: 700;
            font-size: 16px
        }

        .action-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 30px
        }

        .btn {
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer
        }

        .btn-primary {
            background: var(--beige-dark);
            color: var(--bg-white)
        }

        .btn-primary:hover {
            background: var(--text-dark);
            transform: translateY(-2px)
        }

        .btn-secondary {
            background: var(--bg-white);
            color: var(--text-dark);
            border: 1px solid rgba(200, 182, 166, 0.3)
        }

        .btn-secondary:hover {
            background: var(--bg-light);
            transform: translateY(-2px)
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 8px
        }

        .status-pending {
            background: #fff3cd;
            color: #856404
        }

        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460
        }

        .status-processing {
            background: #d1ecf1;
            color: #0c5460
        }

        .status-shipped {
            background: #d4edda;
            color: #155724
        }

        .status-delivered {
            background: #d1ecf1;
            color: #0c5460
        }

        @media (max-width:768px) {
            .confirmation-container {
                padding: 0 16px;
                margin: 20px auto
            }

            .success-card {
                padding: 30px 20px
            }

            .details-grid {
                grid-template-columns: 1fr;
                gap: 20px
            }

            .order-item {
                flex-direction: column;
                gap: 12px
            }

            .action-buttons {
                flex-direction: column
            }

            .btn {
                width: 100%;
                justify-content: center
            }
        }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="confirmation-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="success-title">Commande Confirmée !</h1>
            <p class="success-message">
                Merci pour votre commande. Nous vous enverrons une confirmation par email avec les détails de votre
                commande.
            </p>
            <div class="order-number">
                Commande #<?php echo htmlspecialchars($order['order_number']); ?>
            </div>
            <span class="status-badge status-<?php echo $order['status']; ?>">
                <?php
                $statusLabels = [
                    'pending' => 'En attente',
                    'confirmed' => 'Confirmée',
                    'processing' => 'En traitement',
                    'shipped' => 'Expédiée',
                    'delivered' => 'Livrée',
                    'cancelled' => 'Annulée'
                ];
                echo $statusLabels[$order['status']] ?? $order['status'];
                ?>
            </span>
        </div>

        <div class="order-details">
            <div class="details-grid">
                <div class="detail-section">
                    <h3>Informations de livraison</h3>
                    <div class="detail-item">
                        <span class="detail-label">Nom</span>
                        <span
                            class="detail-value"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['email']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Téléphone</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['phone']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Adresse</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['address']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Ville</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['city']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Code postal</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['postal_code']); ?></span>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Détails de la commande</h3>
                    <div class="detail-item">
                        <span class="detail-label">Numéro de commande</span>
                        <span class="detail-value">#<?php echo htmlspecialchars($order['order_number']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date</span>
                        <span
                            class="detail-value"><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Méthode de paiement</span>
                        <span
                            class="detail-value"><?php echo $order['payment_method'] === 'cash' ? 'Paiement à la livraison' : 'Carte bancaire'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Statut</span>
                        <span
                            class="detail-value"><?php echo $statusLabels[$order['status']] ?? $order['status']; ?></span>
                    </div>
                </div>
            </div>

            <div class="order-items">
                <h3 class="items-title">Articles commandés</h3>
                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                    <div class="order-item">
                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"
                            class="item-image" onerror="this.src='https://via.placeholder.com/60x60/F5EFE6/C8B6A6?text=P'">

                        <div class="item-info">
                            <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="item-quantity">Quantité: <?php echo $item['quantity']; ?></div>
                        </div>

                        <div class="item-price"><?php echo number_format($item['total'], 3); ?> DT</div>
                    </div>
                <?php endwhile; ?>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Sous-total</span>
                        <span><?php echo number_format($order['subtotal'], 3); ?> DT</span>
                    </div>
                    <div class="summary-row">
                        <span>Livraison</span>
                        <span><?php echo number_format($order['shipping'], 3); ?> DT</span>
                    </div>
                    <div class="summary-row">
                        <span>TVA (19%)</span>
                        <span><?php echo number_format($order['tax'], 3); ?> DT</span>
                    </div>
                    <div class="summary-row">
                        <span>Total</span>
                        <span><?php echo number_format($order['total'], 3); ?> DT</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="account.php" class="btn btn-secondary">
                <i class="fas fa-user"></i>
                Mon Compte
            </a>
            <a href="shop.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i>
                Continuer mes achats
            </a>
        </div>
    </div>
</body>

</html>