<?php
require_once 'includes/autoload.php';

// Récupérer les produits featured
$featured_query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 WHERE p.is_active = 1 AND p.is_featured = 1 
                 ORDER BY p.created_at DESC 
                 LIMIT 6";
$featured_result = mysqli_query($cnx, $featured_query);

// Récupérer les produits par collection
$collections_query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.category_id 
                    WHERE p.is_active = 1 AND c.slug IN ('printemps-ete-2025', 'collection-classique', 'collection-exclusive')
                    ORDER BY c.sort_order, p.created_at DESC";
$collections_result = mysqli_query($cnx, $collections_query);

// Organiser les produits par collection
$collections = [];
while ($product = mysqli_fetch_assoc($collections_result)) {
    $collections[$product['category_slug']][] = $product;
}
?>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuraya — Accueil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        /* Hero */
        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 500px;
            margin: 20px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(26, 28, 28, 0.7), rgba(26, 28, 28, 0.5)), url('assets/img/850x450-Pix_9-1.jpg') center/cover no-repeat;
            text-align: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--beige-dark) 0%, transparent 50%);
            opacity: 0.3
        }

        .hero-content {
            position: relative;
            z-index: 2
        }

        .hero h1 {
            font-size: 48px;
            letter-spacing: 4px;
            color: var(--bg-white);
            font-weight: 700;
            margin-bottom: 16px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3)
        }

        .hero-subtitle {
            font-size: 24px;
            color: var(--bg-white);
            font-weight: 500;
            letter-spacing: 1px;
            margin-bottom: 30px
        }

        .hero-btn {
            display: inline-block;
            padding: 15px 40px;
            background: var(--bg-white);
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 16px;
            letter-spacing: 1px
        }

        .hero-btn:hover {
            background: var(--beige-dark);
            color: var(--bg-white);
            transform: translateY(-2px)
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 80px auto;
            padding: 0 24px
        }

        .content-items {
            display: flex;
            flex-direction: column;
            gap: 20px
        }

        .content-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 30px;
            background: var(--bg-white);
            border-radius: 12px;
            text-decoration: none;
            border: 1px solid rgba(200, 182, 166, 0.2);
            transition: all 0.3s ease
        }

        .content-item:hover {
            background: var(--beige-dark);
            color: var(--bg-white);
            transform: translateX(10px)
        }

        .content-item-left {
            display: flex;
            align-items: center;
            gap: 15px
        }

        .item-arrow {
            font-size: 24px;
            font-weight: bold;
            color: var(--beige-dark);
            transition: color 0.3s ease
        }

        .content-item:hover .item-arrow {
            color: var(--bg-white)
        }

        .item-text {
            font-size: 18px;
            font-weight: 500;
            color: var(--text-dark);
            transition: color 0.3s ease
        }

        .content-item:hover .item-text {
            color: var(--bg-white)
        }

        .item-plus {
            font-size: 28px;
            font-weight: bold;
            color: var(--beige-dark);
            transition: color 0.3s ease
        }

        .content-item:hover .item-plus {
            color: var(--bg-white)
        }

        /* Other Collections Section */
        .other-collections {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 24px
        }

        .collections-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px
        }

        .collection-card {
            background: var(--bg-white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(200, 182, 166, 0.15);
            transition: all 0.3s ease;
            border: 1px solid rgba(200, 182, 166, 0.2)
        }

        .collection-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 15px 40px rgba(200, 182, 166, 0.25);
            border-color: var(--beige-dark)
        }

        .collection-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            position: relative
        }

        .collection-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease
        }

        .collection-card:hover .collection-image img {
            transform: scale(1.08)
        }

        .collection-info {
            padding: 30px;
            text-align: center
        }

        .collection-info h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 16px;
            letter-spacing: 0.5px
        }

        .collection-info p {
            color: var(--text-gray);
            line-height: 1.6;
            margin-bottom: 24px;
            font-size: 15px
        }

        .collection-btn {
            display: inline-block;
            background: var(--beige-dark);
            color: var(--bg-white);
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px
        }

        .collection-btn:hover {
            background: var(--text-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(200, 182, 166, 0.3)
        }

        /* Products */
        .section {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 24px
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px
        }

        .section h2 {
            font-size: 32px;
            margin-bottom: 12px;
            color: var(--text-dark);
            font-weight: 600;
            letter-spacing: 1px
        }

        .section-subtitle {
            color: var(--text-gray);
            font-size: 16px;
            font-weight: 400
        }

        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px
        }

        .product-card {
            background: var(--bg-white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(200, 182, 166, 0.15);
            transition: all 0.3s ease;
            text-decoration: none;
            border: 1px solid rgba(200, 182, 166, 0.2)
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(200, 182, 166, 0.25);
            border-color: var(--beige-dark)
        }

        .product-image {
            width: 100%;
            height: 340px;
            object-fit: cover;
            display: block;
            background: var(--bg-light);
            transition: transform 0.3s ease
        }

        .product-card:hover .product-image {
            transform: scale(1.05)
        }

        .product-info {
            padding: 24px
        }

        .product-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 12px;
            font-size: 16px;
            line-height: 1.4
        }

        .product-price {
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

        .current-price {
            color: var(--beige-dark);
            font-weight: 700
        }

        @media (max-width:992px) {
            .hero {
                min-height: 400px;
                margin: 15px;
                padding: 40px 20px
            }

            .hero h1 {
                font-size: 42px;
                letter-spacing: 4px
            }

            .collections-container {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width:768px) {
            .section h2 {
                font-size: 28px
            }

            .products-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px
            }

            .product-image {
                height: 250px
            }
        }

        @media (max-width:600px) {
            .hero h1 {
                font-size: 32px;
                letter-spacing: 2px
            }

            .hero-subtitle {
                font-size: 18px;
            }

            .products-container {
                grid-template-columns: 1fr;
                gap: 16px
            }

            .product-image {
                height: 320px;
            }

            .product-info {
                padding: 20px
            }
        }
    </style>
</head>

<body>
    <header>
        <?php include 'templates/navbar_updated.php'; ?>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Mode</h1>
            <p class="hero-subtitle">Collection Automne - Hiver 2025/26</p>
            <a href="#" class="hero-btn">Voir Plus</a>
        </div>
    </section>

    <!-- Other Collections Section -->
    <section class="other-collections">
        <div class="collections-container">
            <div class="collection-card">
                <div class="collection-image">
                    <img src="<?php echo get_image_url('img/collection-1.jpg'); ?>" alt="Printemps - Été 2025"
                        onerror="this.src='https://via.placeholder.com/400x300/F5EFE6/C8B6A6?text=Printemps+Été'">
                </div>
                <div class="collection-info">
                    <h3>Printemps - Été 2025</h3>
                    <p>Découvrez notre nouvelle collection printanière aux couleurs vibrantes et aux tissus légers.</p>
                    <a href="/nurayapro/produits/index.php?category=printemps-ete" class="collection-btn">Explorer</a>
                </div>
            </div>

            <div class="collection-card">
                <div class="collection-image">
                    <img src="<?php echo get_image_url('img/collection-2.jpg'); ?>" alt="Collection Classique"
                        onerror="this.src='https://via.placeholder.com/400x300/F5EFE6/C8B6A6?text=Classique'">
                </div>
                <div class="collection-info">
                    <h3>Collection Classique</h3>
                    <p>Des pièces intemporelles qui traversent les saisons avec élégance et raffinement.</p>
                    <a href="/nurayapro/produits/index.php?category=classique" class="collection-btn">Explorer</a>
                </div>
            </div>

            <div class="collection-card">
                <div class="collection-image">
                    <img src="<?php echo get_image_url('img/collection-3.jpg'); ?>" alt="Collection Exclusive"
                        onerror="this.src='https://via.placeholder.com/400x300/F5EFE6/C8B6A6?text=Exclusive'">
                </div>
                <div class="collection-info">
                    <h3>Collection Exclusive</h3>
                    <p>Des pièces uniques en édition limitée, créées avec les matériaux les plus précieux.</p>
                    <a href="/nurayapro/produits/index.php?category=exclusive" class="collection-btn">Explorer</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="section">
        <div class="section-header">
            <h2>Produits Vedettes</h2>
            <p class="section-subtitle">Découvrez nos pièces les plus populaires</p>
        </div>
        <div class="products-container">
            <?php if (mysqli_num_rows($featured_result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($featured_result)): ?>
                    <a href="/nurayapro/src/Controllers/produits/product.php?id=<?php echo $product['product_id']; ?>" class="product-card">
                        <img src="<?php echo get_image_url($product['image_url'], 'Produit'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="product-image"
                            onerror="this.src='https://via.placeholder.com/280x340/F5EFE6/C8B6A6?text=Produit'">
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-price">
                                <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                    <span class="compare-price"><?php echo number_format($product['compare_price'], 3); ?> DT</span>
                                <?php endif; ?>
                                <span class="current-price"><?php echo number_format($product['price'], 3); ?> DT</span>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--text-gray); grid-column: 1/-1;">
                    Aucun produit vedette disponible pour le moment.
                </p>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="/nurayapro/src/Controllers/produits/index.php" class="hero-btn"
                style="background: var(--beige-dark); color: var(--bg-white);">
                Voir Tous les Produits
            </a>
        </div>
    </section>

    <div class="main-content">
        <div class="content-items">
            <a href="#" class="content-item">
                <div class="content-item-left">
                    <span class="item-arrow">></span>
                    <span class="item-text">Contacter un Conseiller</span>
                </div>
                <span class="item-plus">+</span>
            </a>

            <a href="#" class="content-item">
                <div class="content-item-left">
                    <span class="item-arrow">></span>
                    <span class="item-text">Must Have</span>
                </div>
                <span class="item-plus">+</span>
            </a>

            <a href="#" class="content-item">
                <div class="content-item-left">
                    <span class="item-arrow">></span>
                    <span class="item-text">About US</span>
                </div>
                <span class="item-plus">+</span>
            </a>
        </div>
    </div>

    <?php include 'templates/footer.php'; ?>

</body>

</html>