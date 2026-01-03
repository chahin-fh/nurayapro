<?php
session_start();
header('Content-Type: application/json');

// Connexion à la base de données
require_once '../config/database.php';

// Récupérer l'action demandée
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addReview();
        break;
    case 'get':
        getReviews();
        break;
    case 'update':
        updateReview();
        break;
    case 'delete':
        deleteReview();
        break;
    case 'helpful':
        markHelpful();
        break;
    case 'report':
        reportReview();
        break;
    case 'user_reviews':
        getUserReviews();
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

    // Échapper les entrées pour éviter les injections SQL
    $title = mysqli_real_escape_string($cnx, $title);
    $comment = mysqli_real_escape_string($cnx, $comment);

    // Vérifier si le produit existe
    $product_query = "SELECT product_id FROM products WHERE product_id = $product_id AND is_active = 1";
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
    $is_verified_purchase = mysqli_num_rows($purchase_result) > 0 ? 1 : 0;

    // Insérer l'avis
    $insert_query = "INSERT INTO reviews (product_id, user_id, rating, title, comment, is_verified_purchase, is_approved) 
                    VALUES ($product_id, $user_id, $rating, " . ($title ? "'$title'" : 'NULL') . ", '$comment', $is_verified_purchase, 1)";

    if (mysqli_query($cnx, $insert_query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Avis envoyé avec succès.',
            'verified_purchase' => (bool) $is_verified_purchase
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi de l\'avis']);
    }
}

function getReviews()
{
    global $cnx;

    $product_id = (int) ($_GET['product_id'] ?? 0);
    $page = (int) ($_GET['page'] ?? 1);
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID produit invalide']);
        return;
    }

    // Récupérer les avis avec pagination et informations de votes
    $reviews_query = "SELECT r.*, u.first_name, u.last_name, u.role as user_role,
                      CASE WHEN u.role = 'admin' THEN 1 ELSE 0 END as is_admin_review,
                      COALESCE(r.helpful_count, 0) as helpful_count";
    
    // Si l'utilisateur est connecté, vérifier s'il a voté
    if ($user_id > 0) {
        $reviews_query .= ",
                      (SELECT COUNT(*) FROM review_helpful WHERE review_id = r.id AND user_id = $user_id) as user_voted";
    }
    
    $reviews_query .= " FROM reviews r 
                      LEFT JOIN users u ON r.user_id = u.id 
                      WHERE r.product_id = $product_id AND r.is_approved = 1 
                      ORDER BY is_admin_review DESC, helpful_count DESC, r.created_at DESC 
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
        $reviewData = [
            'id' => $review['id'],
            'rating' => $review['rating'],
            'title' => $review['title'],
            'comment' => $review['comment'],
            'author' => $review['first_name'] . ' ' . $review['last_name'],
            'date' => $review['created_at'],
            'verified_purchase' => (bool) $review['is_verified_purchase'],
            'is_admin' => (bool) $review['is_admin_review'],
            'helpful_count' => (int) $review['helpful_count']
        ];
        
        if ($user_id > 0) {
            $reviewData['user_voted'] = (bool) $review['user_voted'];
        }
        
        $reviews[] = $reviewData;
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

