<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Rating System - Nuraya</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .step {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .step h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            margin-right: 10px;
            font-weight: bold;
        }

        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 500;
        }

        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-block;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .feature {
            background: white;
            border: 2px solid #e9ecef;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .feature i {
            font-size: 30px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .icon {
            margin-right: 8px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-star icon"></i>Rating System Setup</h1>
            <p>Configuration du système de notation et d'avis</p>
        </div>

        <div class="content">
            <?php
            require_once 'config/database.php';

            // Check if setup is already done
            $checkQuery = "SHOW COLUMNS FROM reviews LIKE 'helpful_count'";
            $checkResult = mysqli_query($cnx, $checkQuery);
            $alreadySetup = mysqli_num_rows($checkResult) > 0;

            if (isset($_POST['run_setup']) && !$alreadySetup) {
                echo '<div class="status info"><i class="fas fa-cog fa-spin icon"></i>Installation en cours...</div>';

                $errors = [];
                $success = [];

                // Read SQL file
                $sqlFile = file_get_contents('database_improvements.sql');
                
                // Split into individual queries
                $queries = array_filter(array_map('trim', explode(';', $sqlFile)));

                foreach ($queries as $query) {
                    // Skip comments and DELIMITER statements
                    if (empty($query) || strpos($query, '--') === 0 || strpos($query, 'DELIMITER') !== false) {
                        continue;
                    }

                    if (mysqli_query($cnx, $query)) {
                        $success[] = "✓ Requête exécutée avec succès";
                    } else {
                        $error = mysqli_error($cnx);
                        // Skip errors for triggers if they already exist
                        if (strpos($error, 'already exists') === false) {
                            $errors[] = "✗ Erreur: " . substr($error, 0, 100);
                        }
                    }
                }

                if (count($errors) == 0) {
                    echo '<div class="status success">';
                    echo '<h3><i class="fas fa-check-circle icon"></i>Installation réussie!</h3>';
                    echo '<p>Le système de notation a été configuré avec succès.</p>';
                    echo '<p>Nombre de requêtes exécutées: ' . count($success) . '</p>';
                    echo '</div>';
                } else {
                    echo '<div class="status error">';
                    echo '<h3><i class="fas fa-exclamation-triangle icon"></i>Erreurs détectées</h3>';
                    foreach ($errors as $error) {
                        echo '<p>' . $error . '</p>';
                    }
                    echo '</div>';
                }

                // Verify installation
                $verifyQueries = [
                    "SHOW COLUMNS FROM reviews LIKE 'helpful_count'" => "Colonne helpful_count",
                    "SHOW TRIGGERS LIKE 'review_helpful_insert'" => "Trigger review_helpful_insert",
                    "SHOW TRIGGERS LIKE 'review_helpful_delete'" => "Trigger review_helpful_delete"
                ];

                echo '<div class="status info">';
                echo '<h3><i class="fas fa-clipboard-check icon"></i>Vérification</h3>';
                foreach ($verifyQueries as $query => $desc) {
                    $result = mysqli_query($cnx, $query);
                    if (mysqli_num_rows($result) > 0) {
                        echo '<p>✓ ' . $desc . ' - OK</p>';
                    } else {
                        echo '<p>✗ ' . $desc . ' - NON TROUVÉ</p>';
                    }
                }
                echo '</div>';

                echo '<p style="margin-top: 20px; text-align: center;">';
                echo '<a href="product.php?id=2" class="btn"><i class="fas fa-eye icon"></i>Voir un produit pour tester</a>';
                echo '</p>';
            } else {
                if ($alreadySetup) {
                    echo '<div class="status success">';
                    echo '<h3><i class="fas fa-check-circle icon"></i>Système déjà configuré</h3>';
                    echo '<p>Le système de notation est déjà installé et configuré.</p>';
                    echo '</div>';
                } else {
                    ?>
                    <div class="status info">
                        <h3><i class="fas fa-info-circle icon"></i>Prêt pour l'installation</h3>
                        <p>Cliquez sur le bouton ci-dessous pour installer les améliorations de la base de données.</p>
                    </div>

                    <div class="step">
                        <h3><span class="step-number">1</span>Nouvelles fonctionnalités</h3>
                        <div class="features">
                            <div class="feature">
                                <i class="fas fa-thumbs-up"></i>
                                <p>Votes utiles</p>
                            </div>
                            <div class="feature">
                                <i class="fas fa-chart-bar"></i>
                                <p>Statistiques en temps réel</p>
                            </div>
                            <div class="feature">
                                <i class="fas fa-tachometer-alt"></i>
                                <p>Performance optimisée</p>
                            </div>
                            <div class="feature">
                                <i class="fas fa-sort-amount-down"></i>
                                <p>Tri intelligent</p>
                            </div>
                        </div>
                    </div>

                    <div class="step">
                        <h3><span class="step-number">2</span>Modifications de la base de données</h3>
                        <ul>
                            <li>✓ Ajout de la colonne <code>helpful_count</code></li>
                            <li>✓ Création d'index pour meilleures performances</li>
                            <li>✓ Triggers pour mise à jour automatique</li>
                            <li>✓ Mise à jour des données existantes</li>
                        </ul>
                    </div>

                    <div class="step">
                        <h3><span class="step-number">3</span>Installation</h3>
                        <p>L'installation va:</p>
                        <ul style="margin: 10px 0 20px 20px;">
                            <li>Analyser le fichier SQL</li>
                            <li>Exécuter les requêtes</li>
                            <li>Vérifier l'installation</li>
                            <li>Afficher le résultat</li>
                        </ul>
                        <form method="post" style="text-align: center;">
                            <button type="submit" name="run_setup" class="btn">
                                <i class="fas fa-rocket icon"></i>Lancer l'installation
                            </button>
                        </form>
                    </div>

                    <div class="status info" style="margin-top: 30px;">
                        <h3><i class="fas fa-book icon"></i>Documentation</h3>
                        <p>Consultez le fichier <code>RATING_SYSTEM_DOCUMENTATION.md</code> pour la documentation complète.</p>
                    </div>
                    <?php
                }
            }

            mysqli_close($cnx);
            ?>
        </div>
    </div>
</body>
</html>
