<?php
session_start();
require_once 'config/database.php';
require_once 'config/settings.php';

$page_title = "Conditions Générales de Vente - NURAYA";
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
            <h1>Conditions Générales de Vente</h1>
            <p>Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">1. Objet</h2>
            <p>Les présentes Conditions Générales de Vente (CGV) régissent les ventes de produits par NURAYA via son site internet. Toute commande implique l'acceptation sans réserve des présentes CGV.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">2. Produits</h2>
            <h3>2.1 Description</h3>
            <p>Les produits proposés sont ceux qui figurent sur le site NURAYA. Les photographies et descriptions sont aussi précises que possible mais ne peuvent assurer une similitude parfaite avec le produit, notamment en ce qui concerne les couleurs.</p>
            
            <h3>2.2 Disponibilité</h3>
            <p>Nos offres de produits et prix sont valables tant qu'elles sont visibles sur le site, dans la limite des stocks disponibles. En cas d'indisponibilité d'un produit après passation de votre commande, nous vous en informerons par email et vous proposerons un remboursement.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">3. Prix</h2>
            <div class="highlight-box">
                <p>Les prix sont indiqués en Dinars Tunisiens (TND).</p>
                <p>NURAYA se réserve le droit de modifier ses prix à tout moment mais les produits seront facturés sur la base des tarifs en vigueur au moment de la validation de la commande.</p>
            </div>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">4. Commande</h2>
            <h3>4.1 Processus de commande</h3>
            <p>Pour commander, vous devez :</p>
            <ul>
                <li>Créer un compte ou vous connecter</li>
                <li>Sélectionner les produits et les ajouter au panier</li>
                <li>Vérifier le contenu de votre panier</li>
                <li>Renseigner vos informations de livraison</li>
                <li>Choisir le mode de paiement</li>
                <li>Confirmer votre commande</li>
            </ul>
            
            <h3>4.2 Confirmation de commande</h3>
            <p>Une fois votre commande validée, vous recevrez un email de confirmation récapitulant votre commande.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">5. Paiement</h2>
            <h3>5.1 Modes de paiement acceptés</h3>
            <ul>
                <li>Carte bancaire (Visa, Mastercard)</li>
                <li>Paiement en ligne sécurisé</li>
                <li>Paiement à la livraison (sous conditions)</li>
            </ul>
            
            <h3>5.2 Sécurité</h3>
            <p>Tous les paiements en ligne sont sécurisés et cryptés. Nous ne conservons aucune information bancaire sur nos serveurs.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">6. Livraison</h2>
            <h3>6.1 Zones de livraison</h3>
            <p>Nous livrons actuellement sur tout le territoire tunisien.</p>
            
            <h3>6.2 Délais de livraison</h3>
            <ul>
                <li><strong>Tunis et environs :</strong> 2-3 jours ouvrables</li>
                <li><strong>Autres régions :</strong> 3-5 jours ouvrables</li>
            </ul>
            
            <h3>6.3 Frais de livraison</h3>
            <p>Les frais de livraison sont calculés en fonction de votre adresse et du poids de votre commande. Ils sont indiqués avant la validation de votre commande.</p>
            <div class="highlight-box">
                <p><strong>Livraison gratuite</strong> pour toute commande supérieure à 100 TND.</p>
            </div>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">7. Droit de rétractation</h2>
            <h3>7.1 Délai</h3>
            <p>Conformément à la législation en vigueur, vous disposez d'un délai de <strong>14 jours</strong> à compter de la réception de votre commande pour exercer votre droit de rétractation sans avoir à justifier de motifs.</p>
            
            <h3>7.2 Conditions</h3>
            <p>Les produits doivent être retournés :</p>
            <ul>
                <li>Dans leur emballage d'origine</li>
                <li>En parfait état</li>
                <li>Non portés et non lavés</li>
                <li>Avec toutes les étiquettes attachées</li>
            </ul>
            
            <h3>7.3 Procédure</h3>
            <p>Pour exercer votre droit de rétractation, contactez notre service client à : <a href="mailto:retours@nuraya.tn">retours@nuraya.tn</a></p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">8. Remboursement</h2>
            <p>En cas de retour accepté, nous procéderons au remboursement dans un délai de 14 jours suivant la réception des produits retournés, en utilisant le même moyen de paiement que celui utilisé pour la commande.</p>
            <p>Les frais de retour restent à votre charge, sauf en cas de produit défectueux ou d'erreur de notre part.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">9. Garanties</h2>
            <h3>9.1 Garantie légale de conformité</h3>
            <p>Tous nos produits bénéficient de la garantie légale de conformité prévue par la loi.</p>
            
            <h3>9.2 Garantie des vices cachés</h3>
            <p>Vous bénéficiez également de la garantie légale contre les vices cachés.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">10. Service client</h2>
            <p>Notre service client est à votre disposition pour toute question :</p>
            <div class="highlight-box">
                <p><strong>Email :</strong> contact@nuraya.tn</p>
                <p><strong>Téléphone :</strong> +216 71 234 567</p>
                <p><strong>Horaires :</strong> Lundi - Vendredi : 9h00 - 18h00</p>
            </div>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">11. Données personnelles</h2>
            <p>Les données personnelles collectées lors de votre commande sont nécessaires au traitement de celle-ci. Pour plus d'informations, consultez notre <a href="privacy.php">Politique de Confidentialité</a>.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">12. Droit applicable et juridiction</h2>
            <p>Les présentes CGV sont soumises au droit tunisien. En cas de litige, une solution amiable sera recherchée avant toute action judiciaire. À défaut, les tribunaux tunisiens seront seuls compétents.</p>
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
