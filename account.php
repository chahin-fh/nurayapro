<?php
require_once 'includes/autoload.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer les informations de l'utilisateur
$user_query = "SELECT id, first_name, last_name, email, phone, created_at, last_login FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($cnx, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Récupérer les commandes de l'utilisateur
$orders_query = "SELECT o.*, COUNT(oi.id) as item_count 
                 FROM orders o 
                 LEFT JOIN order_items oi ON o.id = oi.order_id 
                 WHERE o.user_id = " . $_SESSION['user_id'] . " 
                 GROUP BY o.id 
                 ORDER BY o.order_date DESC 
                 LIMIT 5";
$orders_result = mysqli_query($cnx, $orders_query);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte — Nuraya</title>
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

    .account-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 24px;
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 30px
    }

    .account-sidebar {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
        height: fit-content
    }

    .sidebar-header {
        text-align: center;
        margin-bottom: 30px
    }

    .user-avatar {
        width: 80px;
        height: 80px;
        background: var(--beige-dark);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        color: var(--bg-white);
        font-size: 32px;
        font-weight: 700
    }

    .user-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 4px
    }

    .user-email {
        color: var(--text-gray);
        font-size: 14px
    }

    .sidebar-menu {
        list-style: none
    }

    .sidebar-menu li {
        margin-bottom: 8px
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: var(--text-dark);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: var(--bg-light);
        color: var(--beige-dark)
    }

    .sidebar-menu i {
        width: 16px;
        text-align: center
    }

    .account-content {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
    }

    .content-header {
        margin-bottom: 30px
    }

    .content-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px
    }

    .content-header p {
        color: var(--text-gray);
        font-size: 14px
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px
    }

    .info-card {
        background: var(--bg-light);
        padding: 20px;
        border-radius: 12px
    }

    .info-card h3 {
        font-size: 14px;
        color: var(--text-gray);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px
    }

    .info-card p {
        font-size: 16px;
        color: var(--text-dark);
        font-weight: 600
    }

    .orders-section {
        margin-top: 40px
    }

    .orders-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px
    }

    .orders-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark)
    }

    .view-all-btn {
        color: var(--beige-dark);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px
    }

    .view-all-btn:hover {
        text-decoration: underline
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        border-bottom: 1px solid rgba(200, 182, 166, 0.1);
        transition: background 0.3s ease
    }

    .order-item:hover {
        background: var(--bg-light)
    }

    .order-item:last-child {
        border-bottom: none
    }

    .order-info {
        flex: 1
    }

    .order-number {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 4px
    }

    .order-date {
        font-size: 14px;
        color: var(--text-gray)
    }

    .order-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase
    }

    .status-pending {
        background: #fff3cd;
        color: #856404
    }

    .status-confirmed {
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

    .status-cancelled {
        background: #f8d7da;
        color: #721c24
    }

    .no-orders {
        text-align: center;
        padding: 40px;
        color: var(--text-gray)
    }

    .no-orders i {
        font-size: 48px;
        color: var(--beige-dark);
        margin-bottom: 16px
    }

    @media (max-width:768px) {
        .account-container {
            grid-template-columns: 1fr;
            gap: 20px
        }

        .account-sidebar {
            order: 2
        }

        .account-content {
            order: 1
        }

        .info-grid {
            grid-template-columns: 1fr
        }
    }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="account-container">
        <div class="account-sidebar">
            <div class="sidebar-header">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                </div>
                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="account.php" class="active">
                        <i class="fas fa-user"></i>
                        Mon Profil
                    </a>
                </li>
                <li>
                    <a href="orders.php">
                        <i class="fas fa-shopping-bag"></i>
                        Mes Commandes
                    </a>
                </li>
                <li>
                    <a href="wishlist.php">
                        <i class="fas fa-heart"></i>
                        Mes Favoris
                    </a>
                </li>
                <li>
                    <a href="addresses.php">
                        <i class="fas fa-map-marker-alt"></i>
                        Adresses
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        Paramètres
                    </a>
                </li>
                <li>
                    <a href="api/auth.php?action=logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </li>
            </ul>
        </div>

        <div class="account-content">
            <div class="content-header">
                <h1>Bonjour, <?php echo htmlspecialchars($user['first_name']); ?> !</h1>
                <p>Gérez votre profil et suivez vos commandes</p>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3>Date d'inscription</h3>
                    <p><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                </div>
                <div class="info-card">
                    <h3>Dernière connexion</h3>
                    <p><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Première connexion'; ?>
                    </p>
                </div>
                <div class="info-card">
                    <h3>Total des commandes</h3>
                    <p><?php echo mysqli_num_rows($orders_result); ?> commandes</p>
                </div>
            </div>

            <div class="orders-section">
                <div class="orders-header">
                    <h2>Commandes récentes</h2>
                    <a href="orders.php" class="view-all-btn">Voir tout</a>
                </div>

                <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                <div class="order-item">
                    <div class="order-info">
                        <div class="order-number">#<?php echo htmlspecialchars($order['order_number']); ?></div>
                        <div class="order-date"><?php echo date('d/m/Y', strtotime($order['order_date'])); ?> •
                            <?php echo $order['item_count']; ?> article(s)
                        </div>
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
                <?php endwhile; ?>
                <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <p>Vous n'avez pas encore de commandes</p>
                    <p style="margin-top: 8px; font-size: 14px;">
                        <a href="produits/index.php" style="color: var(--beige-dark); text-decoration: none;">
                            Commencer vos achats →
                        </a>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>