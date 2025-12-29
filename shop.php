<?php
include 'config/database.php';
include 'includes/functions.php';

// Récupérer la catégorie et le terme de recherche depuis l'URL
$category_slug = isset($_GET['category']) ? $_GET['category'] : '';
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Construire la requête SQL en fonction des filtres
$where_conditions = ["p.is_active = 1"];
$params = [];

if (!empty($category_slug)) {
    $category_slug = mysqli_real_escape_string($cnx, $category_slug);
    $where_conditions[] = "c.slug = '$category_slug'";
    $category_name_query = "SELECT name FROM categories WHERE slug = '$category_slug' LIMIT 1";
    $category_result = mysqli_query($cnx, $category_name_query);
    $category_data = mysqli_fetch_assoc($category_result);
    $category_name = $category_data ? $category_data['name'] : 'Collection';
} else {
    $category_name = "Tous les Produits";
}

if (!empty($search_term)) {
    $escaped_search = mysqli_real_escape_string($cnx, $search_term);
    $where_conditions[] = "(p.name LIKE '%$escaped_search%' OR p.description LIKE '%$escaped_search%' OR p.short_description LIKE '%$escaped_search%' OR p.sku LIKE '%$escaped_search%')";
    $category_name = !empty($category_name) ? "$category_name - Résultats pour \"$search_term\"" : "Résultats pour \"$search_term\"";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Compter le nombre total de produits pour la pagination
$count_query = "SELECT COUNT(p.product_id) as total 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                $where_clause";
$count_result = mysqli_query($cnx, $count_query);
$total_products = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_products / $limit);

// Récupérer les produits avec pagination
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            $where_clause 
            ORDER BY p.created_at DESC 
            LIMIT $limit OFFSET $offset";
$result = mysqli_query($cnx, $query);

// Récupérer toutes les catégories pour le filtre
$categories_query = "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order";
$categories_result = mysqli_query($cnx, $categories_query);

$i = 0;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Shop — Nuraya</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
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

    .featured-products {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 24px;
        display: flex;
        gap: 30px
    }

    /* Categories Filter */
    .categories-filter {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
        border: 1px solid rgba(200, 182, 166, 0.2);
        width: 280px;
        flex-shrink: 0
    }

    .categories-filter h3 {
        font-size: 20px;
        color: var(--text-dark);
        margin-bottom: 16px;
        font-weight: 600
    }

    .filter-list {
        display: flex;
        flex-direction: column;
        gap: 8px
    }

    .filter-item {
        background: var(--bg-light);
        color: var(--text-gray);
        padding: 12px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        display: block
    }

    .filter-item:hover {
        background: var(--beige-dark);
        color: var(--bg-white);
        transform: translateX(5px)
    }

    .filter-item.active {
        background: var(--beige-dark);
        color: var(--bg-white);
        border-color: var(--beige-dark)
    }

    .mobile-filter-toggle {
        display: none;
    }

    .products-section {
        flex: 1
    }

    .section-title {
        font-size: 32px;
        margin-bottom: 32px;
        color: var(--text-dark);
        font-weight: 700;
        text-align: center;
        position: relative;
        padding-bottom: 16px
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: var(--beige-dark)
    }

    .products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px
    }

    .product-card {
        background: var(--bg-white);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
        border: 1px solid rgba(200, 182, 166, 0.2)
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(200, 182, 166, 0.25)
    }

    .product-image {
        width: 100%;
        height: 320px;
        object-fit: cover;
        display: block;
        background: var(--bg-light)
    }

    .product-info {
        padding: 20px
    }

    .product-title {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 12px;
        font-size: 16px;
        line-height: 1.4
    }

    .product-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px
    }

    .current-price {
        color: var(--beige-dark);
        font-weight: 700;
        font-size: 18px
    }

    .compare-price {
        text-decoration: line-through;
        color: var(--text-gray);
        font-size: 14px;
        margin-right: 8px;
        font-weight: 400
    }

    .add-to-cart {
        background: var(--beige-dark);
        color: var(--bg-white);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s
    }

    .add-to-cart:hover {
        background: var(--text-dark);
        transform: scale(1.05)
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 40px;
        padding: 20px 0
    }

    .pagination a,
    .pagination span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1px solid rgba(200, 182, 166, 0.2)
    }

    .pagination a {
        background: var(--bg-white);
        color: var(--text-dark)
    }

    .pagination a:hover {
        background: var(--beige-dark);
        color: var(--bg-white);
        transform: translateY(-2px)
    }

    .pagination .current {
        background: var(--beige-dark);
        color: var(--bg-white);
        border-color: var(--beige-dark)
    }

    .pagination .dots {
        background: transparent;
        color: var(--text-gray);
        border: none
    }

    @media (max-width:992px) {
        .featured-products {
            flex-direction: column;
            gap: 30px
        }

        .categories-filter {
            width: 100%;
            padding: 20px;
        }

        .filter-list {
            flex-direction: row;
            overflow-x: auto;
            padding-bottom: 10px;
            gap: 12px;
            scrollbar-width: none;
            /* Firefox */
        }

        .filter-list::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Edge */
        }

        .filter-item {
            white-space: nowrap;
            flex-shrink: 0;
            padding: 10px 20px;
        }

        .products {
            grid-template-columns: repeat(3, 1fr);
            gap: 20px
        }
    }

    @media (max-width:768px) {
        .products {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px
        }

        .product-image {
            height: 250px;
        }

        .section-title {
            font-size: 24px;
        }
    }

    @media (max-width:480px) {
        .products {
            grid-template-columns: 1fr;
        }

        .product-image {
            height: 320px;
        }
    }
    </style>
