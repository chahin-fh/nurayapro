<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuraya — Accueil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--primary:#000;--muted:#6b7280;--accent:#ff6b6b;--bg:#f9f9f9}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Montserrat',sans-serif;background:var(--bg);color:#111}

        /* Hero */
        .hero{display:flex;align-items:center;justify-content:center;flex-direction:column;min-height:420px;margin:22px 28px;border-radius:12px;background:linear-gradient(90deg,rgba(0,0,0,0.5),rgba(0,0,0,0.5)),url('img/850x450-Pix_9-1.jpg') center/cover no-repeat;text-align:center;padding:40px}
        .hero h1{font-size:48px;letter-spacing:4px;color:white}
        .hero p{margin-top:8px;color:rgba(255,255,255,0.9)}

        /* Products */
        .section{max-width:1200px;margin:34px auto;padding:0 24px}
        .section h2{font-size:22px;margin-bottom:18px;color:#071033}
        .products-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:22px}
        .product-card{background:white;border-radius:14px;overflow:hidden;box-shadow:0 12px 30px rgba(15,23,42,0.06);transition:transform .22s}
        .product-card:hover{transform:translateY(-6px)}
        .product-image{width:100%;height:320px;object-fit:cover;display:block;background:#f0f0f0;border-radius:12px 12px 0 0}
        .product-info{padding:14px}
        .product-name{font-weight:700;color:#071033;margin-bottom:8px}
        .product-price{color:var(--accent);font-weight:800}

        @media (max-width:900px){
            .hero h1{font-size:34px}
            .product-image{height:220px}
        }
    </style>
</head>
<body>
    <header>
        <?php include('navbar.php'); ?>
    </header>

    <section class="hero">
        <h1>NURAYA</h1>
        <p>L'ART DE LA MODESTIE</p>
    </section>

    <div class="section">
        <h2 class="drop-title">Legacy of Carthage — Collection</h2>
        <div class="products-container">
            <a href="shop">
                <div class="product-card">
                    <img class="product-image" src="img/IMG_8003.webp">
                    <div class="product-info">
                        <div class="product-name">Legacy of Carthage: Human Hotdle</div>
                        <div class="product-price">85.000 DT</div>
                    </div>
                </div>
            </a>

            <a href="shop">
                <div class="product-card">
                    <img class="product-image" src="img/Gear-Cargo-Pants-are-Back-1302952122.webp">
                    <div class="product-info">
                        <div class="product-name">Legacy of Carthage: Mandarin's Hotdle</div>
                        <div class="product-price">95.000 DT</div>
                    </div>
                </div>
            </a>

            <a href="shop">
                <div class="product-card">
                    <img class="product-image" src="img/9.webp">
                    <div class="product-info">
                        <div class="product-name">Carthagenias Helmet (Balasiana)</div>
                        <div class="product-price">10.000 DT</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

</body>
</html>