<!DOCTYPE html>
<html>
<head>
    <title>Open Product Page Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .btn { 
            display: inline-block;
            background: #C8B6A6; 
            color: white; 
            padding: 15px 30px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 10px 5px;
            font-size: 16px;
        }
        .btn:hover { background: #b5a395; }
        .section { 
            background: white; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .issue { background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 10px 0; }
        .success { background: #e8f5e9; border-left: 4px solid #4CAF50; padding: 15px; margin: 10px 0; }
        h2 { color: #333; boreder-bottom: 2px solid #C8B6A6; padding-bottom: 10px; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Test du Système de Notation - Aide au Diagnostic</h1>
    
    <div class="section">
        <h2>🎯 Pages de Test Disponibles</h2>
        <p>Cliquez sur ces liens pour tester différentes parties du système:</p>
        
        <a href="product.php?id=2" class="btn" target="_blank">
            📦 Page Produit #2
        </a>
        
        <a href="diagnose_rating.php" class="btn" target="_blank">
            🔍 Page Diagnostic Complète
        </a>
        
        <a href="verify_rating_system.php" class="btn" target="_blank">
            ✅ Vérification Système
        </a>
        
        <a href="test_rating_system.php" class="btn" target="_blank">
            🧪 Tests Unitaires
        </a>
    </div>

    <div class="section">
        <h2>❓ Comment identifier le problème</h2>
        
        <h3>Étape 1: Ouvrez la page produit</h3>
        <p>Cliquez sur <strong>"📦 Page Produit #2"</strong> ci-dessus</p>
        
        <h3>Étape 2: Que voyez-vous?</h3>
        <div class="issue">
            <strong>Option A:</strong> Les étoiles ne s'affichent PAS DU TOUT
            <p>➡️ Problème: CSS ou HTML cassé</p>
        </div>
        
        <div class="issue">
            <strong>Option B:</strong> Les étoiles sont TOUTES GRISES (☆☆☆☆☆)
            <p>➡️ Problème: $avg_rating = 0 ou classe CSS 'filled' ne fonctionne pas</p>
        </div>
        
        <div class="issue">
            <strong>Option C:</strong> Les étoiles sont correctes MAIS affichent "0/5 (0 avis)"
            <p>➡️ Problème: Les avis ne sont pas approuvés ou $total_reviews = 0</p>
        </div>
        
        <div class="issue">
            <strong>Option D:</strong> Tout s'affiche MAIS cliquer ne fait rien
            <p>➡️ Problème: JavaScript showTab() ne fonctionne pas</p>
        </div>
        
        <div class="success">
            <strong>Option E:</strong> Tout fonctionne correctement!
            <p>✅ Le système marche!</p>
        </div>
        
        <h3>Étape 3: Ouvrez la Console JavaScript</h3>
        <p>Sur la page produit:</p>
        <ol>
            <li>Appuyez sur <strong>F12</strong></li>
            <li>Cliquez sur l'onglet <strong>"Console"</strong></li>
            <li>Regardez s'il y a des erreurs en ROUGE</li>
            <li>Copiez le message d'erreur si vous en voyez un</li>
        </ol>
    </div>

    <div class="section">
        <h2>🔧 Tests Rapides PHP</h2>
        <?php
        include 'config/database.php';
        
        echo "<h3>Base de données:</h3>";
        
        // Check reviews count
        $count_query = "SELECT COUNT(*) as total FROM reviews WHERE is_approved = 1";
        $count_result = mysqli_query($cnx, $count_query);
        $count = mysqli_fetch_assoc($count_result)['total'];
        
        if ($count > 0) {
            echo "<div class='success'>✅ $count avis approuvés trouvés</div>";
        } else {
            echo "<div class='issue'>❌ AUCUN avis approuvé! C'est peut-être ça le problème.</div>";
            echo "<p>Exécutez: <code>php create_test_reviews.php</code></p>";
        }
        
        // Check specific product
        $product_query = "SELECT 
            p.product_id, 
            p.name,
            COUNT(r.id) as review_count,
            AVG(r.rating) as avg_rating
            FROM products p
            LEFT JOIN reviews r ON p.product_id = r.product_id AND r.is_approved = 1
            WHERE p.product_id = 2
            GROUP BY p.product_id";
        
        $product_result = mysqli_query($cnx, $product_query);
        $product_data = mysqli_fetch_assoc($product_result);
        
        if ($product_data) {
            echo "<h3>Produit #2:</h3>";
            echo "<p><strong>Nom:</strong> " . htmlspecialchars($product_data['name']) . "</p>";
            echo "<p><strong>Nombre d'avis:</strong> " . $product_data['review_count'] . "</p>";
            echo "<p><strong>Note moyenne:</strong> " . round($product_data['avg_rating'], 1) . "/5</p>";
            
            if ($product_data['review_count'] > 0) {
                echo "<div class='success'>✅ Ce produit a des avis, le rating devrait s'afficher!</div>";
            } else {
                echo "<div class='issue'>⚠️ Ce produit n'a PAS d'avis approuvés</div>";
            }
        }
        
        mysqli_close($cnx);
        ?>
    </div>

    <div class="section">
        <h2>📝 Instructions Détaillées</h2>
        <p><strong>Si vous voyez "0/5 (0 avis)" sur la page produit:</strong></p>
        <ol>
            <li>Les avis ne sont pas approuvés OUDANS la base de données</li>
            <li>Exécutez: <code>php create_test_reviews.php</code></li>
            <li>Rechargez la page produit</li>
        </ol>
        
        <p><strong>Si les étoiles sont toutes grises:</strong></p>
        <ol>
            <li>Vérifiez le CSS de la classe <code>.star.filled</code></li>
            <li>Ouvrez la console F12 → onglet "Elements"</li>
            <li>Inspectez les éléments <code>&lt;span class="star"&gt;</code></li>
            <li>Vérifiez si la classe "filled" est bien présente</li>
        </ol>
        
        <p><strong>Si cliquer ne scrolle pas vers les avis:</strong></p>
        <ol>
            <li>Ouvrez F12 → Console</li>
            <li>Cherchez des erreurs JavaScript en rouge</li>
            <li>Vérifiez si <code>showTab</code> est définie: tapez dans la console: <code>typeof showTab</code></li>
            <li>Ça devrait afficher "function"</li>
        </ol>
    </div>

</body>
</html>
