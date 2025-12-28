<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<button class="admin-nav-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i> Menu
</button>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2><i class="fas fa-crown"></i> Admin</h2>
            <button class="btn-icon" onclick="toggleSidebar()" style="background: none; color: var(--bg-white); font-size: 20px; display: none;" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
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

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Show close button on mobile
if (window.innerWidth <= 992) {
    document.getElementById('closeSidebar').style.display = 'block';
}

window.addEventListener('resize', () => {
    if (window.innerWidth <= 992) {
        document.getElementById('closeSidebar').style.display = 'block';
    } else {
        document.getElementById('closeSidebar').style.display = 'none';
        document.getElementById('adminSidebar').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }
});
</script>
