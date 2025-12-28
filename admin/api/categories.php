<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

require_once __DIR__ . '/../../../src/Controllers/cnx.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'save':
        saveCategory();
        break;
    case 'delete':
        deleteCategory();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action invalide']);
}

function saveCategory()
{
    global $cnx;
    $id = (int)($_POST['category_id'] ?? 0);
    $name = mysqli_real_escape_string($cnx, $_POST['name'] ?? '');
    $slug = mysqli_real_escape_string($cnx, $_POST['slug'] ?? '');
    $description = mysqli_real_escape_string($cnx, $_POST['description'] ?? '');
    $is_active = (int)($_POST['is_active'] ?? 1);
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    
    if (empty($name) || empty($slug)) {
        echo json_encode(['success' => false, 'message' => 'Nom et slug sont requis']);
        return;
    }
    
    if ($id > 0) {
        $query = "UPDATE categories SET name = '$name', slug = '$slug', description = '$description', 
                  is_active = $is_active, sort_order = $sort_order WHERE category_id = $id";
    } else {
        $query = "INSERT INTO categories (name, slug, description, is_active, sort_order) 
                  VALUES ('$name', '$slug', '$description', $is_active, $sort_order)";
    }
    
    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Catégorie enregistrée']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
    }
}

function deleteCategory()
{
    global $cnx;
    $id = (int)($_POST['category_id'] ?? 0);
    
    // Vérifier si la catégorie est utilisée par des produits
    $check_query = "SELECT COUNT(*) as count FROM products WHERE category_id = $id";
    $check_result = mysqli_query($cnx, $check_query);
    $count = mysqli_fetch_assoc($check_result)['count'];
    
    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Cette catégorie ne peut pas être supprimée car elle contient des produits']);
        return;
    }
    
    $query = "DELETE FROM categories WHERE category_id = $id";
    
    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Catégorie supprimée']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }
}
?>
