<?php
session_start();
require_once 'config/database.php';
require_once 'config/settings.php';

$page_title = "Politique de Confidentialité - NURAYA";
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
            <h1>Politique de Confidentialité</h1>
            <p>Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">1. Introduction</h2>
            <p>NURAYA s'engage à protéger la vie privée de ses utilisateurs. Cette politique de confidentialité explique quelles données personnelles nous collectons, comment nous les utilisons et quels sont vos droits.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">2. Données collectées</h2>
            <h3>2.1 Données que vous nous fournissez directement</h3>
            <p>Lors de la création de votre compte ou de vos achats, nous collectons :</p>
            <ul>
                <li>Nom et prénom</li>
                <li>Adresse email</li>
                <li>Numéro de téléphone</li>
                <li>Adresse de livraison et de facturation</li>
                <li>Informations de paiement (cryptées)</li>
            </ul>
            
            <h3>2.2 Données collectées automatiquement</h3>
            <ul>
                <li>Adresse IP</li>
                <li>Type de navigateur et système d'exploitation</li>
                <li>Pages visitées et durée de visite</li>
                <li>Données de cookies (voir notre Politique de Cookies)</li>
            </ul>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">3. Utilisation des données</h2>
            <p>Nous utilisons vos données personnelles pour :</p>
            <ul>
                <li>Traiter et gérer vos commandes</li>
                <li>Créer et gérer votre compte client</li>
                <li>Communiquer avec vous concernant vos commandes</li>
                <li>Améliorer notre site et nos services</li>
                <li>Vous envoyer des offres promotionnelles (avec votre consentement)</li>
                <li>Assurer la sécurité de notre plateforme</li>
                <li>Respecter nos obligations légales</li>
            </ul>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">4. Partage des données</h2>
            <p>Nous ne vendons jamais vos données personnelles. Nous pouvons les partager uniquement avec :</p>
            <ul>
                <li><strong>Prestataires de services :</strong> Transporteurs, processeurs de paiement</li>
                <li><strong>Autorités légales :</strong> Si requis par la loi</li>
                <li><strong>Partenaires commerciaux :</strong> Uniquement avec votre consentement explicite</li>
            </ul>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">5. Sécurité des données</h2>
            <div class="highlight-box">
                <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos données contre tout accès non autorisé, modification, divulgation ou destruction.</p>
                <p>Les informations de paiement sont cryptées et traitées via des plateformes sécurisées conformes aux normes PCI-DSS.</p>
            </div>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">6. Conservation des données</h2>
            <p>Nous conservons vos données personnelles aussi longtemps que nécessaire pour :</p>
            <ul>
                <li>Fournir nos services</li>
                <li>Respecter nos obligations légales et fiscales</li>
                <li>Résoudre les litiges</li>
                <li>Faire respecter nos accords</li>
            </ul>
            <p>En général, nous conservons les données de compte pendant 3 ans après la dernière activité, et les données de commande pendant 10 ans conformément aux obligations fiscales.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">7. Vos droits</h2>
            <p>Conformément à la législation en vigueur, vous disposez des droits suivants :</p>
            <ul>
                <li><strong>Droit d'accès :</strong> Obtenir une copie de vos données personnelles</li>
                <li><strong>Droit de rectification :</strong> Corriger vos données inexactes</li>
                <li><strong>Droit à l'effacement :</strong> Supprimer vos données dans certaines conditions</li>
                <li><strong>Droit d'opposition :</strong> Vous opposer au traitement de vos données</li>
                <li><strong>Droit à la portabilité :</strong> Recevoir vos données dans un format structuré</li>
                <li><strong>Droit de retirer votre consentement :</strong> À tout moment</li>
            </ul>
            <p>Pour exercer ces droits, contactez-nous à : <a href="mailto:privacy@nuraya.tn">privacy@nuraya.tn</a></p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">8. Cookies</h2>
            <p>Notre site utilise des cookies pour améliorer votre expérience. Pour plus d'informations, consultez notre <a href="cookies.php">Politique de Cookies</a>.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">9. Modifications de cette politique</h2>
            <p>Nous pouvons mettre à jour cette politique de confidentialité. Nous vous informerons de tout changement significatif par email ou via une notification sur notre site.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">10. Contact</h2>
            <p>Pour toute question concernant cette politique de confidentialité, contactez-nous :</p>
            <div class="highlight-box">
                <p><strong>Email :</strong> privacy@nuraya.tn</p>
                <p><strong>Téléphone :</strong> +216 71 234 567</p>
                <p><strong>Adresse :</strong> 123 Avenue Habib Bourguiba, Tunis, Tunisie</p>
            </div>
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
