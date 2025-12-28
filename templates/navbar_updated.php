<?php
include 'config/database.php';

// Pas besoin de chemins dynamiques - tout est à la racine
$base_path = '';
$assets_path = 'assets/';
?>
<style>
:root {
    --bg-light: #F5EFE6;
    --bg-white: #FAF7F2;
    --beige-dark: #C8B6A6;
    --text-dark: #1C1C1C;
    --text-gray: #7A7A7A;
    --accent-pink: #E6B7C8;
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 28px;
    background: var(--bg-white);
    box-shadow: 0 2px 10px rgba(200, 182, 166, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.navbar .logo {
    font-weight: 700;
    font-size: 24px;
    color: var(--text-dark);
    text-decoration: none;
    letter-spacing: 1px;
}

.nav-links {
    display: flex;
    gap: 24px;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center
}

.nav-links a {
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 6px;
    transition: all 0.3s ease;
    position: relative
}

.nav-links a:hover,
.nav-links a.active {
    color: var(--beige-dark);
    background: rgba(200, 182, 166, 0.1)
}

/* Search Bar */
.search-container {
    position: relative;
    display: flex;
    align-items: center;
    background: var(--bg-light);
    border-radius: 25px;
    padding: 8px 16px;
    transition: all 0.3s ease;
    border: 1px solid transparent
}

.search-container:focus-within {
    border-color: var(--beige-dark);
    box-shadow: 0 0 0 3px rgba(200, 182, 166, 0.1)
}

.search-input {
    border: none;
    background: transparent;
    outline: none;
    width: 200px;
    font-size: 14px;
    color: var(--text-dark);
    transition: width 0.3s ease
}

.search-input:focus {
    width: 250px
}

.search-btn {
    background: none;
    border: none;
    color: var(--text-gray);
    cursor: pointer;
    padding: 4px;
    transition: color 0.3s ease
}

.search-btn:hover {
    color: var(--beige-dark)
}

/* Search Results Dropdown */
.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-white);
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(200, 182, 166, 0.2);
    margin-top: 8px;
    max-height: 400px;
    overflow-y: auto;
    display: none;
    z-index: 1000
}

.search-results.show {
    display: block
}

.search-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: var(--text-dark);
    border-bottom: 1px solid rgba(200, 182, 166, 0.1);
    transition: background 0.3s ease
}

.search-result-item:hover {
    background: var(--bg-light)
}

.search-result-item:last-child {
    border-bottom: none
}

.search-result-image {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 8px;
    background: var(--bg-light)
}

.search-result-info {
    flex: 1
}

.search-result-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px
}

.search-result-category {
    font-size: 12px;
    color: var(--text-gray)
}

.search-result-price {
    font-weight: 700;
    color: var(--beige-dark);
    font-size: 14px
}

.no-results {
    padding: 20px;
    text-align: center;
    color: var(--text-gray);
    font-size: 14px
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 16px
}

.cart-icon {
    position: relative;
    cursor: pointer
}

.cart-count {
    position: absolute;
    top: -6px;
    right: -8px;
    background: var(--accent-pink);
    color: var(--bg-white);
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700
}

.user-menu {
    position: relative
}

.user-btn {
    background: none;
    border: none;
    color: var(--text-dark);
    font-size: 20px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease
}

.user-btn:hover {
    background: var(--bg-light);
    color: var(--beige-dark)
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--bg-white);
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(200, 182, 166, 0.2);
    margin-top: 8px;
    min-width: 200px;
    display: none;
    z-index: 1000
}

.user-dropdown.show {
    display: block
}

.user-dropdown a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    text-decoration: none;
    color: var(--text-dark);
    border-bottom: 1px solid rgba(200, 182, 166, 0.1);
    transition: background 0.3s ease
}

.user-dropdown a:hover {
    background: var(--bg-light)
}

.user-dropdown a:last-child {
    border-bottom: none
}

.user-dropdown i {
    width: 16px;
    text-align: center;
    color: var(--beige-dark)
}

.hamburger-menu {
    display: none;
    color: var(--text-dark);
    font-size: 20px;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.3s ease
}

