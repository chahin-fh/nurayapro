<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-crown"></i> Admin Panel</h2>
        <p class="admin-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
    </div>
    
    <nav class="sidebar-nav">
        <a href="index.php" class="<?php echo $current_page === 'index' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="products.php" class="<?php echo $current_page === 'products' ? 'active' : ''; ?>">
            <i class="fas fa-box"></i> Produits
        </a>
        <a href="orders.php" class="<?php echo $current_page === 'orders' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Commandes
        </a>
        <a href="users.php" class="<?php echo $current_page === 'users' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Utilisateurs
        </a>
        <a href="categories.php" class="<?php echo $current_page === 'categories' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i> Catégories
        </a>
        <a href="../index.php" target="_blank">
            <i class="fas fa-external-link-alt"></i> Voir le site
        </a>
        <a href="../src/Controllers/api/auth.php?action=logout" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </nav>
</aside>

<style>
.admin-sidebar {
    width: 260px;
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    color: white;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 30px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-align: center;
}

.sidebar-header h2 {
    margin: 0 0 10px 0;
    font-size: 22px;
    font-weight: 700;
}

.admin-name {
    font-size: 13px;
    color: #bdc3c7;
    margin: 0;
}

.sidebar-nav {
    padding: 20px 0;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    color: #ecf0f1;
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.sidebar-nav a:hover {
    background: rgba(255,255,255,0.1);
    border-left-color: #3498db;
}

.sidebar-nav a.active {
    background: rgba(52, 152, 219, 0.2);
    border-left-color: #3498db;
    font-weight: 600;
}

.sidebar-nav a i {
    width: 20px;
    text-align: center;
}

.logout-link {
    margin-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 20px !important;
    color: #e74c3c !important;
}

.logout-link:hover {
    background: rgba(231, 76, 60, 0.1) !important;
    border-left-color: #e74c3c !important;
}
</style>
