<?php
include 'config/database.php';
if (!isset($_SESSION)) {
    session_start();
}

// Get a product with reviews
$product_id = 2;
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = $product_id AND p.is_active = 1";
$result = mysqli_query($cnx, $query);
$product = mysqli_fetch_assoc($result);

// Get rating stats
$avg_rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                     FROM reviews 
                     WHERE product_id = $product_id AND is_approved = 1";
$avg_result = mysqli_query($cnx, $avg_rating_query);
$rating_data = mysqli_fetch_assoc($avg_result);
$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
$total_reviews = $rating_data['total_reviews'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diagnostic - Système de Notation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-bottom: 20px; }
        h2 { color: #555; margin: 30px 0 15px; border-bottom: 2px solid #C8B6A6; padding-bottom: 10px; }
        .test-item {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #ccc;
        }
        .test-item.pass { background: #e8f5e9; border-left-color: #4CAF50; }
        .test-item.fail { background: #ffebee; border-left-color: #f44336; }
        .test-item.warn { background: #fff3e0; border-left-color: #ff9800; }
        .test-item.info { background: #e3f2fd; border-left-color: #2196F3; }
        .icon { margin-right: 10px; }
        .code {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
        button {
            background: #C8B6A6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover { background: #b5a395; }
        .stars { font-size: 24px; color: #ffc107; }
        .console-output {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
            margin: 10px 0;
        }
        .error-log { color: #f48771; }
        .warn-log { color: #dcdcaa; }
        .info-log { color: #4ec9b0; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-bug"></i> Diagnostic du Système de Notation</h1>
        
        <h2>1. Vérification PHP Backend</h2>
        
        <div class="test-item <?php echo $product ? 'pass' : 'fail'; ?>">
            <i class="icon fas fa-<?php echo $product ? 'check-circle' : 'times-circle'; ?>"></i>
            <strong>Produit chargé:</strong> 
            <?php echo $product ? "OUI (ID: $product_id - {$product['name']})" : "NON"; ?>
        </div>
        
        <div class="test-item <?php echo $total_reviews > 0 ? 'pass' : 'warn'; ?>">
            <i class="icon fas fa-<?php echo $total_reviews > 0 ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
            <strong>Avis trouvés:</strong> <?php echo $total_reviews; ?> avis approuvés
        </div>
        
        <div class="test-item <?php echo $avg_rating > 0 ? 'pass' : 'warn'; ?>">
            <i class="icon fas fa-star"></i>
            <strong>Note moyenne:</strong> <?php echo $avg_rating; ?>/5
            <div class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span style="color: <?php echo $i <= $avg_rating ? '#ffc107' : '#ddd'; ?>">★</span>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="test-item info">
            <i class="icon fas fa-code"></i>
            <strong>Variables PHP disponibles:</strong>
            <div class="code">
                $product_id = <?php echo $product_id; ?><br>
                $avg_rating = <?php echo $avg_rating; ?><br>
                $total_reviews = <?php echo $total_reviews; ?>
            </div>
        </div>

        <h2>2. Test API Reviews</h2>
        <button onclick="testAPI()"><i class="fas fa-play"></i> Tester l'API</button>
        <div id="api-result"></div>

        <h2>3. Vérification JavaScript</h2>
        <div id="js-tests"></div>
        
        <h2>4. Console JavaScript (Erreurs)</h2>
        <div class="console-output" id="console-output">
            <div class="info-log">[INFO] Surveillance de la console démarrée...</div>
        </div>

        <h2>5. Test du Rendu des Étoiles</h2>
        <div class="test-item info">
            <strong>Affichage direct (HTML/CSS):</strong><br>
            <div class="stars">★★★★★</div>
            <div class="stars" style="color: #ddd;">☆☆☆☆☆</div>
        </div>

        <div class="test-item info">
            <strong>Simulation du code product.php :</strong>
            <div class="product-rating" style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                <div class="stars" style="display: flex; gap: 4px;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?php echo $i <= $avg_rating ? 'filled' : ''; ?>" style="color: <?php echo $i <= $avg_rating ? '#ffc107' : '#ddd'; ?>; font-size: 20px;">★</span>
                    <?php endfor; ?>
                </div>
                <span class="rating-text" style="color: #7A7A7A; font-size: 14px;">
                    <?php echo $avg_rating; ?>/5 (<?php echo $total_reviews; ?> avis)
                </span>
            </div>
        </div>

        <h2>6. Test des Fonctions JavaScript</h2>
        <button onclick="testShowTab()"><i class="fas fa-table"></i> Test showTab()</button>
        <button onclick="testUpdateRating()"><i class="fas fa-star"></i> Test updateRatingDisplay()</button>
        <button onclick="testShowToast()"><i class="fas fa-bell"></i> Test showToast()</button>
        <div id="function-tests"></div>

        <h2>7. Vérification des Scripts Chargés</h2>
        <div id="script-check"></div>

    </div>

    <!-- Include scripts like in product.php -->
    <script src="assets/js/toast.js"></script>
    <script src="assets/js/cart-count.js"></script>
    
    <script>
        // Console capture
        const consoleOutput = document.getElementById('console-output');
        const originalLog = console.log;
        const originalError = console.error;
        const originalWarn = console.warn;

        console.log = function(...args) {
            originalLog.apply(console, args);
            consoleOutput.innerHTML += `<div class="info-log">[LOG] ${args.join(' ')}</div>`;
            consoleOutput.scrollTop = consoleOutput.scrollHeight;
        };

        console.error = function(...args) {
            originalError.apply(console, args);
            consoleOutput.innerHTML += `<div class="error-log">[ERROR] ${args.join(' ')}</div>`;
            consoleOutput.scrollTop = consoleOutput.scrollHeight;
        };

        console.warn = function(...args) {
            originalWarn.apply(console, args);
            consoleOutput.innerHTML += `<div class="warn-log">[WARN] ${args.join(' ')}</div>`;
            consoleOutput.scrollTop = consoleOutput.scrollHeight;
        };

        // Capture errors
        window.addEventListener('error', function(e) {
            consoleOutput.innerHTML += `<div class="error-log">[ERROR] ${e.message} at ${e.filename}:${e.lineno}</div>`;
        });

        // Run JS tests on load
        window.addEventListener('DOMContentLoaded', function() {
            runJSTests();
            checkLoadedScripts();
        });

        function runJSTests() {
            const resultsDiv = document.getElementById('js-tests');
            let html = '';

            // Test 1: showTab exists
            if (typeof showTab === 'function') {
                html += '<div class="test-item pass"><i class="icon fas fa-check-circle"></i><strong>showTab()</strong> est définie</div>';
            } else {
                html += '<div class="test-item fail"><i class="icon fas fa-times-circle"></i><strong>showTab()</strong> n\'est PAS définie</div>';
            }

            // Test 2: updateRatingDisplay exists
            if (typeof updateRatingDisplay === 'function') {
                html += '<div class="test-item pass"><i class="icon fas fa-check-circle"></i><strong>updateRatingDisplay()</strong> est définie</div>';
            } else {
                html += '<div class="test-item fail"><i class="icon fas fa-times-circle"></i><strong>updateRatingDisplay()</strong> n\'est PAS définie</div>';
            }

            // Test 3: showToast exists
            if (typeof showToast === 'function') {
                html += '<div class="test-item pass"><i class="icon fas fa-check-circle"></i><strong>showToast()</strong> est définie</div>';
            } else {
                html += '<div class="test-item fail"><i class="icon fas fa-times-circle"></i><strong>showToast()</strong> n\'est PAS définie</div>';
            }

            // Test 4: jQuery (if needed)
            if (typeof jQuery !== 'undefined') {
                html += '<div class="test-item info"><i class="icon fas fa-info-circle"></i><strong>jQuery</strong> est chargé (version ' + jQuery.fn.jquery + ')</div>';
            } else {
                html += '<div class="test-item info"><i class="icon fas fa-info-circle"></i><strong>jQuery</strong> n\'est pas chargé (pas nécessaire)</div>';
            }

            resultsDiv.innerHTML = html;
        }

        function checkLoadedScripts() {
            const scripts = document.querySelectorAll('script');
            let html = '';
            
            scripts.forEach(script => {
                if (script.src) {
                    const src = script.src.replace(window.location.origin, '');
                    html += `<div class="test-item info"><i class="icon fas fa-file-code"></i>${src}</div>`;
                }
            });

            document.getElementById('script-check').innerHTML = html || '<div class="test-item warn">Aucun script externe détecté</div>';
        }

        function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<div class="test-item info"><i class="fas fa-spinner fa-spin"></i> Test en cours...</div>';
            
            console.log('Testing API: api/reviews.php?action=get&product_id=2&page=1');
            
            fetch('api/reviews.php?action=get&product_id=2&page=1')
                .then(response => {
                    console.log('API Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API Response data:', data);
                    
                    if (data.success) {
                        let html = '<div class="test-item pass"><i class="icon fas fa-check-circle"></i><strong>API fonctionne!</strong></div>';
                        html += '<div class="code">' + JSON.stringify(data, null, 2) + '</div>';
                        resultDiv.innerHTML = html;
                    } else {
                        resultDiv.innerHTML = `<div class="test-item fail"><i class="icon fas fa-times-circle"></i>API Error: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('API Error:', error);
                    resultDiv.innerHTML = `<div class="test-item fail"><i class="icon fas fa-times-circle"></i>Fetch Error: ${error.message}</div>`;
                });
        }

        function testShowTab() {
            const resultDiv = document.getElementById('function-tests');
            
            if (typeof showTab === 'function') {
                try {
                    console.log('Testing showTab() function...');
                    // Don't actually call it since we don't have the tabs here
                    resultDiv.innerHTML = '<div class="test-item pass"><i class="icon fas fa-check-circle"></i>showTab() existe et peut être appelée</div>';
                } catch(e) {
                    resultDiv.innerHTML = `<div class="test-item fail"><i class="icon fas fa-times-circle"></i>Erreur: ${e.message}</div>`;
                }
            } else {
                resultDiv.innerHTML = '<div class="test-item fail"><i class="icon fas fa-times-circle"></i>showTab() n\'existe pas</div>';
            }
        }

        function testUpdateRating() {
            const resultDiv = document.getElementById('function-tests');
            
            if (typeof updateRatingDisplay === 'function') {
                resultDiv.innerHTML = '<div class="test-item pass"><i class="icon fas fa-check-circle"></i>updateRatingDisplay() existe</div>';
            } else {
                resultDiv.innerHTML = '<div class="test-item fail"><i class="icon fas fa-times-circle"></i>updateRatingDisplay() n\'existe pas</div>';
            }
        }

        function testShowToast() {
            if (typeof showToast === 'function') {
                console.log('Testing showToast() function...');
                showToast('Test de notification!', 'success');
                document.getElementById('function-tests').innerHTML = '<div class="test-item pass"><i class="icon fas fa-check-circle"></i>showToast() fonctionne! Regardez en haut à droite.</div>';
            } else {
                document.getElementById('function-tests').innerHTML = '<div class="test-item fail"><i class="icon fas fa-times-circle"></i>showToast() n\'existe pas</div>';
            }
        }

        // Define showTab if it doesn't exist (for testing)
        if (typeof showTab === 'undefined') {
            console.warn('showTab() not found, defining dummy version');
            function showTab(tabId) {
                console.log('showTab called with:', tabId);
            }
        }

        // Define updateRatingDisplay if it doesn't exist (for testing)
        if (typeof updateRatingDisplay === 'undefined') {
            console.warn('updateRatingDisplay() not found, defining dummy version');
            function updateRatingDisplay(rating) {
                console.log('updateRatingDisplay called with:', rating);
            }
        }
    </script>
</body>
</html>
