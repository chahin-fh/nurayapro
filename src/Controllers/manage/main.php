<?php
include '../cnx.php';

// Vérifier si les paramètres GET existent
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$dl = isset($_GET['dl']) ? intval($_GET['dl']) : 0;

if ($dl == 1 && $id > 0) {
    // Récupérer le chemin de l'image avant suppression
    $result = mysqli_query($cnx, "SELECT image_url FROM products WHERE product_id = '$id'");
    
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        $image_path = $product['image_url'];
        
        // Supprimer le produit de la base de données
        $result_of_del = mysqli_query($cnx, "DELETE FROM products WHERE product_id = '$id'");
        
        if ($result_of_del) {
            // Vérifier si le fichier image existe avant de le supprimer
            if (file_exists($image_path)) {
                if (!unlink($image_path)) {
                    // Journaliser l'erreur si la suppression du fichier échoue
                    error_log("Failed to delete image file: " . $image_path);
                }
            } else {
                error_log("Image file not found: " . $image_path);
            }
            
            // Redirection après suppression réussie
            header("Location: index.php");
            mysqli_close($cnx);
            exit();
        } else {
            // Gérer l'erreur de suppression SQL
            die("Erreur lors de la suppression du produit: " . mysqli_error($cnx));
        }
    } else {
        // Produit non trouvé
        die("Produit non trouvé");
    }
} elseif ($id > 0) { 
    // Mode édition - Récupérer les informations du produit
    $result = mysqli_query($cnx, "SELECT p.name, p.category_id, p.price, p.stock_quantity, 
                                 p.description, p.product_id, p.image_url, c.name as category_name 
                                 FROM products p 
                                 INNER JOIN categories c ON p.category_id = c.category_id 
                                 WHERE p.product_id = '$id'");
    
    if (!$result || mysqli_num_rows($result) == 0) {
        die("Produit non trouvé");
    }
    
    $product = mysqli_fetch_assoc($result);
    $cat_res = mysqli_query($cnx, "SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../uploads/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .current-image {
            max-width: 200px;
            max-height: 200px;
            margin: 10px 0;
            display: block;
        }
        .photo-upload {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
        }
        .photo-upload:hover {
            border-color: #999;
        }
        .required {
            color: red;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fas fa-edit"></i> EDIT PRODUCT</h2>
        <form id="productForm" method="post" action="/nuraya_pro/manage-edit" enctype="multipart/form-data">
            <div class="form-group">
                <label for="photos">
                    <i class="fas fa-images"></i> Photo actuelle
                </label>
                <?php if (!empty($product['image_url'])) : ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="current-image" alt="Current product image">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                <?php endif; ?>
                
                <label for="photos">
                    <i class="fas fa-images"></i> Nouvelle photo
                </label>
                <div class="photo-upload" id="photoUploadArea">
                    <i class="fas fa-camera"></i>
                    <p>Drag photo here or click to upload</p>
                    <p class="upload-hint">Supported formats: JPG, PNG, GIF (Max 5MB)</p>
                    <input type="file" id="photos" name="pro_images" accept="image/*" style="display: none;">
                </div>
            </div>
            
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading"></i> Title
                    <span class="required">*</span>
                </label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['name']); ?>" required minlength="3">
                <div class="input-hint">Minimum 3 characters</div>
            </div>
            
            <div class="form-group">
                <label for="price">
                    <i class="fas fa-tag"></i> Price
                    <span class="required">*</span>
                </label>
                <div class="price-container">
                    <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" min="0.01" step="0.01" required>
                </div>
                <div class="input-hint">Enter a price greater than 0</div>
            </div>

            <div class="form-group">
                <label for="quantity">
                    <i class='fas fa-box'></i> Quantity
                    <span class="required">*</span>
                </label>
                <div class="quantity-container">
                    <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" min="1" required>
                </div>
                <div class="input-hint">Enter the stock quantity</div>
            </div>            
            
            <div class="form-group">
                <label for="category">
                    <i class="fas fa-tags"></i> Category
                    <span class="required">*</span>
                </label>
                <select name="category" id="category" required>
                    <option value="">Select a category</option>
                    <?php 
                    mysqli_data_seek($cat_res, 0);
                    while($tc = mysqli_fetch_assoc($cat_res)) : ?>
                        <option value="<?php echo $tc['category_id']; ?>" 
                            <?php echo ($tc['category_id'] == $product['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tc['name']); ?>
                        </option>
                    <?php endwhile; ?>    
                </select>
                <div class="input-hint">Select a category for your product</div>
            </div>
            
            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i> Description
                    <span class="required">*</span>
                </label>
                <textarea id="description" name="description" required minlength="10"><?php echo htmlspecialchars($product['description']); ?></textarea>
                <div class="input-hint">Minimum 10 characters</div>
            </div>
            
            <input type="hidden" name="id" value="<?php echo $product['product_id']; ?>">
            
            <button type="submit" class="btn">
                <i class="fas fa-save"></i> Update Product
            </button>
        </form>
    </div>

    <script>
        // Gestion du téléchargement d'image
        document.getElementById('photoUploadArea').addEventListener('click', function() {
            document.getElementById('photos').click();
        });

        document.getElementById('photos').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const uploadArea = document.getElementById('photoUploadArea');
                    uploadArea.innerHTML = '';
                    
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '200px';
                    
                    uploadArea.appendChild(img);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>
<?php } 
mysqli_close($cnx);
?>