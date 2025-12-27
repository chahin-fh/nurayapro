<?php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
include '../cnx.php';

// Récupérer l'action demandée
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addReview();
        break;
    case 'get':
        getReviews();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

function addReview()
{
    global $cnx;

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $product_id = (int) $_POST['product_id'];
    $rating = (int) $_POST['rating'];
    $title = trim($_POST['title'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    // Validation
    if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        return;
    }

    if (strlen($comment) < 10) {
        echo json_encode(['success' => false, 'message' => 'L\'avis doit contenir au moins 10 caractères']);
        return;
    }

    // Vérifier si le produit existe
    $product_query = "SELECT id FROM products WHERE product_id = $product_id AND is_active = 1";
    $product_result = mysqli_query($cnx, $product_query);

    if (mysqli_num_rows($product_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Produit non disponible']);
        return;
    }

    // Vérifier si l'utilisateur a déjà laissé un avis
    $existing_query = "SELECT id FROM reviews WHERE user_id = $user_id AND product_id = $product_id";
    $existing_result = mysqli_query($cnx, $existing_query);

    if (mysqli_num_rows($existing_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Vous avez déjà laissé un avis pour ce produit']);
        return;
    }

    // Vérifier si l'utilisateur a acheté le produit (optionnel)
    $purchase_query = "SELECT oi.id FROM order_items oi 
                      JOIN orders o ON oi.order_id = o.id 
                      WHERE oi.product_id = $product_id AND o.user_id = $user_id 
                      AND o.status IN ('delivered', 'completed')";
    $purchase_result = mysqli_query($cnx, $purchase_query);
    $is_verified_purchase = mysqli_num_rows($purchase_result) > 0;

    // Insérer l'avis
    $insert_query = "INSERT INTO reviews (product_id, user_id, rating, title, comment, is_verified_purchase) 
                    VALUES ($product_id, $user_id, $rating, " . ($title ? "'$title'" : 'NULL') . ", '$comment', $is_verified_purchase)";

    if (mysqli_query($cnx, $insert_query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Avis envoyé avec succès. Il sera visible après validation.',
            'verified_purchase' => $is_verified_purchase
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'avis']);
    }
}

function getReviews()
{
    global $cnx;

    $product_id = (int) $_GET['product_id'];
    $page = (int) ($_GET['page'] ?? 1);
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID produit invalide']);
        return;
    }

    // Récupérer les avis avec pagination
    $reviews_query = "SELECT r.*, u.first_name, u.last_name, u.role as user_role,
                      CASE WHEN u.role = 'admin' THEN 1 ELSE 0 END as is_admin_review
                      FROM reviews r 
                      LEFT JOIN users u ON r.user_id = u.id 
                      WHERE r.product_id = $product_id AND r.is_approved = 1 
                      ORDER BY is_admin_review DESC, r.created_at DESC 
                      LIMIT $limit OFFSET $offset";
    $reviews_result = mysqli_query($cnx, $reviews_query);

    // Compter le total d'avis
    $count_query = "SELECT COUNT(*) as total FROM reviews 
                    WHERE product_id = $product_id AND is_approved = 1";
    $count_result = mysqli_query($cnx, $count_query);
    $total = mysqli_fetch_assoc($count_result)['total'];

    // Calculer les statistiques
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

    $reviews = [];

    while ($review = mysqli_fetch_assoc($reviews_result)) {
        $reviews[] = [
            'id' => $review['id'],
            'rating' => $review['rating'],
            'title' => $review['title'],
            'comment' => $review['comment'],
            'author' => $review['first_name'] . ' ' . $review['last_name'],
            'date' => $review['created_at'],
            'verified_purchase' => (bool) $review['is_verified_purchase'],
            'is_admin' => (bool) $review['is_admin_review']
        ];
    }

    echo json_encode([
        'success' => true,
        'reviews' => $reviews,
        'stats' => [
            'avg_rating' => round($stats['avg_rating'], 1),
            'total_reviews' => $stats['total_reviews'],
            'distribution' => [
                5 => (int) $stats['five_star'],
                4 => (int) $stats['four_star'],
                3 => (int) $stats['three_star'],
                2 => (int) $stats['two_star'],
                1 => (int) $stats['one_star']
            ]
        ],
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_reviews' => $total
        ]
    ]);
}

mysqli_close($cnx);
?>