.hamburger-menu:hover {
    background: var(--bg-light);
    color: var(--beige-dark)
}

/* Mobile Menu */
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
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    overflow-y: auto
}

.mobile-menu.active {
    left: 0
}

.mobile-menu-header {
    padding: 20px;
    border-bottom: 1px solid rgba(200, 182, 166, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center
}

.close-menu {
    font-size: 24px;
    cursor: pointer;
    color: var(--text-dark);
    background: none;
    border: none;
    padding: 4px;
    border-radius: 4px;
    transition: background 0.3s ease
}

.close-menu:hover {
    background: var(--bg-light)
}

.mobile-menu-content {
    padding: 20px
}

.mobile-menu-content a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px solid rgba(200, 182, 166, 0.1);
    transition: color 0.3s ease
}

.mobile-menu-content a:hover {
    color: var(--beige-dark)
}

.mobile-menu-content a.active {
    color: var(--beige-dark);
    font-weight: 600
}

.mobile-menu-content a:last-child {
    border-bottom: none
}

.mobile-menu-content i {
    width: 16px;
    text-align: center;
    color: var(--beige-dark)
}

/* Mobile Search */
.mobile-search {
    padding: 16px;
    border-bottom: 1px solid rgba(200, 182, 166, 0.2)
}

.mobile-search-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid rgba(200, 182, 166, 0.3);
    border-radius: 25px;
    background: var(--bg-light);
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s ease
}

.mobile-search-input:focus {
    border-color: var(--beige-dark)
}

/* Categories Section */
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

/* Responsive */
.mobile-menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1999;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.mobile-menu-overlay.active {
    display: block;
    opacity: 1;
}

@media (max-width:768px) {
    .nav-links {
        display: none
    }

    .search-container {
        display: none
    }

    .hamburger-menu {
        display: block
    }

    .mobile-menu {
        display: block;
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.2);
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 2000;
        background: var(--bg-white);
    }

    .navbar {
        padding: 12px 20px
    }

    .navbar .logo {
        font-size: 20px
    }
}

@media (max-width:480px) {
    .mobile-menu {
        width: 100%;
        left: -100%
    }
}
</style>
<link rel="stylesheet" href="<?php echo $assets_path; ?>css/toast.css">
<script src="<?php echo $assets_path; ?>js/toast.js"></script>

