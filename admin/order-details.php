<?php
require_once 'includes/auth_check.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    header('Location: orders.php');
    exit;
}

// Récupérer les détails de la commande
$order_query = "SELECT o.*, u.first_name as user_fname, u.last_name as user_lname, u.email as user_email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id = $order_id";
$order_result = mysqli_query($cnx, $order_query);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Récupérer les articles de la commande
$items_query = "SELECT oi.*, p.name as product_name, p.image_url 
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
    <title>Commande #<?php echo $order['order_number']; ?> - Admin Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-common.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <div>
                    <a href="orders.php" style="color: var(--text-gray); text-decoration: none; font-size: 14px; display: block; margin-bottom: 10px;">
                        <i class="fas fa-arrow-left"></i> Retour aux commandes
                    </a>
                    <h1>Commande #<?php echo $order['order_number']; ?></h1>
                </div>
                <span class="status-badge status-<?php echo $order['status']; ?>">
                    <?php echo $order['status']; ?>
                </span>
            </div>
            
            <div class="content-grid" style="grid-template-columns: 2fr 1fr;">
                <div class="left-col">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-shopping-basket"></i> Articles de la commande</h3>
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Prix</th>
                                        <th>Quantité</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap:15px;">
                                                    <img src="../<?php echo htmlspecialchars($item['image_url']); ?>" alt="" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo number_format($item['price'], 2); ?> DT</td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><strong><?php echo number_format($item['price'] * $item['quantity'], 2); ?> DT</strong></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div style="display: flex; justify-content: flex-end; margin-top: 30px; gap: 40px; border-top: 1px solid rgba(200,182,166,0.1); padding-top: 20px;">
                            <div style="text-align: right;">
                                <div class="info-row" style="margin-bottom: 10px;">
                                    <span class="info-label">Sous-total:</span>
                                    <span class="info-value"><?php echo number_format($order['subtotal'], 2); ?> DT</span>
                                </div>
                                <div class="info-row" style="margin-bottom: 15px;">
                                    <span class="info-label">Livraison:</span>
                                    <span class="info-value"><?php echo number_format($order['shipping_amount'], 2); ?> DT</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label" style="font-size: 18px;">TOTAL:</span>
                                    <span style="font-size: 24px; font-weight: 700; color: var(--beige-dark);"><?php echo number_format($order['total_amount'], 2); ?> DT</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="right-col">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-user"></i> Informations Client</h3>
                        </div>
                        <div class="info-row" style="margin-bottom: 15px;">
                            <span class="info-label">Nom:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></span>
                        </div>
                        <div class="info-row" style="margin-bottom: 15px;">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Téléphone:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['phone']); ?></span>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-map-marker-alt"></i> Adresse de livraison</h3>
                        </div>
                        <p style="font-size: 14px; line-height: 1.6; color: var(--text-dark);">
                            <?php echo nl2br(htmlspecialchars($order['address'])); ?><br>
                            <?php echo htmlspecialchars($order['postal_code'] . ' ' . $order['city']); ?><br>
                            <?php echo htmlspecialchars($order['country']); ?>
                        </p>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-cog"></i> Actions Commande</h3>
                        </div>
                        <div class="form-group">
                            <label>Changer le statut</label>
                            <select onchange="updateOrderStatus(<?php echo $order_id; ?>, this.value)">
                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>En attente</option>
                                <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmée</option>
                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>En préparation</option>
                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Expédiée</option>
                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Livrée</option>
                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function updateOrderStatus(orderId, newStatus) {
            fetch('api/orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update_status&order_id=${orderId}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    </script>
</body>
</html>
