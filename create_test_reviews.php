<?php
include 'config/database.php';

// Get first active product
$product_query = "SELECT product_id FROM products WHERE is_active = 1 LIMIT 1";
$result = mysqli_query($cnx, $product_query);
$product = mysqli_fetch_assoc($result);
$product_id = $product['product_id'];

// Get first user
$user_query = "SELECT id FROM users LIMIT 1";
$user_result = mysqli_query($cnx, $user_query);
$user = mysqli_fetch_assoc($user_result);
$user_id = $user['id'];

// Insert some test reviews
$reviews = [
    ['rating' => 5, 'title' => 'Excellent produit', 'comment' => 'Très satisfait de mon achat. Qualité au rendez-vous!'],
    ['rating' => 4, 'title' => 'Très bon', 'comment' => 'Produit conforme à mes attentes. Je recommande.'],
    ['rating' => 5, 'title' => 'Parfait', 'comment' => 'Exactement ce que je cherchais. Livraison rapide.'],
];

$inserted = 0;
foreach ($reviews as $review) {
    $query = "INSERT INTO reviews (product_id, user_id, rating, title, comment, is_approved, is_verified_purchase) 
              VALUES ($product_id, $user_id, {$review['rating']}, '{$review['title']}', '{$review['comment']}', 1, 0)";
    if (mysqli_query($cnx, $query)) {
        $inserted++;
    }
}

echo "Inserted $inserted test reviews for product_id: $product_id\n";

// Verify
$count_query = "SELECT COUNT(*) as total, AVG(rating) as avg FROM reviews WHERE product_id = $product_id AND is_approved = 1";
$count_result = mysqli_query($cnx, $count_query);
$stats = mysqli_fetch_assoc($count_result);

echo "Total approved reviews: {$stats['total']}\n";
echo "Average rating: " . round($stats['avg'], 1) . "/5\n";
?>