<header>
    <nav class="navbar">
        <div class="nav-left">
            <div class="hamburger-menu" onclick="toggleMobileMenu()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </div>
        </div>

        <a class="logo" href="<?php echo $base_path; ?>index.php">NURAYA</a>
        <div class="nav-links">
            <a href="<?php echo $base_path; ?>index.php"
                class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/nurayapro/') !== false && strpos($_SERVER['REQUEST_URI'], '.php') === false) ? 'active' : ''; ?>">Accueil</a>
            <a href="<?php echo $base_path; ?>shop.php"
                class="<?php echo strpos($_SERVER['REQUEST_URI'], 'shop.php') !== false ? 'active' : ''; ?>">Boutique</a>
            <a href="<?php echo $base_path; ?>about_new.php"
                class="<?php echo strpos($_SERVER['REQUEST_URI'], 'about_new.php') !== false ? 'active' : ''; ?>">À
                propos</a>
            <a href="<?php echo $base_path; ?>contact_us.php"
                class="<?php echo strpos($_SERVER['REQUEST_URI'], 'contact_us.php') !== false ? 'active' : ''; ?>">Contact</a>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="Rechercher un produit...">
            <button class="search-btn" onclick="performSearch()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
            <div class="search-results" id="searchResults"></div>
        </div>

        <div class="nav-right">
            <div class="user-menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                <button class="user-btn" onclick="toggleUserMenu()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <a href="<?php echo $base_path; ?>account.php">
                        <i class="fas fa-user"></i>
                        Mon Compte
                    </a>
                    <a href="<?php echo $base_path; ?>wishlist.php">
                        <i class="fas fa-heart"></i>
                        Mes Favoris
                    </a>
                    <a href="<?php echo $base_path; ?>orders.php">
                        <i class="fas fa-shopping-bag"></i>
                        Mes Commandes
                    </a>
                    <a href="<?php echo $base_path; ?>api/auth.php?action=logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
                <?php else: ?>
                <a href="<?php echo $base_path; ?>login.php" class="user-btn" title="Connexion">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </a>
                <?php endif; ?>
            </div>

            <div class="cart-icon" onclick="window.location.href='<?php echo $base_path; ?>cart.php'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="10" cy="20" r="2"></circle>
                    <circle cx="20" cy="20" r="2"></circle>
                    <path d="M4 13h7a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H4l3-3z"></path>
                </svg>
                <div class="cart-count" id="cartCount">0</div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <span>Menu</span>
            <button class="close-menu" onclick="toggleMobileMenu()">×</button>
        </div>

        <!-- Mobile Search -->
        <div class="mobile-search">
            <input type="text" class="mobile-search-input" id="mobileSearchInput"
                placeholder="Rechercher un produit...">
            <div class="search-results" id="mobileSearchResults"></div>
        </div>

        <div class="mobile-menu-content">
            <a href="<?php echo $base_path; ?>index.php"
                class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/index.php') !== false || strpos($_SERVER['REQUEST_URI'], '/nurayapro/') !== false && strpos($_SERVER['REQUEST_URI'], '.php') === false) ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                Accueil
            </a>
            <a href="<?php echo $base_path; ?>shop.php"
                class="<?php echo strpos($_SERVER['REQUEST_URI'], 'produits') !== false ? 'active' : ''; ?>">
                <i class="fas fa-shopping-bag"></i>
                Boutique
            </a>
            <a href="<?php echo $base_path; ?>about_new.php"
                class="<?php echo strpos($_SERVER['REQUEST_URI'], 'about_new.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-info-circle"></i>
                À propos
            </a>
            <a href="<?php echo $base_path; ?>contact_us.php"
                class="<?php echo strpos($_SERVER['REQUEST_URI'], 'contact_us.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                Contact
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo $base_path; ?>account.php">
                <i class="fas fa-user"></i>
                Mon Compte
            </a>
            <a href="<?php echo $base_path; ?>wishlist.php">
                <i class="fas fa-heart"></i>
                Mes Favoris
            </a>
            <a href="<?php echo $base_path; ?>orders.php">
                <i class="fas fa-shopping-bag"></i>
                Mes Commandes
            </a>
            <a href="<?php echo $base_path; ?>api/auth.php?action=logout">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
            <?php else: ?>
            <a href="<?php echo $base_path; ?>login.php">
                <i class="fas fa-sign-in-alt"></i>
                Connexion
            </a>
            <a href="<?php echo $base_path; ?>register.php">
                <i class="fas fa-user-plus"></i>
                Inscription
            </a>
            <?php endif; ?>

            <!-- Categories Section -->
            <div class="categories-section">
                <div class="categories-title" onclick="toggleCategories()">
                    <span>Catégories</span>
                    <span class="categories-arrow" id="categoriesArrow">▼</span>
                </div>
                <div class="categories-list" id="categoriesList">
                    <a href="<?php echo $base_path; ?>shop.php?category=mode" class="category-item">
                        <span>></span> Mode
                    </a>
                    <a href="<?php echo $base_path; ?>shop.php?category=accessoires" class="category-item">
                        <span>></span> Accessoires
                    </a>
                    <a href="<?php echo $base_path; ?>shop.php?category=chaussures" class="category-item">
                        <span>></span> Chaussures
                    </a>
                    <a href="<?php echo $base_path; ?>shop.php?category=sacs" class="category-item">
                        <span>></span> Sacs
                    </a>
                    <a href="<?php echo $base_path; ?>shop.php?category=bijoux" class="category-item">
                        <span>></span> Bijoux
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay" onclick="toggleMobileMenu()"></div>
</header>

<script>
let searchTimeout;
let currentSearchTerm = '';
const basePath = '<?php echo $base_path; ?>';

// Mobile Menu Functions
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

