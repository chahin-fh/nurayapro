<?php
include '../cnx.php';
session_start();

// Récupérer l'ID du produit
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: index.php');
    exit;
}

// Récupérer les détails du produit
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = $product_id AND p.is_active = 1";
$result = mysqli_query($cnx, $query);

if (mysqli_num_rows($result) === 0) {
    header('Location: index.php');
    exit;
}

$product = mysqli_fetch_assoc($result);

// Incrémenter le compteur de vues
$update_query = "UPDATE products SET view_count = view_count + 1 WHERE product_id = $product_id";
mysqli_query($cnx, $update_query);

// Récupérer les produits similaires (même catégorie)
$similar_query = "SELECT p.*, c.slug as category_slug 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  WHERE p.category_id = {$product['category_id']} AND p.product_id != $product_id AND p.is_active = 1 
                  ORDER BY RAND() 
                  LIMIT 4";
$similar_result = mysqli_query($cnx, $similar_query);

// Récupérer les avis du produit
$reviews_query = "SELECT r.*, u.first_name, u.last_name 
                  FROM reviews r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.product_id = $product_id AND r.is_approved = 1 
                  ORDER BY r.created_at DESC";
$reviews_result = mysqli_query($cnx, $reviews_query);

// Calculer la moyenne des avis
$avg_rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                     FROM reviews 
                     WHERE product_id = $product_id AND is_approved = 1";
$avg_result = mysqli_query($cnx, $avg_rating_query);
$rating_data = mysqli_fetch_assoc($avg_result);
$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
$total_reviews = $rating_data['total_reviews'] ?? 0;

// Vérifier si l'utilisateur a déjà mis un avis
$user_review = null;
if (isset($_SESSION['user_id'])) {
    $user_review_query = "SELECT * FROM reviews WHERE product_id = $product_id AND user_id = {$_SESSION['user_id']}";
    $user_review_result = mysqli_query($cnx, $user_review_query);
    if (mysqli_num_rows($user_review_result) > 0) {
        $user_review = mysqli_fetch_assoc($user_review_result);
    }
}

