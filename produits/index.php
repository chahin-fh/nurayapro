<?php
include '../cnx.php';
$result = mysqli_query($cnx,"SELECT * from products");
$i = 0 ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Shop — Nuraya</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#000;--muted:#6b7280;--accent:#ff6b6b;--bg:#f9f9f9}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Montserrat',sans-serif;background:var(--bg);color:#111}
        /* Navbar */
        .navbar{display:flex;align-items:center;justify-content:space-between;padding:18px 28px;background:transparent}
        .navbar .logo{font-weight:700;font-size:20px;color:var(--primary);text-decoration:none}
        .nav-links{display:flex;gap:18px;list-style:none;margin:0;padding:0;align-items:center}
        .nav-links a{color:var(--primary);text-decoration:none;font-weight:600;padding:8px 10px;border-radius:6px}
        .nav-links a:hover{color:var(--accent)}
        .icons{display:flex;gap:12px;align-items:center}
        .icons a{color:var(--primary);text-decoration:none;cursor:pointer;transition:color 0.3s}
        .icons a:hover{color:var(--accent)}
        .cart-icon{position:relative}
        .cart-count{position:absolute;top:-6px;right:-8px;background:var(--accent);color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;font-size:11px}
        @media (max-width:768px){.nav-links{display:none}}

        /* Featured Products Section */
        .featured-products{max-width:1200px;margin:40px auto;padding:0 24px}
        .section-title{font-size:24px;margin-bottom:24px;color:var(--primary);font-weight:700}
        .products{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:22px}
        .product-card{background:white;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.08);transition:transform 0.3s,box-shadow 0.3s;cursor:pointer}
        .product-card:hover{transform:translateY(-6px);box-shadow:0 8px 20px rgba(0,0,0,0.12)}
        .product-image{width:100%;height:280px;object-fit:cover;display:block;background:#f0f0f0}
        .product-info{padding:16px}
        .product-title{font-weight:700;color:var(--primary);margin-bottom:8px;font-size:15px}
        .product-price{display:flex;justify-content:space-between;align-items:center;gap:8px}
        .current-price{color:var(--accent);font-weight:800;font-size:16px}
        .add-to-cart{background:var(--primary);color:white;border:none;width:38px;height:38px;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.3s}
        .add-to-cart:hover{background:var(--accent)}

        /* Cart Modal */
        .modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;z-index:1000}
        .modal-overlay.show{display:flex}
        .cart-modal{background:white;border-radius:12px;width:90%;max-width:500px;max-height:80vh;overflow-y:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2)}
        .cart-header{display:flex;justify-content:space-between;align-items:center;padding:20px;border-bottom:1px solid #e5e7eb}
        .cart-header h2{margin:0;color:var(--primary)}
        .close-cart{background:none;border:none;font-size:24px;cursor:pointer;color:var(--muted)}
        .close-cart:hover{color:var(--primary)}
        .cart-items{padding:20px}
        .empty-cart{text-align:center;padding:40px 20px;color:var(--muted)}
        .cart-total{padding:20px;border-top:1px solid #e5e7eb;font-weight:700;text-align:right;color:var(--primary)}
        .checkout-btn{width:100%;padding:12px;background:var(--primary);color:white;border:none;border-radius:8px;font-weight:700;cursor:pointer;margin-top:10px}
        .checkout-btn:hover{background:var(--accent)}

        /* Cart Item Styles */
        .cart-item{display:flex;gap:12px;padding:12px;border-bottom:1px solid #e5e7eb;align-items:flex-start}
        .cart-item-img{width:70px;height:70px;object-fit:cover;border-radius:6px;background:#f0f0f0}
        .cart-item-details{flex:1}
        .cart-item-title{margin:0;font-size:14px;font-weight:600;color:var(--primary)}
        .cart-item-price{margin:4px 0;font-size:13px;color:var(--accent)}
        .cart-item-remove{background:none;border:none;color:var(--muted);cursor:pointer;font-size:12px;padding:0;margin-top:6px}
        .cart-item-remove:hover{color:var(--accent)}
        .cart-item-quantity{font-weight:700;color:var(--primary);font-size:14px}
        
        /* Product Detail Modal */
        .product-detail-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.6);display:none;align-items:center;justify-content:center;z-index:1100}
        .product-detail-overlay.show{display:flex}
        .product-detail-modal{background:white;border-radius:16px;width:90%;max-width:700px;max-height:90vh;overflow-y:auto;box-shadow:0 10px 50px rgba(0,0,0,0.3)}
        .product-detail-header{display:flex;justify-content:space-between;align-items:center;padding:24px;border-bottom:1px solid #e5e7eb}
        .product-detail-header h2{margin:0;color:var(--primary);font-size:24px}
        .close-detail{background:none;border:none;font-size:28px;cursor:pointer;color:var(--muted);padding:0;width:32px;height:32px;display:flex;align-items:center;justify-content:center}
        .close-detail:hover{color:var(--primary)}
        .product-detail-body{padding:24px}
        .detail-image{width:100%;height:350px;object-fit:cover;border-radius:12px;margin-bottom:20px}
        .detail-info{margin-bottom:20px}
        .detail-price{font-size:28px;color:var(--accent);font-weight:800;margin:16px 0}
        .detail-description{color:#555;line-height:1.8;margin:16px 0;font-size:15px}
        .detail-quantity{margin:20px 0}
        .detail-quantity label{display:block;margin-bottom:8px;font-weight:600;color:var(--primary)}
        .detail-quantity input{width:80px;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:14px}
        .detail-buttons{display:flex;gap:12px;margin-top:24px}
        .detail-add-btn{flex:1;padding:14px;background:var(--primary);color:white;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:16px;transition:background 0.3s}
        .detail-add-btn:hover{background:var(--accent)}
        .detail-continue-btn{flex:1;padding:14px;background:var(--accent);color:white;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:16px;transition:background 0.3s}
        .detail-continue-btn:hover{background:#ff5252}
        
        /* Make product cards clickable */
        .product-card{cursor:pointer}
        .product-card:not(:has(.add-to-cart:hover)){cursor:pointer}
        
        /* Floating Cart Badge */
        .floating-cart-badge{position:fixed;bottom:30px;right:30px;z-index:999;background:var(--primary);color:white;width:60px;height:60px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,0.2);transition:all 0.3s ease;border:none;font-size:24px}
        .floating-cart-badge:hover{background:var(--accent);transform:scale(1.1);box-shadow:0 6px 24px rgba(0,0,0,0.3)}
        .floating-cart-badge .cart-count{position:absolute;top:-5px;right:-5px;background:var(--accent);color:white;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700}

        @media (max-width:768px){.products{grid-template-columns:1fr}.floating-cart-badge{bottom:20px;right:20px;width:50px;height:50px}.floating-cart-badge i{font-size:20px}}
    </style>
</head>
<body>
    <header>
        <?php include('../navbar.php');?>
    </header>
    <section class="featured-products">
        <h2 class="section-title">Featured Products</h2>
        <div class="products">
            <?php while($t= mysqli_fetch_assoc($result)) :
            $i++ ;
            ?>
            <div class="product-card" data-product-id="<?php echo $t['product_id'] ; ?>" data-name="<?php echo htmlspecialchars($t['name']) ; ?>" data-price="<?php echo $t['price'] ; ?>" data-description="<?php echo htmlspecialchars($t['description'] ?? 'High-quality product') ; ?>" data-image="<?php echo strpos($t['image_url'], 'http') === 0 ? $t['image_url'] : '../uploads/' . $t['image_url']; ?>">
                <div class="product-image">
                    <img src="../<?php echo $t['image_url'] ; ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo $t['name'] ; ?></h3>
                    <div class="product-price">
                        <div>
                            <span class="current-price"><?php echo $t['price'] ; ?> DT</span>
                        </div>
                        <button class="add-to-cart" data-product-id = "<?php echo $t['product_id'] ; ?>" data-id="<?php echo $i ; ?>" data-name="<?php echo $t['name'] ; ?>" data-price="<?php echo $t['price'] ; ?>" data-image="<?php echo strpos($t['image_url'], 'http') === 0 ? $t['image_url'] : '../uploads/' . $t['image_url']; ?>">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endwhile ; ?>
    </section>

    <div class="modal-overlay">
        <div class="cart-modal">
            <div class="cart-header">
                <h2>Your Cart</h2>
                <button class="close-cart" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="/nuraya_pro/checkout" method="post">
                <div class="cart-items">
                    <!-- Cart items will be added here dynamically -->
                    <div class="empty-cart">
                        <i class="fas fa-shopping-bag" style="font-size: 40px; margin-bottom: 15px;"></i>
                        <p>Your cart is empty</p>
                    </div>
                </div>
                <div class="cart-total">
                    Total: <span>0.00 DT</span>
                </div>
                <input class="checkout-btn" type="submit" value="Checkout">
            </form>
        </div>
    </div>
    
    <!-- Product Detail Modal -->
    <div class="product-detail-overlay" id="productDetailOverlay">
        <div class="product-detail-modal">
            <div class="product-detail-header">
                <h2 id="detailProductName">Product Name</h2>
                <button class="close-detail" type="button" id="closeProductDetail">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="product-detail-body">
                <img id="detailProductImage" src="" alt="Product" class="detail-image">
                <div class="detail-info">
                    <div class="detail-price"><span id="detailProductPrice">0.00</span> DT</div>
                    <p class="detail-description" id="detailProductDescription">Product description goes here</p>
                </div>
                <div class="detail-quantity">
                    <label for="detailQuantityInput">Quantity:</label>
                    <input type="number" id="detailQuantityInput" min="1" value="1">
                </div>
                <div class="detail-buttons">
                    <button class="detail-add-btn" id="detailAddToCartBtn">
                        <i class="fas fa-shopping-bag"></i> Add to Cart
                    </button>
                    <button class="detail-continue-btn" id="detailContinueShoppingBtn">
                        Continue Shopping
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Floating Cart Badge -->
    <button class="floating-cart-badge" id="floatingCartBadge">
        <i class="fas fa-shopping-bag"></i>
        <span class="cart-count" id="cartBadgeCount">0</span>
    </button>
    
    <script src="main.js?v=<?php echo time(); ?>"></script>
    <?php mysqli_close($cnx); ?>
    </body>
</html>