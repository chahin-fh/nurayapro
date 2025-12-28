<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Nuraya</title>
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

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--beige-dark) 0%, rgba(200, 182, 166, 0.8) 100%);
            color: var(--bg-white);
            padding: 80px 28px;
            text-align: center;
            border-radius: 16px;
            margin: 30px 28px;
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
            background: linear-gradient(135deg, var(--text-dark) 0%, transparent 50%);
            opacity: 0.2
        }

        .hero-content {
            position: relative;
            z-index: 2
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 16px;
            letter-spacing: 2px;
            font-weight: 700
        }

        .hero p {
            font-size: 18px;
            color: var(--bg-white);
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.95
        }

        /* Container */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 28px
        }

        /* Sections */
        .section {
            margin-bottom: 60px
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--text-dark);
            position: relative;
            padding-bottom: 12px
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--beige-dark)
        }

        /* Feature Cards */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 28px
        }

        .feature-card {
            background: var(--bg-white);
            padding: 28px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--beige-dark)
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(200, 182, 166, 0.25)
        }

        .feature-card h3 {
            font-size: 18px;
            margin-bottom: 12px;
            color: var(--text-dark);
            font-weight: 600
        }

        .feature-card p {
            color: var(--text-gray);
            line-height: 1.7;
            font-size: 14px
        }

        /* Content Box */
        .content-block {
            background: var(--bg-white);
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
            margin-bottom: 28px
        }

        .content-block p {
            color: var(--text-dark);
            line-height: 1.8;
            font-size: 15px;
            margin-bottom: 14px
        }

        .content-block p:last-child {
            margin-bottom: 0
        }

        /* Highlight Box */
        .highlight-box {
            background: linear-gradient(135deg, var(--accent-pink) 0%, rgba(230, 183, 200, 0.1) 100%);
            padding: 28px;
            border-radius: 16px;
            border-left: 4px solid var(--beige-dark);
            margin: 28px 0
        }

        .highlight-box p {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 16px;
            margin: 0
        }

        /* Stats Section */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-top: 28px;
            text-align: center
        }

        .stat {
            padding: 24px;
            background: var(--bg-white);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--beige-dark);
            margin-bottom: 8px
        }

        .stat-label {
            color: var(--text-gray);
            font-size: 14px;
            font-weight: 600
        }

        /* CTA Button */
        .cta-button {
            display: inline-block;
            background: var(--beige-dark);
            color: var(--bg-white);
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 20px;
            border: none;
            cursor: pointer
        }

        .cta-button:hover {
            background: var(--text-dark);
            transform: translateY(-2px)
        }

        /* Responsive */
        @media (max-width:900px) {
            .hero {
                padding: 60px 20px;
                margin: 20px
            }

            .hero h1 {
                font-size: 32px
            }

            .section-title {
                font-size: 24px
            }

            .container {
                padding: 30px 20px
            }
        }

        @media (max-width:600px) {
            .hero h1 {
                font-size: 28px
            }

            .navbar {
                padding: 16px 20px
            }
        }
    </style>
</head>

<body>
    <header>
        <?php include 'templates/navbar_updated.php'; ?>
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
                <p>At Nuraya, we believe in the power of mindful design and authentic expression. Our mission is to
                    create products and experiences that resonate with your values, inspire confidence, and bring beauty
                    into your everyday life.</p>
                <p>We are committed to quality, sustainability, and ethical practices in everything we do. Every product
                    tells a story—of craftsmanship, innovation, and purpose.</p>
            </div>
        </section>

        <!-- Core Values Section -->
        <section class="section">
            <h2 class="section-title">Our Values</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3><i class="fas fa-leaf" style="color:var(--accent);margin-right:8px"></i>Sustainability</h3>
                    <p>We prioritize eco-friendly materials and ethical production practices to minimize our
                        environmental impact and support responsible sourcing.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fas fa-lightbulb" style="color:var(--accent);margin-right:8px"></i>Innovation</h3>
                    <p>Continuous improvement drives us forward. We blend traditional craftsmanship with modern design
                        thinking to create timeless products.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fas fa-heart" style="color:var(--accent);margin-right:8px"></i>Authenticity</h3>
                    <p>We believe in honest communication and transparent practices. Our customers deserve to know the
                        story behind every product.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fas fa-users" style="color:var(--accent);margin-right:8px"></i>Community</h3>
                    <p>We're building a community of like-minded individuals who value quality, creativity, and positive
                        change in the world.</p>
                </div>
            </div>
        </section>

        <!-- Our Story Section -->
        <section class="section">
            <h2 class="section-title">Our Story</h2>
            <div class="content-block">
                <p>Nuraya was founded on the belief that great design should be accessible to everyone. What started as
                    a small vision has grown into a movement of conscious consumers and creators who demand more from
                    their products—not just quality, but meaning.</p>
                <p>Today, we continue to push boundaries, challenge conventions, and create experiences that matter. Our
                    journey is ongoing, and we're grateful to have you as part of it.</p>
            </div>
            <div class="highlight-box">
                <p>"Excellence is not a destination—it's a commitment to continuous improvement and unwavering
                    integrity."</p>
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
            <p style="color:var(--muted);margin-bottom:24px;font-size:15px">Explore our collection and discover products
                that resonate with your values.</p>
            <a href="shop.php" class="cta-button">Shop Now</a>
        </section>
    </div>
</body>

</html>