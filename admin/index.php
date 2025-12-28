<?php
require_once 'includes/auth_check.php';

// Statistiques
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()) as today_orders,
    (SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled') as total_revenue,
    (SELECT SUM(total_amount) FROM orders WHERE DATE(order_date) = CURDATE()) as today_revenue,
    (SELECT COUNT(*) FROM users WHERE role = 'user') as total_users,
    (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as new_users_today,
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM products WHERE stock_quantity <= min_stock_level) as low_stock_products";

$stats_result = mysqli_query($cnx, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Commandes récentes
$recent_orders_query = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5";
$recent_orders = mysqli_query($cnx, $recent_orders_query);

// Produits en rupture
$low_stock_query = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    JOIN categories c ON p.category_id = c.category_id 
                    WHERE p.stock_quantity <= p.min_stock_level 
                    LIMIT 5";
$low_stock = mysqli_query($cnx, $low_stock_query);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-common.css">
    <script src="../assets/js/toast.js"></script>
</head>

<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="page-header">
                <h1>Tableau de bord</h1>
                <p>Bienvenue sur votre interface de gestion Nuraya.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="value"><?php echo number_format($stats['total_revenue'] ?? 0, 2); ?> DT</div>
                    <div class="label">Revenu Total</div>
                </div>
                <div class="stat-card green">
                    <div class="icon"><i class="fas fa-cart-shopping"></i></div>
                    <div class="value"><?php echo $stats['total_orders']; ?></div>
                    <div class="label">Commandes Totales</div>
                </div>
                <div class="stat-card orange">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="value"><?php echo $stats['total_users']; ?></div>
                    <div class="label">Utilisateurs Clients</div>
                </div>
                <div class="stat-card purple">
                    <div class="icon"><i class="fas fa-box-open"></i></div>
                    <div class="value"><?php echo $stats['total_products']; ?></div>
                    <div class="label">Produits en Ligne</div>
                </div>
            </div>

            <div class="content-grid">
                <!-- Dernières Commandes -->
                <div class="card">
                    <div class="card-header">
                        <h3>Dernières Commandes</h3>
                        <a href="orders.php">Voir tout</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                <tr>
                                    <td>#<?php echo $order['order_number']; ?></td>
                                    <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                                    </td>
                                    <td><strong><?php echo number_format($order['total_amount'], 2); ?> DT</strong></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Stock Faible -->
                <div class="card">
                    <div class="card-header">
                        <h3>Alertes Stock</h3>
                        <a href="products.php?stock=low">Gérer</a>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($low_stock) > 0): ?>
                                <?php while ($product = mysqli_fetch_assoc($low_stock)): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($product['name']); ?>
                                        </div>
                                        <small
                                            style="color: var(--text-gray);"><?php echo htmlspecialchars($product['category_name']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-inactive">
                                            <?php echo $product['stock_quantity']; ?> restant(s)
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; color: var(--text-gray); padding: 30px;">
                                        Aucune alerte de stock
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>