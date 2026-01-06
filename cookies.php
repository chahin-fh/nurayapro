<?php
session_start();
require_once 'config/database.php';
require_once 'config/settings.php';

$page_title = "Politique de Cookies - NURAYA";
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
        
        .cookie-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: var(--bg-light);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .cookie-table th,
        .cookie-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--beige-dark);
        }
        
        .cookie-table th {
            background: var(--beige-dark);
            color: var(--bg-white);
            font-weight: 600;
        }
        
        .cookie-table td {
            color: var(--text-gray);
        }
        
        .cookie-table tr:last-child td {
            border-bottom: none;
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
            
            .cookie-table {
                font-size: 14px;
            }
            
            .cookie-table th,
            .cookie-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include 'templates/navbar_updated.php'; ?>
    
    <div class="legal-container">
        <section class="legal-header">
            <h1>Politique de Cookies</h1>
            <p>Dernière mise à jour : <?php echo date('d/m/Y'); ?></p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">1. Qu'est-ce qu'un cookie ?</h2>
            <p>Un cookie est un petit fichier texte déposé sur votre terminal (ordinateur, smartphone, tablette) lors de la visite d'un site internet. Il permet au site de mémoriser des informations sur votre visite, comme votre langue de préférence et d'autres paramètres, afin de faciliter votre prochaine visite et de rendre le site plus utile.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">2. Types de cookies utilisés</h2>
            
            <h3>2.1 Cookies strictement nécessaires</h3>
            <div class="highlight-box">
                <p>Ces cookies sont essentiels au fonctionnement du site. Ils vous permettent de naviguer sur le site et d'utiliser ses fonctionnalités.</p>
            </div>
            
            <table class="cookie-table">
                <thead>
                    <tr>
                        <th>Nom du cookie</th>
                        <th>Finalité</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PHPSESSID</td>
                        <td>Gestion de la session utilisateur</td>
                        <td>Session</td>
                    </tr>
                    <tr>
                        <td>cart_token</td>
                        <td>Mémorisation du panier d'achat</td>
                        <td>30 jours</td>
                    </tr>
                    <tr>
                        <td>user_auth</td>
                        <td>Authentification de l'utilisateur</td>
                        <td>14 jours</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>2.2 Cookies de performance</h3>
            <p>Ces cookies collectent des informations sur la façon dont les visiteurs utilisent notre site, par exemple les pages les plus consultées. Ces données nous aident à améliorer le fonctionnement du site.</p>
            
            <table class="cookie-table">
                <thead>
                    <tr>
                        <th>Nom du cookie</th>
                        <th>Finalité</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>_ga</td>
                        <td>Google Analytics - Analyse d'audience</td>
                        <td>2 ans</td>
                    </tr>
                    <tr>
                        <td>_gid</td>
                        <td>Google Analytics - Identification utilisateur</td>
                        <td>24 heures</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>2.3 Cookies de fonctionnalité</h3>
            <p>Ces cookies permettent au site de se souvenir des choix que vous avez effectués (comme votre nom d'utilisateur, langue ou région) et fournissent des fonctionnalités améliorées et plus personnelles.</p>
            
            <table class="cookie-table">
                <thead>
                    <tr>
                        <th>Nom du cookie</th>
                        <th>Finalité</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>user_preferences</td>
                        <td>Sauvegarde des préférences utilisateur</td>
                        <td>1 an</td>
                    </tr>
                    <tr>
                        <td>wishlist</td>
                        <td>Mémorisation de la liste de souhaits</td>
                        <td>90 jours</td>
                    </tr>
                </tbody>
            </table>
            
            <h3>2.4 Cookies publicitaires</h3>
            <p>Ces cookies sont utilisés pour diffuser des publicités plus pertinentes pour vous et vos centres d'intérêt.</p>
            <div class="highlight-box">
                <p><strong>Note :</strong> NURAYA n'utilise actuellement pas de cookies publicitaires tiers.</p>
            </div>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">3. Gestion des cookies</h2>
            
            <h3>3.1 Via votre navigateur</h3>
            <p>Vous pouvez configurer votre navigateur pour accepter ou refuser les cookies :</p>
            <ul>
                <li><strong>Google Chrome :</strong> Paramètres > Confidentialité et sécurité > Cookies</li>
                <li><strong>Firefox :</strong> Options > Vie privée et sécurité > Cookies et données de sites</li>
                <li><strong>Safari :</strong> Préférences > Confidentialité > Cookies et données de sites web</li>
                <li><strong>Edge :</strong> Paramètres > Cookies et autorisations de site > Cookies</li>
            </ul>
            
            <h3>3.2 Conséquences du refus des cookies</h3>
            <p>Le refus de certains cookies peut entraîner :</p>
            <ul>
                <li>L'impossibilité de mémoriser votre panier</li>
                <li>La nécessité de vous reconnecter à chaque visite</li>
                <li>Une expérience utilisateur dégradée</li>
                <li>L'impossibilité d'accéder à certaines fonctionnalités</li>
            </ul>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">4. Cookies tiers</h2>
            <p>Certains cookies sont déposés par des services tiers avec lesquels nous travaillons :</p>
            <ul>
                <li><strong>Google Analytics :</strong> Pour l'analyse d'audience</li>
                <li><strong>Réseaux sociaux :</strong> Si vous partagez du contenu via les boutons sociaux</li>
            </ul>
            <p>Ces cookies tiers sont soumis aux politiques de confidentialité respectives de ces services.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">5. Durée de conservation</h2>
            <p>Les cookies ont des durées de conservation variables :</p>
            <ul>
                <li><strong>Cookies de session :</strong> Supprimés à la fermeture du navigateur</li>
                <li><strong>Cookies persistants :</strong> Conservés selon les durées indiquées dans les tableaux ci-dessus</li>
            </ul>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">6. Mise à jour de la politique</h2>
            <p>Cette politique de cookies peut être mise à jour. Nous vous encourageons à la consulter régulièrement pour rester informé de notre utilisation des cookies.</p>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">7. Contact</h2>
            <p>Pour toute question concernant notre utilisation des cookies, contactez-nous :</p>
            <div class="highlight-box">
                <p><strong>Email :</strong> privacy@nuraya.tn</p>
                <p><strong>Téléphone :</strong> +216 71 234 567</p>
                <p><strong>Adresse :</strong> 123 Avenue Habib Bourguiba, Tunis, Tunisie</p>
            </div>
        </section>
        
        <section class="content-section">
            <h2 class="section-title">8. Pour en savoir plus</h2>
            <p>Pour plus d'informations sur les cookies et la protection de vos données personnelles :</p>
            <ul>
                <li><a href="privacy.php">Notre Politique de Confidentialité</a></li>
                <li><a href="https://www.cnil.fr/fr/cookies-les-outils-pour-les-maitriser" target="_blank" rel="noopener noreferrer">CNIL - Les cookies</a></li>
            </ul>
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
