<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification du Système de Notation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-section h2 {
            color: #333;
            border-bottom: 2px solid #C8B6A6;
            padding-bottom: 10px;
        }
        .success {
            color: #4CAF50;
            padding: 10px;
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            margin: 10px 0;
        }
        .error {
            color: #f44336;
            padding: 10px;
            background: #ffebee;
            border-left: 4px solid #f44336;
            margin: 10px 0;
        }
        .info {
            color: #2196F3;
            padding: 10px;
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            margin: 10px 0;
        }
        .stars {
            font-size: 24px;
            color: #ffc107;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #C8B6A6;
            color: white;
        }
        .btn {
            background: #C8B6A6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        .btn:hover {
            background: #b5a395;
        }
    </style>
</head>
<body>
    <h1><i class="fas fa-star"></i> Vérification du Système de Notation</h1>
    
    <div class="test-section">
        <h2><i class="fas fa-database"></i> Test 1: Structure de la Base de Données</h2>
        <?php
        include 'config/database.php';
        
        $tables_to_check = ['reviews', 'review_helpful', 'review_reports', 'products'];
        $all_ok = true;
        
        foreach ($tables_to_check as $table) {
            $query = "SHOW TABLES LIKE '$table'";
            $result = mysqli_query($cnx, $query);
            if (mysqli_num_rows($result) > 0) {
                echo "<div class='success'><i class='fas fa-check-circle'></i> Table '$table' existe</div>";
            } else {
                echo "<div class='error'><i class='fas fa-times-circle'></i> Table '$table' n'existe PAS</div>";
                $all_ok = false;
            }
        }
        
        // Check specific columns
        $columns_check = [
            'reviews' => ['is_approved', 'rating', 'product_id', 'user_id'],
            'products' => ['is_active']
        ];
        
        foreach ($columns_check as $table => $columns) {
            foreach ($columns as $column) {
                $query = "SHOW COLUMNS FROM $table LIKE '$column'";
                $result = mysqli_query($cnx, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo "<div class='success'><i class='fas fa-check'></i> Colonne '$table.$column' existe</div>";
                } else {
                    echo "<div class='error'><i class='fas fa-times'></i> Colonne '$table.$column' n'existe PAS</div>";
                    $all_ok = false;
                }
            }
        }
        ?>
    </div>

    <div class="test-section">
        <h2><i class="fas fa-comments"></i> Test 2: Données de Test</h2>
        <?php
        $query = "SELECT p.product_id, p.name, COUNT(r.id) as review_count, AVG(r.rating) as avg_rating
                  FROM products p
                  LEFT JOIN reviews r ON p.product_id = r.product_id AND r.is_approved = 1
                  WHERE p.is_active = 1
                  GROUP BY p.product_id
                  ORDER BY review_count DESC
                  LIMIT 5";
        
        $result = mysqli_query($cnx, $query);
        
        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr><th>ID Produit</th><th>Nom</th><th>Nb Avis</th><th>Note Moyenne</th><th>Étoiles</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                $avg = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= $i <= $avg ? '★' : '☆';
                }
                
                echo "<tr>";
                echo "<td>{$row['product_id']}</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>{$row['review_count']}</td>";
                echo "<td>{$avg}/5</td>";
                echo "<td class='stars'>{$stars}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<div class='info'><i class='fas fa-info-circle'></i> " . mysqli_num_rows($result) . " produit(s) trouvé(s)</div>";
        } else {
            echo "<div class='error'><i class='fas fa-exclamation-triangle'></i> Aucun produit trouvé</div>";
        }
        ?>
    </div>

    <div class="test-section">
        <h2><i class="fas fa-code"></i> Test 3: API Reviews</h2>
        <button class="btn" onclick="testAPI()"><i class="fas fa-play"></i> Tester l'API</button>
        <div id="api-result"></div>
    </div>

    <div class="test-section">
        <h2><i class="fas fa-file-code"></i> Test 4: Fichiers JavaScript</h2>
        <?php
        $js_files = [
            'assets/js/toast.js',
            'assets/js/cart-count.js',
            'templates/reviews_section.php'
        ];
        
        foreach ($js_files as $file) {
            if (file_exists($file)) {
                $size = filesize($file);
                echo "<div class='success'><i class='fas fa-check-circle'></i> $file existe ($size octets)</div>";
            } else {
                echo "<div class='error'><i class='fas fa-times-circle'></i> $file n'existe PAS</div>";
            }
        }
        
        // Check if functions exist in files
        $product_content = file_get_contents('product.php');
        if (strpos($product_content, 'function showTab') !== false) {
            echo "<div class='success'><i class='fas fa-check'></i> Fonction 'showTab' trouvée dans product.php</div>";
        }
        if (strpos($product_content, 'toast.js') !== false) {
            echo "<div class='success'><i class='fas fa-check'></i> Script 'toast.js' inclus dans product.php</div>";
        }
        
        $reviews_content = file_get_contents('templates/reviews_section.php');
        if (strpos($reviews_content, 'function updateRatingDisplay') !== false) {
            echo "<div class='success'><i class='fas fa-check'></i> Fonction 'updateRatingDisplay' trouvée dans reviews_section.php</div>";
        } else {
            echo "<div class='error'><i class='fas fa-times'></i> Fonction 'updateRatingDisplay' NON trouvée dans reviews_section.php</div>";
        }
        ?>
    </div>

    <div class="test-section">
        <h2><i class="fas fa-link"></i> Test 5: Liens de Test</h2>
        <?php
        // Find a product with reviews
        $query = "SELECT p.product_id FROM products p 
                  INNER JOIN reviews r ON p.product_id = r.product_id 
                  WHERE p.is_active = 1 AND r.is_approved = 1 
                  LIMIT 1";
        $result = mysqli_query($cnx, $query);
        $product = mysqli_fetch_assoc($result);
        
        if ($product) {
            $product_id = $product['product_id'];
            echo "<div class='info'>";
            echo "<p><strong>Produit avec avis trouvé: ID $product_id</strong></p>";
            echo "<p><a href='product.php?id=$product_id' target='_blank' class='btn'>";
            echo "<i class='fas fa-external-link-alt'></i> Voir le produit</a></p>";
            echo "</div>";
        } else {
            echo "<div class='error'><i class='fas fa-exclamation-triangle'></i> Aucun produit avec avis approuvés trouvé</div>";
        }
        ?>
    </div>

    <div class="test-section">
        <h2><i class="fas fa-check-double"></i> Résumé</h2>
        <div id="summary">
            <?php
            $total_reviews = mysqli_fetch_assoc(mysqli_query($cnx, "SELECT COUNT(*) as count FROM reviews"))['count'];
            $approved_reviews = mysqli_fetch_assoc(mysqli_query($cnx, "SELECT COUNT(*) as count FROM reviews WHERE is_approved = 1"))['count'];
            $pending_reviews = $total_reviews - $approved_reviews;
            
            echo "<p><strong>Total des avis:</strong> $total_reviews</p>";
            echo "<p><strong>Avis approuvés:</strong> $approved_reviews</p>";
            echo "<p><strong>Avis en attente:</strong> $pending_reviews</p>";
            
            if ($approved_reviews > 0) {
                echo "<div class='success'><i class='fas fa-thumbs-up'></i> Le système de notation est opérationnel!</div>";
            } else {
                echo "<div class='info'><i class='fas fa-info-circle'></i> Système prêt mais aucun avis approuvé. Créez des avis de test.</div>";
                echo "<button class='btn' onclick='createTestReviews()'><i class='fas fa-plus'></i> Créer des Avis de Test</button>";
            }
            ?>
        </div>
    </div>

    <script>
        function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<div class="info"><i class="fas fa-spinner fa-spin"></i> Test en cours...</div>';
            
            // Get a product ID with reviews
            fetch('api/reviews.php?action=get&product_id=2&page=1')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '<div class="success"><i class="fas fa-check-circle"></i> API fonctionne correctement!</div>';
                        html += '<table>';
                        html += '<tr><th>Propriété</th><th>Valeur</th></tr>';
                        html += `<tr><td>Note moyenne</td><td>${data.stats.avg_rating}/5</td></tr>`;
                        html += `<tr><td>Total avis</td><td>${data.stats.total_reviews}</td></tr>`;
                        html += `<tr><td>Avis retournés</td><td>${data.reviews.length}</td></tr>`;
                        html += `<tr><td>Page courante</td><td>${data.pagination.current_page}</td></tr>`;
                        html += '</table>';
                        
                        if (data.reviews.length > 0) {
                            html += '<h4>Exemple d\'avis:</h4>';
                            const review = data.reviews[0];
                            html += `<p><strong>${review.author}</strong> - ${review.rating}/5</p>`;
                            html += `<p>${review.comment}</p>`;
                        }
                        
                        resultDiv.innerHTML = html;
                    } else {
                        resultDiv.innerHTML = `<div class="error"><i class="fas fa-times-circle"></i> Erreur API: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = `<div class="error"><i class="fas fa-times-circle"></i> Erreur: ${error.message}</div>`;
                });
        }
        
        function createTestReviews() {
            if (confirm('Créer 3 avis de test pour le produit #2?')) {
                fetch('create_test_reviews.php')
                    .then(response => response.text())
                    .then(data => {
                        alert('Avis créés! Rechargez la page.');
                        location.reload();
                    });
            }
        }
    </script>
</body>
</html>
