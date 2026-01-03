<?php
// Simple test to see what product.php actually renders
include 'config/database.php';
if (!isset($_SESSION)) {
    session_start();
}

$product_id = 2;

// Same queries as product.php
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = $product_id AND p.is_active = 1";
$result = mysqli_query($cnx, $query);
$product = mysqli_fetch_assoc($result);

$avg_rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                     FROM reviews 
                     WHERE product_id = $product_id AND is_approved = 1";
$avg_result = mysqli_query($cnx, $avg_rating_query);
$rating_data = mysqli_fetch_assoc($avg_result);
$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
$total_reviews = $rating_data['total_reviews'] ?? 0;

echo "=== PRODUCT DATA ===\n";
echo "Product ID: $product_id\n";
echo "Product Name: " . ($product['name'] ?? 'NOT FOUND') . "\n";
echo "Average Rating: $avg_rating\n";
echo "Total Reviews: $total_reviews\n\n";

echo "=== RATING HTML OUTPUT (as would appear in product.php) ===\n";
?>
<div class="product-rating" onclick="showTab('reviews'); document.getElementById('reviews').scrollIntoView({behavior: 'smooth', block: 'start'});">
    <div class="stars">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <span class="star <?php echo $i <= $avg_rating ? 'filled' : ''; ?>">★</span>
        <?php endfor; ?>
    </div>
    <span class="rating-text"><?php echo $avg_rating; ?>/5 (<?php echo $total_reviews; ?> avis)</span>
</div>
<?php

echo "\n\n=== EXPECTED OUTPUT ===\n";
echo "Should show: $avg_rating/5 ($total_reviews avis)\n";
echo "Stars filled: " . (int)$avg_rating . " out of 5\n";

// Check if reviews_section.php exists
echo "\n=== FILE CHECKS ===\n";
$files_to_check = [
    'templates/reviews_section.php',
    'assets/js/toast.js',
    'assets/js/cart-count.js'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✓ $file EXISTS (" . filesize($file) . " bytes)\n";
        
        // Check for key functions
        if ($file === 'templates/reviews_section.php') {
            $content = file_get_contents($file);
            if (strpos($content, 'function updateRatingDisplay') !== false) {
                echo "  ✓ Contains updateRatingDisplay()\n";
            } else {
                echo "  ✗ MISSING updateRatingDisplay()\n";
            }
            if (strpos($content, 'function loadReviews') !== false) {
                echo "  ✓ Contains loadReviews()\n";
            } else {
                echo "  ✗ MISSING loadReviews()\n";
            }
        }
    } else {
        echo "✗ $file DOES NOT EXIST\n";
    }
}

// Check product.php includes
echo "\n=== PRODUCT.PHP INCLUDES ===\n";
$product_content = file_get_contents('product.php');

if (strpos($product_content, 'toast.js') !== false) {
    echo "✓ toast.js included\n";
} else {
    echo "✗ toast.js NOT included\n";
}

if (strpos($product_content, 'reviews_section.php') !== false) {
    echo "✓ reviews_section.php included\n";
} else {
    echo "✗ reviews_section.php NOT included\n";
}

if (strpos($product_content, 'function showTab') !== false) {
    echo "✓ showTab() defined\n";
} else {
    echo "✗ showTab() NOT defined\n";
}

mysqli_close($cnx);
?>