// Vérifier si le produit est dans les favoris
$is_in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $wishlist_query = "SELECT id FROM wishlist WHERE product_id = $product_id AND user_id = {$_SESSION['user_id']}";
    $wishlist_result = mysqli_query($cnx, $wishlist_query);
    $is_in_wishlist = mysqli_num_rows($wishlist_result) > 0;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title><?php echo htmlspecialchars($product['name']); ?> — Nuraya</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='description'
        content="<?php echo htmlspecialchars($product['short_description'] ?? $product['meta_description'] ?? substr(strip_tags($product['description']), 0, 160)); ?>">
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
            color: var(--text-dark);
            line-height: 1.6
        }

        .product-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: start
        }

        .product-images {
            position: sticky;
            top: 20px
        }

        .main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 16px;
            background: var(--bg-white);
            box-shadow: 0 8px 25px rgba(200, 182, 166, 0.15)
        }

        .product-info {
            background: var(--bg-white);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(200, 182, 166, 0.15)
        }

        .product-category {
            color: var(--text-gray);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px
        }

        .product-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 16px;
            line-height: 1.2
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px
        }

        .stars {
            display: flex;
            gap: 4px
        }

        .star {
            color: #ddd;
            font-size: 16px
        }

        .star.filled {
            color: #ffc107
        }

        .rating-text {
            color: var(--text-gray);
            font-size: 14px
        }

        .product-price {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px
        }

        .current-price {
            font-size: 36px;
            font-weight: 700;
            color: var(--beige-dark)
        }

        .compare-price {
            font-size: 20px;
            color: var(--text-gray);
            text-decoration: line-through
        }

        .product-description {
            color: var(--text-gray);
            margin-bottom: 32px;
            line-height: 1.8
        }

        .product-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 32px;
            padding: 24px;
            background: var(--bg-light);
            border-radius: 12px
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px
        }

        .meta-label {
            font-size: 12px;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px
        }

        .meta-value {
            font-weight: 600;
            color: var(--text-dark)
        }

        .product-actions {
            display: flex;
            gap: 16px;
            margin-bottom: 32px
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            border: 1px solid rgba(200, 182, 166, 0.3);
            border-radius: 8px;
            overflow: hidden
        }

        .quantity-btn {
            background: var(--bg-light);
            border: none;
            width: 40px;
            height: 48px;
            cursor: pointer;
            font-size: 18px;
            color: var(--text-dark);
            transition: background 0.3s
        }

        .quantity-btn:hover {
            background: var(--beige-dark);
            color: var(--bg-white)
        }

        .quantity-input {
            width: 60px;
            height: 48px;
            border: none;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            background: var(--bg-white)
        }

        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px
        }

        .btn-primary {
            background: var(--beige-dark);
            color: var(--bg-white);
            flex: 1
        }

        .btn-primary:hover {
            background: var(--text-dark);
            transform: translateY(-2px)
        }

        .btn-secondary {
            background: var(--bg-white);
            color: var(--text-dark);
            border: 1px solid rgba(200, 182, 166, 0.3)
        }

        .btn-secondary:hover {
            background: var(--bg-light);
            transform: translateY(-2px)
        }

        .btn-secondary.active {
            background: var(--accent-pink);
            color: var(--bg-white);
            border-color: var(--accent-pink)
        }

        .tabs {
            margin-top: 60px
        }

        .tab-nav {
            display: flex;
            gap: 0;
            border-bottom: 2px solid rgba(200, 182, 166, 0.2);
            margin-bottom: 32px
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 16px 24px;
            font-weight: 600;
            color: var(--text-gray);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s
        }

        .tab-btn:hover {
            color: var(--text-dark)
        }

        .tab-btn.active {
            color: var(--beige-dark);
            border-bottom-color: var(--beige-dark)
        }

        .tab-content {
            display: none
        }

        .tab-content.active {
            display: block
        }

        .description-content {
            background: var(--bg-white);
            padding: 32px;
            border-radius: 16px;
            line-height: 1.8
        }

        .reviews-section {
            background: var(--bg-white);
            padding: 32px;
            border-radius: 16px
        }

        .review-form {
            background: var(--bg-light);
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 32px
        }

        .form-group {
            margin-bottom: 16px
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark)
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(200, 182, 166, 0.3);
            border-radius: 8px;
            font-size: 14px;
            background: var(--bg-white);
            transition: border-color 0.3s
        }

        .form-control:focus {
            outline: none;
            border-color: var(--beige-dark)
        }

        .rating-input {
            display: flex;
            gap: 8px;
            font-size: 24px
        }

        .rating-input .star {
            cursor: pointer;
            color: #ddd;
            transition: color 0.3s
        }

        .rating-input .star:hover,
        .rating-input .star.selected {
            color: #ffc107
        }

        .review-item {
            padding: 24px;
            border-bottom: 1px solid rgba(200, 182, 166, 0.2)
        }

        .review-item:last-child {
            border-bottom: none
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px
        }

        .review-author {
            font-weight: 600;
            color: var(--text-dark)
        }

        .review-date {
            color: var(--text-gray);
            font-size: 14px
        }

        .review-rating {
            display: flex;
            gap: 4px;
            margin-bottom: 8px
        }

        .review-text {
            color: var(--text-gray);
            line-height: 1.6
        }

        .similar-products {
            margin-top: 80px
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            color: var(--text-dark)
        }

        .similar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 24px
        }

        .similar-card {
            background: var(--bg-white);
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            color: var(--text-dark);
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
        }

        .similar-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(200, 182, 166, 0.25)
        }

        .similar-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: var(--bg-light)
        }

        .similar-info {
            padding: 16px
        }

        .similar-title {
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.4
        }

        .similar-price {
            color: var(--beige-dark);
            font-weight: 700;
            font-size: 18px
        }

        @media (max-width:768px) {
            .product-container {
                grid-template-columns: 1fr;
                gap: 40px
            }

            .product-images {
                position: static
            }

            .main-image {
                height: 400px
            }

            .product-info {
                padding: 24px
            }

            .product-title {
                font-size: 24px
            }

            .current-price {
                font-size: 28px
            }

            .product-actions {
                flex-direction: column
            }

            .btn-primary {
                width: 100%
            }

            .similar-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr))
            }
        }
    </style>
