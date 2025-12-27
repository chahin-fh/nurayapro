<?php
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos — Nuraya</title>
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

    .about-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 24px
    }

    .hero-section {
        background: var(--bg-white);
        border-radius: 20px;
        padding: 60px 40px;
        text-align: center;
        margin-bottom: 40px;
        box-shadow: 0 8px 30px rgba(200, 182, 166, 0.15)
    }

    .hero-title {
        font-size: 42px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 20px;
        letter-spacing: 1px
    }

    .hero-subtitle {
        font-size: 18px;
        color: var(--text-gray);
        line-height: 1.8;
        max-width: 600px;
        margin: 0 auto
    }

    .content-section {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--beige-dark)
    }

    .section-content {
        color: var(--text-gray);
        font-size: 16px;
        line-height: 1.8
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 30px
    }

    .value-card {
        text-align: center;
        padding: 30px 20px;
        background: var(--bg-light);
        border-radius: 12px;
        transition: transform 0.3s ease
    }

    .value-card:hover {
        transform: translateY(-5px)
    }

    .value-icon {
        font-size: 48px;
        color: var(--beige-dark);
        margin-bottom: 20px
    }

    .value-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 12px
    }

    .value-description {
        color: var(--text-gray);
        font-size: 14px;
        line-height: 1.6
    }

    .team-section {
        margin-top: 40px
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-top: 30px
    }

    .team-member {
        text-align: center
    }

    .member-avatar {
        width: 120px;
        height: 120px;
        background: var(--beige-dark);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: var(--bg-white);
        font-size: 36px;
        font-weight: 700
    }

    .member-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px
    }

    .member-role {
        color: var(--text-gray);
        font-size: 14px;
        margin-bottom: 12px
    }

    .member-bio {
        color: var(--text-gray);
        font-size: 14px;
        line-height: 1.6
    }

    .stats-section {
        background: linear-gradient(135deg, var(--beige-dark), #B8A29A);
        color: var(--bg-white);
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 30px
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        text-align: center
    }

    .stat-item {
        padding: 20px
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 8px
    }

    .stat-label {
        font-size: 16px;
        opacity: 0.9
    }

    .cta-section {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
    }

    .cta-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 16px
    }

    .cta-description {
        color: var(--text-gray);
        margin-bottom: 24px;
        font-size: 16px
    }

    .cta-buttons {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap
    }

    .btn {
        padding: 14px 32px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer
    }

    .btn-primary {
        background: var(--beige-dark);
        color: var(--bg-white)
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

    @media (max-width:768px) {
        .about-container {
            padding: 0 16px;
            margin: 20px auto
        }

        .hero-section {
            padding: 40px 20px
        }

        .hero-title {
            font-size: 32px
        }

        .hero-subtitle {
            font-size: 16px
        }

        .content-section {
            padding: 30px 20px
        }

        .values-grid,
        .team-grid {
            grid-template-columns: 1fr;
            gap: 20px
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px
        }

        .cta-buttons {
            flex-direction: column;
            align-items: center
        }

        .btn {
            width: 100%;
            max-width: 300px;
            justify-content: center
        }
    }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="about-container">
        <section class="hero-section">
            <h1 class="hero-title">Nuraya</h1>
            <p class="hero-subtitle">
                Découvrez l'élégance intemporelle à travers nos collections uniques,
                créées avec passion et savoir-faire artisanal.
            </p>
        </section>

        <section class="content-section">
            <h2 class="section-title">Notre Histoire</h2>
            <div class="section-content">
                <p>
                    Fondée en 2020, Nuraya est née d'une passion pour la mode et l'artisanat d'exception.
                    Notre mission est de vous offrir des pièces uniques qui allient tradition et modernité,
                    créées avec les matériaux les plus précieux et un souci du détail inégalé.
                </p>
                <p style="margin-top: 16px;">
                    Chaque création raconte une histoire, celle de son artisan et de l'inspiration qui l'a vue naître.
                    Nous croyons que la mode doit être une expression de soi-même, tout en respectant
                    les valeurs d'éthique et de durabilité qui nous sont chères.
                </p>
            </div>
        </section>

        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">5000+</div>
                    <div class="stat-label">Clients Satisfaits</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">150+</div>
                    <div class="stat-label">Créations Uniques</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">25</div>
                    <div class="stat-label">Pays Livrés</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.9★</div>
                    <div class="stat-label">Note Moyenne</div>
                </div>
            </div>
        </section>

        <section class="content-section">
            <h2 class="section-title">Nos Valeurs</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3 class="value-title">Qualité</h3>
                    <p class="value-description">
                        Des matériaux d'exception et un savoir-faire artisanal
                        pour des créations qui durent dans le temps.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="value-title">Durabilité</h3>
                    <p class="value-description">
                        Un engagement pour des pratiques respectueuses
                        de l'environnement et de nos artisans.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="value-title">Passion</h3>
                    <p class="value-description">
                        Chaque pièce est créée avec amour et dévouement,
                        reflétant notre passion pour le beau.
                    </p>
                </div>
            </div>
        </section>

        <section class="content-section team-section">
            <h2 class="section-title">Notre Équipe</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-avatar">NK</div>
                    <h3 class="member-name">Nour Kayat</h3>
                    <p class="member-role">Fondatrice & Designer</p>
                    <p class="member-bio">
                        Passionnée de mode depuis son enfance,
                        Nour transforme ses rêves en créations uniques.
                    </p>
                </div>
                <div class="team-member">
                    <div class="member-avatar">SK</div>
                    <h3 class="member-name">Sarah Khaled</h3>
                    <p class="member-role">Directrice Artistique</p>
                    <p class="member-bio">
                        Son œil expert et sa créativité sans limites
                        donnent vie à chaque collection.
                    </p>
                </div>
                <div class="team-member">
                    <div class="member-avatar">YB</div>
                    <h3 class="member-name">Youssef Ben</h3>
                    <p class="member-role">Responsable Production</p>
                    <p class="member-bio">
                        Garant de la qualité et de l'éthique
                        à chaque étape de création.
                    </p>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <h2 class="cta-title">Rejoignez l'Aventure Nuraya</h2>
            <p class="cta-description">
                Découvrez nos collections et trouvez la pièce qui vous ressemble.
            </p>
            <div class="cta-buttons">
                <a href="produits/index.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i>
                    Explorer les Collections
                </a>
                <a href="contact_us.php" class="btn btn-secondary">
                    <i class="fas fa-envelope"></i>
                    Nous Contacter
                </a>
            </div>
        </section>
    </div>

    <?php include 'templates/footer.php'; ?>
</body>

</html>