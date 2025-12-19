<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#000;--muted:#6b7280;--accent:#ff6b6b;--bg:#f9f9f9;--light:#fff}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Montserrat',sans-serif;background:var(--bg);color:#333;line-height:1.6}
        
        /* Hero Section */
        .hero{background:linear-gradient(135deg,#000 0%,#1a1a1a 100%);color:white;padding:80px 28px;text-align:center;border-radius:12px;margin:30px 28px}
        .hero h1{font-size:48px;margin-bottom:16px;letter-spacing:2px}
        .hero p{font-size:18px;color:rgba(255,255,255,0.85);max-width:600px;margin:0 auto}
        
        /* Container */
        .container{max-width:1000px;margin:0 auto;padding:40px 28px}
        
        /* Sections */
        .section{margin-bottom:60px}
        .section-title{font-size:32px;font-weight:700;margin-bottom:24px;color:var(--primary);position:relative;padding-bottom:12px}
        .section-title::after{content:'';position:absolute;bottom:0;left:0;width:60px;height:3px;background:var(--accent)}
        
        /* Feature Cards */
        .features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;margin-top:28px}
        .feature-card{background:white;padding:28px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);transition:transform 0.3s,box-shadow 0.3s;border-left:4px solid var(--accent)}
        .feature-card:hover{transform:translateY(-4px);box-shadow:0 6px 16px rgba(0,0,0,0.12)}
        .feature-card h3{font-size:18px;margin-bottom:12px;color:var(--primary)}
        .feature-card p{color:var(--muted);line-height:1.7;font-size:14px}
        
        /* Content Box */
        .content-block{background:white;padding:32px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:28px}
        .content-block p{color:#555;line-height:1.8;font-size:15px;margin-bottom:14px}
        .content-block p:last-child{margin-bottom:0}
        
        /* Highlight Box */
        .highlight-box{background:linear-gradient(135deg,rgba(255,107,107,0.1) 0%,rgba(255,107,107,0.05) 100%);padding:28px;border-radius:10px;border-left:4px solid var(--accent);margin:28px 0}
        .highlight-box p{color:var(--primary);font-weight:600;font-size:16px;margin:0}
        
        /* Stats Section */
        .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:24px;margin-top:28px;text-align:center}
        .stat{padding:24px}
        .stat-number{font-size:36px;font-weight:700;color:var(--accent);margin-bottom:8px}
        .stat-label{color:var(--muted);font-size:14px;font-weight:600}
        
        /* CTA Button */
        .cta-button{display:inline-block;background:var(--primary);color:white;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;transition:background 0.3s;margin-top:20px;border:none;cursor:pointer}
        .cta-button:hover{background:var(--accent)}
        
        /* Responsive */
        @media (max-width:900px){
            .hero{padding:60px 20px;margin:20px;font-size:32px}
            .hero h1{font-size:32px}
            .section-title{font-size:24px}
            .container{padding:30px 20px}
        }
        
        @media (max-width:600px){
            .hero h1{font-size:28px}
            .navbar{padding:16px 20px}
        }
    </style>
</head>
<body>
    <header>
        <?php include('navbar.php'); ?>
    </header>
    
    <section class="hero">
        <h1>About Nuraya</h1>
        <p>Crafting meaningful experiences through thoughtful design and authentic storytelling</p>
    </section>
    
    <div class="container">
        <!-- Our Mission Section -->
        <section class="section">
            <h2 class="section-title">Our Mission</h2>
            <div class="content-block">
                <p>At Nuraya, we believe in the power of mindful design and authentic expression. Our mission is to create products and experiences that resonate with your values, inspire confidence, and bring beauty into your everyday life.</p>
                <p>We are committed to quality, sustainability, and ethical practices in everything we do. Every product tells a story—of craftsmanship, innovation, and purpose.</p>
            </div>
        </section>
        
        <!-- Core Values Section -->
        <section class="section">
            <h2 class="section-title">Our Values</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3><i class="fas fa-leaf" style="color:var(--accent);margin-right:8px"></i>Sustainability</h3>
                    <p>We prioritize eco-friendly materials and ethical production practices to minimize our environmental impact and support responsible sourcing.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fas fa-lightbulb" style="color:var(--accent);margin-right:8px"></i>Innovation</h3>
                    <p>Continuous improvement drives us forward. We blend traditional craftsmanship with modern design thinking to create timeless products.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fas fa-heart" style="color:var(--accent);margin-right:8px"></i>Authenticity</h3>
                    <p>We believe in honest communication and transparent practices. Our customers deserve to know the story behind every product.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fas fa-users" style="color:var(--accent);margin-right:8px"></i>Community</h3>
                    <p>We're building a community of like-minded individuals who value quality, creativity, and positive change in the world.</p>
                </div>
            </div>
        </section>
        
        <!-- Our Story Section -->
        <section class="section">
            <h2 class="section-title">Our Story</h2>
            <div class="content-block">
                <p>Nuraya was founded on the belief that great design should be accessible to everyone. What started as a small vision has grown into a movement of conscious consumers and creators who demand more from their products—not just quality, but meaning.</p>
                <p>Today, we continue to push boundaries, challenge conventions, and create experiences that matter. Our journey is ongoing, and we're grateful to have you as part of it.</p>
            </div>
            <div class="highlight-box">
                <p>"Excellence is not a destination—it's a commitment to continuous improvement and unwavering integrity."</p>
            </div>
        </section>
        
        <!-- By the Numbers Section -->
        <section class="section">
            <h2 class="section-title">By the Numbers</h2>
            <div class="stats">
                <div class="stat">
                    <div class="stat-number">5K+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Unique Products</div>
                </div>
                <div class="stat">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Authentic</div>
                </div>
            </div>
        </section>
        
        <!-- Call to Action Section -->
        <section class="section" style="text-align:center;background:white;padding:40px;border-radius:10px">
            <h2 class="section-title" style="text-align:center;margin-bottom:16px">Ready to Join Us?</h2>
            <p style="color:var(--muted);margin-bottom:24px;font-size:15px">Explore our collection and discover products that resonate with your values.</p>
            <a href="shop" class="cta-button">Shop Now</a>
        </section>
    </div>
</body>
</html>