// User Menu Functions
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    const userDropdown = document.getElementById('userDropdown');
    if (userDropdown && !e.target.closest('.user-menu')) {
        userDropdown.classList.remove('show');
    }

    const searchResults = document.getElementById('searchResults');
    if (searchResults && !e.target.closest('.search-container')) {
        searchResults.classList.remove('show');
    }
});

// Search Functions
function performSearch() {
    const searchTerm = document.getElementById('searchInput').value.trim();
    if (searchTerm) {
        window.location.href = `<?php echo $base_path; ?>shop.php?search=${encodeURIComponent(searchTerm)}`;
    }
}

function handleSearchInput() {
    const searchTerm = this.value.trim();

    if (searchTerm === currentSearchTerm) return;
    currentSearchTerm = searchTerm;

    clearTimeout(searchTimeout);

    if (searchTerm.length < 2) {
        document.getElementById('searchResults').classList.remove('show');
        return;
    }

    searchTimeout = setTimeout(() => {
        fetchSearchResults(searchTerm);
    }, 300);
}

function fetchSearchResults(searchTerm) {
    fetch(`${basePath}api/search.php?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => {
            console.error('Search error:', error);
        });
}

function displaySearchResults(data) {
    const resultsContainer = document.getElementById('searchResults');

    if (data.success && data.results.length > 0) {
        let html = '';
        data.results.forEach(product => {
            html += `
                <a href="${basePath}product.php?id=${product.product_id}" class="search-result-item">
                    <img src="${product.image_url}" alt="${product.name}" class="search-result-image"
                         onerror="this.src='https://via.placeholder.com/40x40/F5EFE6/C8B6A6?text=P'">
                    <div class="search-result-info">
                        <div class="search-result-name">${product.name}</div>
                        <div class="search-result-category">${product.category_name}</div>
                    </div>
                    <div class="search-result-price">${product.price} DT</div>
                </a>
            `;
        });
        resultsContainer.innerHTML = html;
        resultsContainer.classList.add('show');
    } else {
        resultsContainer.innerHTML = '<div class="no-results">Aucun produit trouvé</div>';
        resultsContainer.classList.add('show');
    }
}

function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    let overlay = document.getElementById('mobileMenuOverlay');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'mobileMenuOverlay';
        overlay.className = 'mobile-menu-overlay';
        overlay.onclick = toggleMobileMenu;
        document.body.appendChild(overlay);
    }

    menu.classList.toggle('active');
    overlay.classList.toggle('active');

    if (menu.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

const mobileSearchInput = document.getElementById('mobileSearchInput');
if (mobileSearchInput) {
    mobileSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        const resultsContainer = document.getElementById('mobileSearchResults');

        if (searchTerm.length < 2) {
            resultsContainer.classList.remove('show');
            return;
        }

        fetch(`${basePath}api/search.php?q=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.results.length > 0) {
                    let html = '';
                    data.results.forEach(product => {
                        html += `
                            <a href="${basePath}product.php?id=${product.product_id}" class="search-result-item">
                                <img src="${product.image_url}" alt="${product.name}" class="search-result-image"
                                     onerror="this.src='https://via.placeholder.com/40x40/F5EFE6/C8B6A6?text=P'">
                                <div class="search-result-info">
                                    <div class="search-result-name">${product.name}</div>
                                    <div class="search-result-category">${product.category_name}</div>
                                </div>
                                <div class="search-result-price">${product.price} DT</div>
                            </a>
                        `;
                    });
                    resultsContainer.innerHTML = html;
                    resultsContainer.classList.add('show');
                } else {
                    resultsContainer.innerHTML = '<div class="no-results">Aucun produit trouvé</div>';
                    resultsContainer.classList.add('show');
                }
            });
    });

    mobileSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
                window.location.href =
                    `${basePath}shop.php?search=${encodeURIComponent(searchTerm)}`;
            }
        }
    });
}

// Load cart count on page load
function loadCartCount() {
    fetch(`${basePath}api/cart.php?action=get`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cartCount').textContent = data.total_items || 0;
            }
        })
        .catch(error => {
            console.error('Cart load error:', error);
        });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadCartCount();
});
</script>
</header>