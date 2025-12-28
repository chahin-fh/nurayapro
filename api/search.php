<?php
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../config/database.php';
include '../includes/functions.php';

// Récupérer le terme de recherche
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($searchTerm) || strlen($searchTerm) < 2) {
    echo json_encode(['success' => false, 'message' => 'Terme de recherche trop court']);
    exit;
}

// Échapper le terme de recherche pour éviter les injections SQL
$searchTerm = mysqli_real_escape_string($cnx, $searchTerm);

// Rechercher dans les produits
$query = "SELECT p.product_id, p.name, p.price, p.image_url, c.name as category_name, c.slug as category_slug
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.is_active = 1 
            AND (p.name LIKE '%$searchTerm%' 
                 OR p.description LIKE '%$searchTerm%' 
                 OR p.short_description LIKE '%$searchTerm%'
                 OR p.sku LIKE '%$searchTerm%'
                 OR p.tags LIKE '%$searchTerm%')
            ORDER BY p.name ASC 
            LIMIT 10";

$result = mysqli_query($cnx, $query);

$products = [];

while ($product = mysqli_fetch_assoc($result)) {
    $products[] = [
        'product_id' => $product['product_id'],
        'name' => htmlspecialchars($product['name']),
        'price' => number_format((float) $product['price'], 3),
        'image_url' => get_image_url($product['image_url'], 'Produit'),
        'category_name' => htmlspecialchars($product['category_name']),
        'category_slug' => $product['category_slug']
    ];
}

echo json_encode([
    'success' => true,
    'results' => $products,
    'total' => count($products),
    'search_term' => htmlspecialchars($searchTerm)
]);

mysqli_close($cnx);
?>