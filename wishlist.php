<?php
include 'cnx.php';
include 'functions.php';

// Vérifier si l'utilisateur est connecté
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Récupérer les favoris de l'utilisateur
$wishlist_query = "SELECT w.*, p.name, p.price, p.image_url, c.name as category_name, c.slug as category_slug
                  FROM wishlist w 
                  LEFT JOIN products p ON w.product_id = p.product_id 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  WHERE w.user_id = " . $_SESSION['user_id'] . " AND p.is_active = 1
                  ORDER BY w.created_at DESC";
$wishlist_result = mysqli_query($cnx, $wishlist_query);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris — Nuraya</title>
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

        .wishlist-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px
        }

        .page-subtitle {
            color: var(--text-gray);
            font-size: 16px
        }

        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px
        }

        .wishlist-item {
            background: var(--bg-white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
            transition: transform 0.3s ease;
            position: relative
        }

        .wishlist-item:hover {
            transform: translateY(-8px)
        }

        .remove-wishlist {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-pink);
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 10
        }

        .remove-wishlist:hover {
            background: var(--bg-white);
            transform: scale(1.1)
        }

        .product-link {
            text-decoration: none;
            color: inherit;
            display: block
        }

        .product-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            background: var(--bg-light)
        }

        .product-info {
            padding: 20px
        }

        .product-category {
            color: var(--text-gray);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px
        }

        .product-name {
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
            gap: 12px
        }

        .price {
            font-size: 20px;
            font-weight: 700;
            color: var(--beige-dark)
        }

        .add-to-cart-btn {
            background: var(--beige-dark);
            color: var(--bg-white);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease
        }

        .add-to-cart-btn:hover {
            background: var(--text-dark);
            transform: translateY(-2px)
        }

        .empty-wishlist {
            text-align: center;
            padding: 80px 20px;
            background: var(--bg-white);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
        }

        .empty-wishlist i {
            font-size: 64px;
            color: var(--beige-dark);
            margin-bottom: 24px
        }

        .empty-wishlist h2 {
            font-size: 24px;
            color: var(--text-dark);
            margin-bottom: 12px
        }

        .empty-wishlist p {
            color: var(--text-gray);
            margin-bottom: 24px
        }

        .shop-now-btn {
            background: var(--beige-dark);
            color: var(--bg-white);
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease
        }

        .shop-now-btn:hover {
            background: var(--text-dark);
            transform: translateY(-2px)
        }

        @media (max-width:768px) {
            .wishlist-container {
                margin: 20px auto;
                padding: 0 16px
            }

            .page-title {
                font-size: 28px
            }

            .wishlist-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px
            }
        }

        @media (max-width:480px) {
            .wishlist-grid {
                grid-template-columns: 1fr;
                gap: 16px
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar_updated.php'; ?>

    <div class="wishlist-container">
        <div class="page-header">
            <h1 class="page-title">Mes Favoris</h1>
            <p class="page-subtitle">Les produits que vous avez sauvegardés</p>
        </div>

        <?php if (mysqli_num_rows($wishlist_result) > 0): ?>
            <div class="wishlist-grid">
                <?php while ($item = mysqli_fetch_assoc($wishlist_result)): ?>
                    <div class="wishlist-item">
                        <button class="remove-wishlist" onclick="removeFromWishlist(<?php echo $item['product_id']; ?>)">
                            <i class="fas fa-times"></i>
                        </button>
                        <a href="produits/product.php?id=<?php echo $item['product_id']; ?>" class="product-link">
                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"
                                class="product-image"
                                onerror="this.src='https://via.placeholder.com/280x280/F5EFE6/C8B6A6?text=Produit'">
                            <div class="product-info">
                                <div class="product-category"><?php echo htmlspecialchars($item['category_name']); ?></div>
                                <h3 class="product-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="product-price">
                                    <span class="price"><?php echo number_format($item['price'], 3); ?> DT</span>
                                    <button class="add-to-cart-btn"
                                        onclick="addToCart(event, <?php echo $item['product_id']; ?>)">
                                        <i class="fas fa-shopping-bag"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-wishlist">
                <i class="fas fa-heart"></i>
                <h2>Votre liste de favoris est vide</h2>
                <p>Ajoutez des produits à vos favoris pour les retrouver facilement</p>
                <a href="produits/index.php" class="shop-now-btn">
                    <i class="fas fa-shopping-bag"></i> Découvrir les produits
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function removeFromWishlist(productId) {
            if (!confirm('Êtes-vous sûr de vouloir retirer ce produit de vos favoris ?')) {
                return;
            }

            fetch('api/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&product_id=${productId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du retrait des favoris');
                });
        }

        function addToCart(event, productId) {
            event.preventDefault();

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
                        alert('Produit ajouté au panier !');
                        // Mettre à jour le compteur du panier
                        const cartCount = document.getElementById('cartCount');
                        if (cartCount) {
                            const currentCount = parseInt(cartCount.textContent);
                            cartCount.textContent = currentCount + 1;
                        }
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de l\'ajout au panier');
                });
        }
    </script>
</body>

</html>