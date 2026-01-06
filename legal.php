<?php
session_start();
require_once 'config/database.php';
require_once 'config/settings.php';

$page_title = "Mentions Légales - NURAYA";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
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
            padding: 0;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .legal-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 24px;
        }
        
        .legal-header {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 8px 30px rgba(200, 182, 166, 0.15);
        }
        
        .legal-header h1 {
            font-size: 42px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .legal-header p {
            font-size: 16px;
            color: var(--text-gray);
        }
        
        .content-section {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--beige-dark);
        }
        
        .content-section h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 20px 0 10px;
        }
        
        .content-section p {
            color: var(--text-gray);
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .content-section ul {
            margin-left: 30px;
            color: var(--text-gray);
            line-height: 1.8;
        }
        
        .content-section ul li {
            margin-bottom: 8px;
        }
        
        .content-section a {
            color: var(--beige-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .content-section a:hover {
            color: var(--accent-pink);
        }
        
        .highlight-box {
            background: var(--bg-light);
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        
        .highlight-box p {
            margin-bottom: 10px;
        }
        
        .highlight-box p:last-child {
            margin-bottom: 0;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            padding: 14px 32px;
            background: var(--beige-dark);
            color: var(--bg-white);
            text-decoration: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: var(--text-dark);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .legal-container {
                padding: 0 16px;
                margin: 20px auto;
            }

            .legal-header {
                padding: 40px 20px;
            }

            .legal-header h1 {
                font-size: 32px;
            }

            .content-section {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'templates/navbar_updated.php'; ?>
    
    <div class="legal-container">
        <section class="legal-header">
            <h1>Mentions Légales</h1>
            <p>Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">1. Informations sur l'éditeur</h2>
            <div class="highlight-box">
                <p><strong>Raison sociale :</strong> NURAYA</p>
                <p><strong>Forme juridique :</strong> [À compléter]</p>
                <p><strong>Adresse :</strong> 123 Avenue Habib Bourguiba, Tunis, Tunisie</p>
                <p><strong>Téléphone :</strong> +216 71 234 567</p>
                <p><strong>Email :</strong> contact@nuraya.tn</p>
                <p><strong>Numéro d'immatriculation :</strong> [À compléter]</p>
            </div>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">2. Directeur de la publication</h2>
            <p>Le directeur de la publication du site est : [Nom du directeur]</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">3. Hébergement du site</h2>
            <p><strong>Hébergeur :</strong> [Nom de l'hébergeur]</p>
            <p><strong>Adresse :</strong> [Adresse de l'hébergeur]</p>
            <p><strong>Contact :</strong> [Contact de l'hébergeur]</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">4. Propriété intellectuelle</h2>
            <p>L'ensemble du contenu présent sur le site NURAYA, incluant, de façon non limitative, les graphismes, images, textes, vidéos, animations, sons, logos, gifs et icônes ainsi que leur mise en forme sont la propriété exclusive de la société à l'exception des marques, logos ou contenus appartenant à d'autres sociétés partenaires ou auteurs.</p>
            <p>Toute reproduction, distribution, modification, adaptation, retransmission ou publication, même partielle, de ces différents éléments est strictement interdite sans l'accord exprès par écrit de NURAYA.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">5. Données personnelles</h2>
            <p>NURAYA s'engage à respecter la confidentialité des données personnelles communiquées par les utilisateurs de son site. Pour plus d'informations, veuillez consulter notre <a href="privacy.php">Politique de Confidentialité</a>.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">6. Cookies</h2>
            <p>Le site utilise des cookies pour améliorer l'expérience utilisateur. Pour en savoir plus, consultez notre <a href="cookies.php">Politique de Cookies</a>.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">7. Limitation de responsabilité</h2>
            <p>NURAYA ne pourra être tenue responsable des dommages directs et indirects causés au matériel de l'utilisateur, lors de l'accès au site, et résultant soit de l'utilisation d'un matériel ne répondant pas aux spécifications indiquées, soit de l'apparition d'un bug ou d'une incompatibilité.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">8. Droit applicable</h2>
            <p>Les présentes mentions légales sont régies par le droit tunisien. En cas de litige et à défaut d'accord amiable, le litige sera porté devant les tribunaux tunisiens conformément aux règles de compétence en vigueur.</p>
        </section>
        
        <div style="text-align: center;">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Retour à l'accueil
            </a>
        </div>
    </div>
    
    <?php include 'templates/footer.php'; ?>
</body>
</html>