</head>

<body>
    <header>
        <?php include('../navbar.php'); ?>
    </header>

    <div class="product-container">
        <div class="product-images">
            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"
                class="main-image" onerror="this.src='https://via.placeholder.com/600x500/F5EFE6/C8B6A6?text=Produit'">
        </div>

        <div class="product-info">
            <div class="product-category">
                <a href="index.php?category=<?php echo $product['category_slug']; ?>"
                    style="color: inherit; text-decoration: none;">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </a>
            </div>

            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

            <div class="product-rating">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?php echo $i <= $avg_rating ? 'filled' : ''; ?>">★</span>
                    <?php endfor; ?>
                </div>
                <span class="rating-text"><?php echo $avg_rating; ?>/5 (<?php echo $total_reviews; ?> avis)</span>
            </div>

            <div class="product-price">
                <span class="current-price"><?php echo number_format($product['price'], 3); ?> DT</span>
                <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                    <span class="compare-price"><?php echo number_format($product['compare_price'], 3); ?> DT</span>
                <?php endif; ?>
            </div>

            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['short_description'] ?? $product['description'])); ?>
            </div>

            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label">Stock</span>
                    <span
                        class="meta-value"><?php echo $product['stock_quantity'] > 0 ? 'Disponible' : 'Rupture'; ?></span>
                </div>
                <?php if ($product['sku']): ?>
                    <div class="meta-item">
                        <span class="meta-label">Référence</span>
                        <span class="meta-value"><?php echo htmlspecialchars($product['sku']); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($product['weight']): ?>
                    <div class="meta-item">
                        <span class="meta-label">Poids</span>
                        <span class="meta-value"><?php echo $product['weight']; ?> kg</span>
                    </div>
                <?php endif; ?>
                <?php if ($product['dimensions']): ?>
                    <div class="meta-item">
                        <span class="meta-label">Dimensions</span>
                        <span class="meta-value"><?php echo htmlspecialchars($product['dimensions']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-actions">
                <div class="quantity-selector">
                    <button class="quantity-btn" onclick="updateQuantity(-1)">−</button>
                    <input type="number" id="quantity" class="quantity-input" value="1" min="1"
                        max="<?php echo $product['stock_quantity']; ?>">
                    <button class="quantity-btn" onclick="updateQuantity(1)">+</button>
                </div>
                <button class="btn btn-primary" onclick="addToCart()">
                    <i class="fas fa-shopping-bag"></i>
                    Ajouter au panier
                </button>
                <button class="btn btn-secondary <?php echo $is_in_wishlist ? 'active' : ''; ?>"
                    onclick="toggleWishlist()">
                    <i class="fas fa-heart"></i>
                    <?php echo $is_in_wishlist ? 'Dans les favoris' : 'Favoris'; ?>
                </button>
            </div>
        </div>
    </div>

    <div class="tabs">
        <div class="tab-nav">
            <button class="tab-btn active" onclick="showTab('description')">Description</button>
            <button class="tab-btn" onclick="showTab('reviews')">Avis (<?php echo $total_reviews; ?>)</button>
        </div>

        <div id="description" class="tab-content active">
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
        </div>

        <div id="reviews" class="tab-content">
            <div class="reviews-section">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (!$user_review): ?>
                        <div class="review-form">
                            <h3 style="margin-bottom: 20px;">Laisser un avis</h3>
                            <form id="reviewForm">
                                <div class="form-group">
                                    <label class="form-label">Note</label>
                                    <div class="rating-input" id="ratingInput">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star" data-rating="<?php echo $i; ?>"
                                                onclick="setRating(<?php echo $i; ?>)">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Titre (optionnel)</label>
                                    <input type="text" name="title" class="form-control" placeholder="Résumez votre expérience">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Votre avis</label>
                                    <textarea name="comment" class="form-control" rows="4" required
                                        placeholder="Partagez votre expérience avec ce produit..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    Envoyer l'avis
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="review-form">
                            <p style="color: var(--text-gray); text-align: center;">
                                <i class="fas fa-check-circle" style="color: var(--beige-dark); margin-right: 8px;"></i>
                                Vous avez déjà laissé un avis pour ce produit
                            </p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="review-form">
                        <p style="color: var(--text-gray); text-align: center;">
                            <a href="../login.php"
                                style="color: var(--beige-dark); text-decoration: none;">Connectez-vous</a>
                            pour laisser un avis
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (mysqli_num_rows($reviews_result) > 0): ?>
                    <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <span
                                    class="review-author"><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></span>
                                <span class="review-date"><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <?php if ($review['title']): ?>
                                <h4 style="margin-bottom: 8px; font-weight: 600;"><?php echo htmlspecialchars($review['title']); ?>
                                </h4>
                            <?php endif; ?>
                            <p class="review-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-gray); padding: 40px;">
                        Soyez le premier à donner votre avis sur ce produit !
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (mysqli_num_rows($similar_result) > 0): ?>
        <div class="similar-products">
            <h2 class="section-title">Produits Similaires</h2>
            <div class="similar-grid">
                <?php while ($similar = mysqli_fetch_assoc($similar_result)): ?>
                    <a href="product.php?id=<?php echo $similar['product_id']; ?>" class="similar-card">
                        <img src="<?php echo $similar['image_url']; ?>" alt="<?php echo htmlspecialchars($similar['name']); ?>"
                            class="similar-image"
                            onerror="this.src='https://via.placeholder.com/250x200/F5EFE6/C8B6A6?text=Produit'">
                        <div class="similar-info">
                            <h3 class="similar-title"><?php echo htmlspecialchars($similar['name']); ?></h3>
                            <div class="similar-price"><?php echo number_format($similar['price'], 3); ?> DT</div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <script>
        let selectedRating = 0;
        let productId = <?php echo $product_id; ?>;
        let isInWishlist = <?php echo $is_in_wishlist ? 'true' : 'false'; ?>;

        function updateQuantity(change) {
            const input = document.getElementById('quantity');
            const newValue = parseInt(input.value) + change;
            const max = parseInt(input.max);

            if (newValue >= 1 && newValue <= max) {
                input.value = newValue;
            }
        }

        function showTab(tabName) {
            // Masquer tous les contenus
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Désactiver tous les boutons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Afficher le contenu sélectionné
            document.getElementById(tabName).classList.add('active');

            // Activer le bouton sélectionné
            event.target.classList.add('active');
        }

        function setRating(rating) {
            selectedRating = rating;
            const stars = document.querySelectorAll('#ratingInput .star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        }

        function addToCart() {
            const quantity = document.getElementById('quantity').value;

            fetch('../api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&product_id=${productId}&quantity=${quantity}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Produit ajouté au panier !');
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de l\'ajout au panier');
                });
        }

        function toggleWishlist() {
            if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
                alert('Veuillez vous connecter pour ajouter aux favoris');
                return;
            }

            const action = isInWishlist ? 'remove' : 'add';

            fetch('../api/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${action}&product_id=${productId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        isInWishlist = !isInWishlist;
                        const btn = event.target.closest('.btn-secondary');
                        btn.classList.toggle('active');
                        btn.innerHTML = `<i class="fas fa-heart"></i> ${isInWishlist ? 'Dans les favoris' : 'Favoris'}`;
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la mise à jour des favoris');
                });
        }

        // Gestion du formulaire d'avis
        document.getElementById('reviewForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'add');
            formData.append('product_id', productId);
            formData.append('rating', selectedRating);

            if (selectedRating === 0) {
                alert('Veuillez sélectionner une note');
                return;
            }

            fetch('../api/reviews.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Avis envoyé avec succès ! Il sera visible après validation.');
                        this.reset();
                        setRating(0);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de l\'envoi de l\'avis');
                });
        });
    </script>

    <?php mysqli_close($cnx); ?>
</body>

</html>