</head>

<body>
    <header>
        <?php include 'templates/navbar_updated.php'; ?>
    </header>

    <section class="featured-products">
        <!-- Categories Filter -->
        <div class="categories-filter">
            <h3>Catégories</h3>
            <div class="filter-list">
                <a href="shop.php" class="filter-item <?php echo empty($category_slug) ? 'active' : ''; ?>">
                    Tous les Produits
                </a>
                <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                <a href="shop.php?category=<?php echo $cat['slug']; ?>"
                    class="filter-item <?php echo $category_slug === $cat['slug'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="products-section">
            <h2 class="section-title"><?php echo htmlspecialchars($category_name); ?></h2>
            <div class="products">
                <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($t = mysqli_fetch_assoc($result)):
                        $i++;
                        ?>
                <a href="product.php?id=<?php echo $t['product_id']; ?>" class="product-card">
                    <div class="product-image">
                        <img src="<?php echo get_image_url($t['image_url'], 'Produit'); ?>"
                            alt="<?php echo htmlspecialchars($t['name']); ?>" loading="lazy"
                            style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($t['name']); ?></h3>
                        <div class="product-price">
                            <div>
                                <?php if ($t['compare_price'] && $t['compare_price'] > $t['price']): ?>
                                <span class="compare-price"><?php echo number_format($t['compare_price'], 3); ?>
                                    DT</span>
                                <?php endif; ?>
                                <span class="current-price"><?php echo number_format($t['price'], 3); ?> DT</span>
                            </div>
                            <button class="add-to-cart" data-product-id="<?php echo $t['product_id']; ?>"
                                data-id="<?php echo $i; ?>" data-name="<?php echo $t['name']; ?>"
                                data-price="<?php echo $t['price']; ?>" data-image="<?php echo $t['image_url']; ?>">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </a>
                <?php endwhile; ?>
                <?php else: ?>
                <p style="text-align: center; color: var(--text-gray); grid-column: 1/-1; padding: 40px;">
                    Aucun produit trouvé dans cette catégorie.
                </p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a
                    href="?page=<?php echo $page - 1; ?><?php echo !empty($category_slug) ? '' : '&category=' . $category_slug; ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>

                <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);

                    for ($i = $start_page; $i <= $end_page; $i++):
                        if ($i == $page):
                            ?>
                <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                <a
                    href="?page=<?php echo $i; ?><?php echo !empty($category_slug) ? '' : '&category=' . $category_slug; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if ($end_page < $total_pages): ?>
                <span class="dots">...</span>
                <a
                    href="?page=<?php echo $total_pages; ?><?php echo !empty($category_slug) ? '' : '&category=' . $category_slug; ?>">
                    <?php echo $total_pages; ?>
                </a>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                <a
                    href="?page=<?php echo $page + 1; ?><?php echo !empty($category_slug) ? '' : '&category=' . $category_slug; ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
    // Fonction pour ajouter au panier
    function addToCart(productId, name, price, image) {
        // Envoyer au serveur pour sauvegarder en base de données
        fetch('api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Afficher le message de confirmation
                    showAddToCartMessage(name);
                    // Mettre à jour le compteur
                    updateCartCount();
                } else {
                    console.error('Erreur:', data.message);
                    showAddToCartMessage('Erreur: ' + data.message, true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Fallback: sauvegarder en localStorage si le serveur échoue
                savToLocalStorage(productId, name, price, image);
            });
    }

    // Fallback: sauvegarder en localStorage
    function savToLocalStorage(productId, name, price, image) {
        let cart = localStorage.getItem('cart');
        cart = cart ? JSON.parse(cart) : [];

        let existingItem = cart.find(item => item.productId == productId);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                productId: productId,
                name: name,
                price: parseFloat(price),
                image: image,
                quantity: 1
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
    }

    // Fonction pour afficher un message de confirmation
    function showAddToCartMessage(productName, isError = false) {
        const message = document.createElement('div');
        message.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${isError ? '#f44336' : '#4CAF50'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 9999;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
        `;
        message.textContent = (isError ? '✗ ' : '✓ ') + productName + (isError ? '' : ' ajouté au panier');
        document.body.appendChild(message);

        setTimeout(() => {
            message.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => message.remove(), 300);
        }, 3000);
    }

    // Fonction pour mettre à jour le compteur du panier dans la navbar
    function updateCartCount() {
        // Récupérer le count depuis le serveur
        fetch('api/cart.php?action=count')
            .then(response => response.json())
            .then(data => {
                    if (data.success) {
                    const cartBadge = document.querySelector('[data-cart-count]');
                    if (cartBadge) {
                        cartBadge.textContent = data.count;
                        cartBadge.style.visibility = data.count > 0 ? 'visible' : 'hidden';
                    }
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }

    // Ajouter les styles d'animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // Ajouter les écouteurs d'événements pour les boutons "Ajouter au panier"
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const productId = this.dataset.productId;
            const name = this.dataset.name;
            const price = this.dataset.price;
            const image = this.dataset.image;

            addToCart(productId, name, price, image);
        });
    });

    // Empêcher la navigation lors du clic sur les cartes produits
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.add-to-cart')) {
                window.location.href = this.href;
            }
        });
    });

    // Initialiser le compteur du panier au chargement
    document.addEventListener('DOMContentLoaded', updateCartCount);
    </script>
    <script src="assets/js/cart-count.js"></script>

    <?php mysqli_close($cnx); ?>
</body>

</html>