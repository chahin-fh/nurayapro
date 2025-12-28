<?php
// Navbar partial: matches about.html theme
?>
<style>
:root {
    --primary: #000;
    --muted: #6b7280;
    --accent: #ff6b6b;
    --bg: #f9f9f9
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 28px;
    background: transparent
}

.navbar .logo {
    font-weight: 700;
    font-size: 20px;
    color: var(--primary);
    text-decoration: none
}

.nav-links {
    display: flex;
    gap: 18px;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center
}

.nav-links a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    padding: 8px 10px;
    border-radius: 6px
}

.nav-links a:hover {
    color: var(--accent)
}

.icons {
    display: flex;
    gap: 12px;
    align-items: center
}

.cart-icon {
    position: relative
}

.cart-count {
    position: absolute;
    top: -6px;
    right: -8px;
    background: var(--accent);
    color: #fff;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px
}

.user-icon {
    color: var(--primary);
    font-size: 20px;
    cursor: pointer;
    transition: color 0.3s
}

.user-icon:hover {
    color: var(--accent)
}

.hamburger-menu,
.search-icon {
    color: var(--primary);
    font-size: 20px;
    cursor: pointer;
    transition: color 0.3s
}

.hamburger-menu:hover,
.search-icon:hover {
    color: var(--accent)
}

.nav-left {
    display: flex;
    align-items: center;
    gap: 15px
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 15px
}

.mobile-menu {
    display: none;
    position: fixed;
    top: 0;
    left: -100%;
    width: 280px;
    height: 100vh;
    background: var(--bg-white);
    z-index: 1000;
    transition: left 0.3s ease;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1)
}

.mobile-menu.active {
    left: 0
}

.mobile-menu-header {
    padding: 20px;
    border-bottom: 1px solid var(--beige-dark);
    display: flex;
    justify-content: space-between;
    align-items: center
}

.close-menu {
    font-size: 24px;
    cursor: pointer;
    color: var(--text-dark)
}

.mobile-menu-content {
    padding: 20px
}

.mobile-menu-content a {
    display: block;
    padding: 12px 0;
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px solid rgba(200, 182, 166, 0.2)
}

.mobile-menu-content a:hover {
    color: var(--beige-dark)
}

/* Categories Styles */
.categories-section {
    margin-top: 20px;
    border-top: 1px solid rgba(200, 182, 166, 0.2);
    padding-top: 20px
}

.categories-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    font-weight: 600;
    color: var(--text-dark);
    cursor: pointer;
    transition: color 0.3s;
    border-bottom: 1px solid rgba(200, 182, 166, 0.2)
}

.categories-title:hover {
    color: var(--beige-dark)
}

.categories-arrow {
    font-size: 12px;
    transition: transform 0.3s
}

.categories-arrow.rotated {
    transform: rotate(180deg)
}

.categories-list {
    display: none;
    padding-left: 10px;
    margin-top: 10px
}

.categories-list.show {
    display: block
}

.category-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    color: var(--text-gray);
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    transition: color 0.3s;
    border-bottom: 1px solid rgba(200, 182, 166, 0.1)
}

.category-item:hover {
    color: var(--beige-dark);
    transform: translateX(5px)
}

.category-item span {
    color: var(--beige-dark);
    font-weight: 600
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999
}

.overlay.active {
    display: block
}

@media (max-width:768px) {
    .nav-links {
        display: none
    }

    .mobile-menu {
        display: block
    }
}
</style>

<header>
    <nav class="navbar">
        <div class="nav-left">
            <div class="hamburger-menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </div>
            <div class="search-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
        </div>

        <a class="logo" href="/nuraya_pro/home">NURAYA</a>

        <div class="nav-right">
            <a href="/nuraya_pro/cree_compte/" class="user-icon" title="Connexion / Créer un compte">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
            <div class="cart-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="10" cy="20" r="2"></circle>
                    <circle cx="20" cy="20" r="2"></circle>
                    <path d="M4 13h7a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H4l3-3z"></path>
                </svg>
                <div class="cart-count">0</div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <span>Menu</span>
            <span class="close-menu" onclick="toggleMobileMenu()">×</span>
        </div>
        <div class="mobile-menu-content">
            <a href="/nuraya_pro/home">Accueil</a>
            <a href="/nuraya_pro/shop">Shop</a>
            <a href="/nuraya_pro/about">About</a>
            <a href="/nuraya_pro/contact">Contact</a>

            <!-- Categories Section -->
            <div class="categories-section">
                <div class="categories-title" onclick="toggleCategories()">
                    <span>Catégories</span>
                    <span class="categories-arrow" id="categoriesArrow">▼</span>
                </div>
                <div class="categories-list" id="categoriesList">
                    <a href="/nuraya_pro/produits/index.php?category=mode" class="category-item">
                        <span>></span> Mode
                    </a>
                    <a href="/nuraya_pro/produits/index.php?category=accessoires" class="category-item">
                        <span>></span> Accessoires
                    </a>
                    <a href="/nuraya_pro/produits/index.php?category=chaussures" class="category-item">
                        <span>></span> Chaussures
                    </a>
                    <a href="/nuraya_pro/produits/index.php?category=sacs" class="category-item">
                        <span>></span> Sacs
                    </a>
                    <a href="/nuraya_pro/produits/index.php?category=bijoux" class="category-item">
                        <span>></span> Bijoux
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay" id="overlay" onclick="toggleMobileMenu()"></div>

    <script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('overlay');
        menu.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    function toggleCategories() {
        const categoriesList = document.getElementById('categoriesList');
        const categoriesArrow = document.getElementById('categoriesArrow');

        if (categoriesList.classList.contains('show')) {
            categoriesList.classList.remove('show');
            categoriesArrow.classList.remove('rotated');
            categoriesArrow.textContent = '▼';
        } else {
            categoriesList.classList.add('show');
            categoriesArrow.classList.add('rotated');
            categoriesArrow.textContent = '▲';
        }
    }

    document.querySelector('.hamburger-menu').addEventListener('click', toggleMobileMenu);
    </script>
</header>