<?php
require_once 'includes/auth_check.php';

// Récupérer les statistiques
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()) as today_orders,
    (SELECT SUM(total) FROM orders WHERE status != 'cancelled') as total_revenue,
    (SELECT SUM(total) FROM orders WHERE DATE(order_date) = CURDATE()) as today_revenue,
    (SELECT COUNT(*) FROM users WHERE role = 'user') as total_users,
    (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as new_users_today,
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM products WHERE stock_quantity <= min_stock_level) as low_stock_products";

$stats_result = mysqli_query($cnx, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Commandes récentes
$recent_orders_query = "SELECT o.*, u.first_name, u.last_name, u.email 
                        FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.id 
                        ORDER BY o.order_date DESC 
                        LIMIT 10";
$recent_orders = mysqli_query($cnx, $recent_orders_query);

// Produits en rupture de stock
$low_stock_query = "SELECT product_id, name, stock_quantity, min_stock_level 
                    FROM products 
                    WHERE stock_quantity <= min_stock_level 
                    ORDER BY stock_quantity ASC 
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #f5f6fa;
            color: #2c3e50;
        }

        .admin-layout {
            display: flex;
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #7f8c8d;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .stat-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }

        .stat-card.blue .icon { background: #e3f2fd; color: #2196f3; }
        .stat-card.green .icon { background: #e8f5e9; color: #4caf50; }
        .stat-card.orange .icon { background: #fff3e0; color: #ff9800; }
        .stat-card.purple .icon { background: #f3e5f5; color: #9c27b0; }

        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-card .label {
            color: #7f8c8d;
            font-size: 14px;
        }

        .stat-card .change {
            font-size: 13px;
            margin-top: 8px;
            font-weight: 600;
        }

        .change.positive { color: #4caf50; }
        .change.negative { color: #f44336; }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #ecf0f1;
        }

        .card-header h3 {
            font-size: 18px;
        }

        .card-header a {
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            padding: 12px;
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
            color: #7f8c8d;
            text-transform: uppercase;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .alert i {
            font-size: 20px;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="page-header">
                <h1>Dashboard</h1>
                <p>Vue d'ensemble de votre boutique</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="value"><?php echo number_format($stats['total_orders']); ?></div>
                    <div class="label">Total Commandes</div>
                    <div class="change positive">
                        <i class="fas fa-arrow-up"></i> <?php echo $stats['today_orders']; ?> aujourd'hui
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="value"><?php echo number_format($stats['total_revenue'], 2); ?> DT</div>
                    <div class="label">Revenu Total</div>
                    <div class="change positive">
                        <i class="fas fa-arrow-up"></i> <?php echo number_format($stats['today_revenue'] ?? 0, 2); ?> DT aujourd'hui
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="value"><?php echo number_format($stats['total_users']); ?></div>
                    <div class="label">Utilisateurs</div>
                    <div class="change positive">
                        <i class="fas fa-arrow-up"></i> <?php echo $stats['new_users_today']; ?> nouveaux
                    </div>
                </div>

                <div class="stat-card purple">
                    <div class="icon"><i class="fas fa-box"></i></div>
                    <div class="value"><?php echo number_format($stats['total_products']); ?></div>
                    <div class="label">Produits</div>
                    <?php if ($stats['low_stock_products'] > 0): ?>
                        <div class="change negative">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $stats['low_stock_products']; ?> en rupture
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Commandes Récentes</h3>
                        <a href="orders.php">Voir tout</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                <tr>
                                    <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo number_format($order['total'], 2); ?> DT</td>
                                    <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Alertes Stock</h3>
                        <a href="products.php">Gérer</a>
                    </div>
                    <?php if (mysqli_num_rows($low_stock) > 0): ?>
                        <?php while ($product = mysqli_fetch_assoc($low_stock)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                                    <small>Stock: <?php echo $product['stock_quantity']; ?> / Min: <?php echo $product['min_stock_level']; ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #7f8c8d; text-align: center; padding: 20px;">
                            <i class="fas fa-check-circle" style="color: #4caf50; font-size: 24px;"></i><br>
                            Tous les produits sont en stock
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
