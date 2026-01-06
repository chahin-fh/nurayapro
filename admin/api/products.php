<?php
session_start();
header('Content-Type: application/json');

// Vérifier l'authentification admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'save':
        saveProduct();
        break;
    case 'delete':
        deleteProduct();
        break;
    case 'toggle_status':
        toggleStatus();
        break;
    case 'update_stock':
        updateStock();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Action invalide']);
}

function deleteProduct()
{
    global $cnx;
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        return;
    }
    
    // Vérifier si le produit a des commandes
    $check_query = "SELECT COUNT(*) as count FROM order_items WHERE product_id = $product_id";
    $result = mysqli_query($cnx, $check_query);
    $count = mysqli_fetch_assoc($result)['count'];
    
    if ($count > 0) {
        // Ne pas supprimer, juste désactiver
        $update_query = "UPDATE products SET is_active = 0 WHERE product_id = $product_id";
        mysqli_query($cnx, $update_query);
        echo json_encode(['success' => true, 'message' => 'Produit désactivé (présent dans des commandes)']);
    } else {
        // Supprimer complètement
        $delete_query = "DELETE FROM products WHERE product_id = $product_id";
        if (mysqli_query($cnx, $delete_query)) {
            echo json_encode(['success' => true, 'message' => 'Produit supprimé']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur de suppression']);
        }
    }
}

function toggleStatus()
{
    global $cnx;
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    $query = "UPDATE products SET is_active = NOT is_active WHERE product_id = $product_id";
    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
    }
}

function updateStock()
{
    global $cnx;
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    
    $query = "UPDATE products SET stock_quantity = $quantity WHERE product_id = $product_id";
    if (mysqli_query($cnx, $query)) {
        echo json_encode(['success' => true, 'message' => 'Stock mis à jour']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de mise à jour']);
    }
}
function saveProduct()
{
    global $cnx;
    $id = (int)($_POST['product_id'] ?? 0);
    $name = mysqli_real_escape_string($cnx, $_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $sku = mysqli_real_escape_string($cnx, $_POST['sku'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $compare_price = $_POST['compare_price'] ? (float)$_POST['compare_price'] : 'NULL';
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $min_stock_level = (int)($_POST['min_stock_level'] ?? 5);
    $short_description = mysqli_real_escape_string($cnx, $_POST['short_description'] ?? '');
    $description = mysqli_real_escape_string($cnx, $_POST['description'] ?? '');
    $is_active = (int)($_POST['is_active'] ?? 1);
    $is_featured = (int)($_POST['is_featured'] ?? 0);
    $sizes_raw = (string)($_POST['sizes'] ?? '');
    
    // Gestion de l'image
    $image_url = mysqli_real_escape_string($cnx, $_POST['existing_image'] ?? '');
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['product_image']['tmp_name'];
        $file_name = $_FILES['product_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = uniqid('prod_') . '.' . $file_ext;
            $upload_dir = __DIR__ . '/../../uploads/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $image_url = 'uploads/' . $new_file_name;
            }
        }
    }

    // Gestion des images supplémentaires
    $existing_additional_images = [];
    if (isset($_POST['existing_additional_images'])) {
        $existing_additional_images = $_POST['existing_additional_images'];
    }
    
    // Traitement des images supplémentaires à supprimer
    $images_to_remove = [];
    if (isset($_POST['remove_additional_images'])) {
        $images_to_remove = $_POST['remove_additional_images'];
    }
    
    // Filtrer les images existantes en supprimant celles marquées pour suppression
    $filtered_existing_images = [];
    foreach ($existing_additional_images as $img) {
        if (!in_array($img, $images_to_remove)) {
            $filtered_existing_images[] = $img;
        }
    }
    
    // Traitement des nouvelles images supplémentaires
    if (isset($_FILES['additional_images']) && $_FILES['additional_images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        $files = $_FILES['additional_images'];
        $file_count = count($files['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $file_tmp = $files['tmp_name'][$i];
                $file_name = $files['name'][$i];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($file_ext, $allowed_ext)) {
                    $new_file_name = uniqid('prod_') . '.' . $file_ext;
                    $upload_dir = __DIR__ . '/../../uploads/';
                    
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                        $filtered_existing_images[] = 'uploads/' . $new_file_name;
                    }
                }
            }
        }
    }
    
    // Convertir le tableau d'images en JSON
    $additional_images_json = json_encode($filtered_existing_images);

    if (empty($name) || $category_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Nom et catégorie sont requis']);
        return;
    }
    
    if ($id > 0) {
        $query = "UPDATE products SET 
                  name = '$name', category_id = $category_id, sku = '$sku', 
                  price = $price, compare_price = $compare_price, 
                  stock_quantity = $stock_quantity, min_stock_level = $min_stock_level, 
                  image_url = '$image_url', short_description = '$short_description', 
                  description = '$description', is_active = $is_active, is_featured = $is_featured,
                  additional_images = '$additional_images_json'
                  WHERE product_id = $id";
    } else {
        $query = "INSERT INTO products (name, category_id, sku, price, compare_price, 
                  stock_quantity, min_stock_level, image_url, short_description, 
                  description, is_active, is_featured, additional_images) 
                  VALUES ('$name', $category_id, '$sku', $price, $compare_price, 
                  $stock_quantity, $min_stock_level, '$image_url', '$short_description', 
                  '$description', $is_active, $is_featured, '$additional_images_json')";
    }
    
    if (mysqli_query($cnx, $query)) {
        $saved_product_id = $id > 0 ? $id : (int)mysqli_insert_id($cnx);

        // Enregistrer les tailles
        $sizes = array_values(array_filter(array_map('trim', explode(',', $sizes_raw)), fn($v) => $v !== ''));
        $unique_sizes = [];
        foreach ($sizes as $s) {
            $key = mb_strtolower($s);
            if (!isset($unique_sizes[$key])) {
                $unique_sizes[$key] = $s;
            }
        }

        mysqli_query($cnx, "DELETE FROM product_sizes WHERE product_id = $saved_product_id");
        $sort = 0;
        foreach (array_values($unique_sizes) as $s) {
            $esc = mysqli_real_escape_string($cnx, $s);
            mysqli_query($cnx, "INSERT INTO product_sizes (product_id, size, sort_order) VALUES ($saved_product_id, '$esc', $sort)");
            $sort++;
        }

        echo json_encode(['success' => true, 'message' => 'Produit enregistré', 'image_url' => $image_url]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement: ' . mysqli_error($cnx)]);
    }
}
?>
