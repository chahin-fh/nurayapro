<?php
require_once 'includes/autoload.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer les informations de l'utilisateur
$user_query = "SELECT id, first_name, last_name, email FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($cnx, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Récupérer le nombre total de commandes
$count_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = " . $_SESSION['user_id'];
$count_result = mysqli_query($cnx, $count_query);
$total_orders = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_orders / $limit);

// Récupérer les commandes
$orders_query = "SELECT o.*, COUNT(oi.id) as item_count 
                 FROM orders o 
                 LEFT JOIN order_items oi ON o.id = oi.order_id 
                 WHERE o.user_id = " . $_SESSION['user_id'] . " 
                 GROUP BY o.id 
                 ORDER BY o.order_date DESC 
                 LIMIT $limit OFFSET $offset";
$orders_result = mysqli_query($cnx, $orders_query);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes — Nuraya</title>
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
            padding: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .account-container {
            display: flex;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            gap: 30px;
        }

        .account-sidebar {
            width: 280px;
            background: var(--bg-white);
            border-radius: 12px;
            padding: 24px;
            align-self: flex-start;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.1);
        }

        .user-profile-summary {
            text-align: center;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(200, 182, 166, 0.2);
            margin-bottom: 24px;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: var(--beige-dark);
            color: var(--bg-white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            margin: 0 auto 16px;
        }

        .user-name {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 4px;
        }

        .user-email {
            color: var(--text-gray);
            font-size: 14px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--bg-light);
            color: var(--beige-dark);
        }

        .sidebar-menu a.active {
            font-weight: 600;
        }

        .account-content {
            flex: 1;
            background: var(--bg-white);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.1);
        }

        .content-header {
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(200, 182, 166, 0.2);
            padding-bottom: 20px;
        }

        .content-header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .order-item {
            background: var(--bg-white);
            border: 1px solid rgba(200, 182, 166, 0.3);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-item:hover {
            border-color: var(--beige-dark);
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
        }

        .order-info {
            flex: 1;
        }

        .order-number {
            font-weight: 700;
            font-size: 18px;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .order-meta {
            color: var(--text-gray);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .order-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed,
        .status-processing {
            background: #cce5ff;
            color: #004085;
        }

        .status-shipped {
            background: #d4edda;
            color: #155724;
        }

        .status-delivered {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .order-action {
            margin-left: 20px;
        }

        .btn-view {
            padding: 8px 16px;
            border: 1px solid var(--beige-dark);
            border-radius: 6px;
            color: var(--beige-dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background: var(--beige-dark);
            color: var(--bg-white);
        }

        .no-orders {
            text-align: center;
            padding: 40px;
            color: var(--text-gray);
        }

        .no-orders i {
            font-size: 48px;
            color: var(--beige-dark);
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .page-link {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--bg-light);
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .page-link.active,
        .page-link:hover {
            background: var(--beige-dark);
            color: var(--bg-white);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .account-container {
                flex-direction: column;
            }

            .account-sidebar {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .sidebar-menu {
                display: flex;
                gap: 10px;
                overflow-x: auto;
                width: 100%;
                padding-bottom: 5px;
            }

            .sidebar-menu li {
                margin-bottom: 0;
                flex-shrink: 0;
            }
        }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="account-container">
        <!-- Sidebar Identical to account.php -->
        <div class="account-sidebar">
            <div class="user-profile-summary">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                </div>
                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>

            <ul class="sidebar-menu">
                <li><a href="account.php"><i class="fas fa-user"></i> Mon Profil</a></li>
                <li><a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Mes Commandes</a></li>
                <li><a href="wishlist.php"><i class="fas fa-heart"></i> Mes Favoris</a></li>
                <li><a href="addresses.php"><i class="fas fa-map-marker-alt"></i> Adresses</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                <li><a href="api/auth.php?action=logout"><i class="fas fa-sign-out-alt"></i>
                        Déconnexion</a></li>
            </ul>
        </div>

        <div class="account-content">
            <div class="content-header">
                <h1>Mes Commandes</h1>
                <p>Historique de vos commandes passées</p>
            </div>

            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <div class="order-item">
                        <div class="order-info">
                            <div class="order-number">#<?php echo htmlspecialchars($order['order_number']); ?></div>
                            <div class="order-meta">
                                <?php echo date('d/m/Y', strtotime($order['order_date'])); ?> •
                                <?php echo $order['item_count']; ?> article(s) •
                                <strong><?php echo number_format($order['total'] ?? 0, 2); ?> DT</strong>
                            </div>
                            <span class="order-status status-<?php echo $order['status']; ?>">
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
                        <div class="order-action">
                            <!-- TODO: Create single order view page -->
                            <a href="#" class="btn-view">Détails</a>
                        </div>
                    </div>
                <?php endwhile; ?>

                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <p>Vous n'avez pas encore passé de commande.</p>
                    <p style="margin-top: 15px;">
                        <a href="shop.php" style="color: var(--beige-dark); font-weight: 600; text-decoration: none;">
                            Découvrir nos produits →
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>