function updateReview()
{
    global $cnx;

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $review_id = (int) $_POST['review_id'];
    $rating = (int) $_POST['rating'];
    $title = trim($_POST['title'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($review_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        return;
    }

    // Échapper les entrées
    $title = mysqli_real_escape_string($cnx, $title);
    $comment = mysqli_real_escape_string($cnx, $comment);

    // Vérifier si l'avis appartient à l'utilisateur
    $check_query = "SELECT id FROM reviews WHERE id = $review_id AND user_id = $user_id";
    $check_result = mysqli_query($cnx, $check_query);

    if (mysqli_num_rows($check_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Avis non trouvé ou non autorisé']);
        return;
    }

    $update_query = "UPDATE reviews SET rating = $rating, title = " . ($title ? "'$title'" : 'NULL') . ", comment = '$comment', updated_at = NOW() WHERE id = $review_id";

    if (mysqli_query($cnx, $update_query)) {
        echo json_encode(['success' => true, 'message' => 'Avis mis à jour avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
    }
}

function deleteReview()
{
    global $cnx;

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $review_id = (int) ($_POST['review_id'] ?? 0);

    if ($review_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID avis invalide']);
        return;
    }

    // Vérifier si l'avis appartient à l'utilisateur ou si admin
    $check_query = "SELECT r.id, u.role 
                    FROM reviews r
                    LEFT JOIN users u ON r.user_id = u.id
                    WHERE r.id = $review_id AND (r.user_id = $user_id OR u.role = 'admin')";
    $check_result = mysqli_query($cnx, $check_query);

    if (mysqli_num_rows($check_result) === 0) {
        echo json_encode(['success' => false, 'message' => 'Avis non trouvé ou non autorisé']);
        return;
    }

    $delete_query = "DELETE FROM reviews WHERE id = $review_id";

    if (mysqli_query($cnx, $delete_query)) {
        echo json_encode(['success' => true, 'message' => 'Avis supprimé avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }
}

function markHelpful()
{
    global $cnx;

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $review_id = (int) ($_POST['review_id'] ?? 0);

    if ($review_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID avis invalide']);
        return;
    }

    // Vérifier si l'utilisateur a déjà marqué cet avis
    $existing_query = "SELECT id FROM review_helpful WHERE review_id = $review_id AND user_id = $user_id";
    $existing_result = mysqli_query($cnx, $existing_query);

    if (mysqli_num_rows($existing_result) > 0) {
        // Retirer le vote
        $delete_query = "DELETE FROM review_helpful WHERE review_id = $review_id AND user_id = $user_id";
        mysqli_query($cnx, $delete_query);
        echo json_encode(['success' => true, 'message' => 'Vote retiré', 'action' => 'removed']);
    } else {
        // Ajouter le vote
        $insert_query = "INSERT INTO review_helpful (review_id, user_id) VALUES ($review_id, $user_id)";
        mysqli_query($cnx, $insert_query);
        echo json_encode(['success' => true, 'message' => 'Vote ajouté', 'action' => 'added']);
    }
}

function reportReview()
{
    global $cnx;

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $review_id = (int) ($_POST['review_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');

    if ($review_id <= 0 || empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        return;
    }

    // Échapper la raison
    $reason = mysqli_real_escape_string($cnx, $reason);

    // Vérifier si l'utilisateur a déjà signalé cet avis
    $existing_query = "SELECT id FROM review_reports WHERE review_id = $review_id AND user_id = $user_id";
    $existing_result = mysqli_query($cnx, $existing_query);

    if (mysqli_num_rows($existing_result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Vous avez déjà signalé cet avis']);
        return;
    }

    $insert_query = "INSERT INTO review_reports (review_id, user_id, reason) VALUES ($review_id, $user_id, '$reason')";

    if (mysqli_query($cnx, $insert_query)) {
        echo json_encode(['success' => true, 'message' => 'Avis signalé avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors du signalement']);
    }
}

function getUserReviews()
{
    global $cnx;

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        return;
    }

    $user_id = $_SESSION['user_id'];
    $page = (int) ($_GET['page'] ?? 1);
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $reviews_query = "SELECT r.*, p.name as product_name, p.product_id
                      FROM reviews r 
                      LEFT JOIN products p ON r.product_id = p.product_id 
                      WHERE r.user_id = $user_id 
                      ORDER BY r.created_at DESC 
                      LIMIT $limit OFFSET $offset";
    $reviews_result = mysqli_query($cnx, $reviews_query);

    $count_query = "SELECT COUNT(*) as total FROM reviews WHERE user_id = $user_id";
    $count_result = mysqli_query($cnx, $count_query);
    $total = mysqli_fetch_assoc($count_result)['total'];

    $reviews = [];

    while ($review = mysqli_fetch_assoc($reviews_result)) {
        $reviews[] = [
            'id' => $review['id'],
            'product_id' => $review['product_id'],
            'product_name' => $review['product_name'],
            'rating' => $review['rating'],
            'title' => $review['title'],
            'comment' => $review['comment'],
            'date' => $review['created_at'],
            'is_approved' => (bool) $review['is_approved']
        ];
    }

    echo json_encode([
        'success' => true,
        'reviews' => $reviews,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_reviews' => $total
        ]
    ]);
}

mysqli_close($cnx);
?>