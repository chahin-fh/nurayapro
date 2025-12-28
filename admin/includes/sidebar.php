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
