<?php
// Test the reviews API
include 'config/database.php';

// Get any product ID
$product_query = "SELECT product_id FROM products WHERE is_active = 1 LIMIT 1";
$result = mysqli_query($cnx, $product_query);
$product = mysqli_fetch_assoc($result);
$product_id = $product['product_id'];

echo "Testing with product_id: $product_id\n\n";

// Check if reviews exist for this product
$reviews_query = "SELECT COUNT(*) as count FROM reviews WHERE product_id = $product_id";
$reviews_result = mysqli_query($cnx, $reviews_query);
$reviews_count = mysqli_fetch_assoc($reviews_result);
echo "Total reviews for product: " . $reviews_count['count'] . "\n";

// Check approved reviews
$approved_query = "SELECT COUNT(*) as count FROM reviews WHERE product_id = $product_id AND is_approved = 1";
$approved_result = mysqli_query($cnx, $approved_query);
$approved_count = mysqli_fetch_assoc($approved_result);
echo "Approved reviews: " . $approved_count['count'] . "\n\n";

// Test the stats query from api/reviews.php
$stats_query = "SELECT AVG(rating) as avg_rating, 
               COUNT(*) as total_reviews,
               SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
               SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
               SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
               SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
               SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
               FROM reviews 
               WHERE product_id = $product_id AND is_approved = 1";
$stats_result = mysqli_query($cnx, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

echo "Statistics:\n";
echo json_encode($stats, JSON_PRETTY_PRINT);
echo "\n\n";

// Simulate API call
$_GET['action'] = 'get';
$_GET['product_id'] = $product_id;
$_GET['page'] = 1;

echo "Simulating API call to reviews.php...\n";
echo "URL: api/reviews.php?action=get&product_id=$product_id&page=1\n\n";

// Make actual API call
$url = "http://localhost/nurayapro/api/reviews.php?action=get&product_id=$product_id&page=1";
$response = file_get_contents($url);
echo "API Response:\n";
echo $response;
?>
