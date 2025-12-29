<?php
require_once 'includes/auth_check.php';

$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = null;

if ($product_id > 0) {
    $query = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($cnx, $query);
    $product = mysqli_fetch_assoc($result);
}

// Récupérer les catégories pour le select
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories = mysqli_query($cnx, $categories_query);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? 'Modifier' : 'Nouveau'; ?> Produit - Admin Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-common.css">
    <script src="../assets/js/toast.js"></script>
</head>

<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="page-header">
                <div>
                    <a href="products.php"
                        style="color: var(--text-gray); text-decoration: none; font-size: 14px; display: block; margin-bottom: 10px;">
                        <i class="fas fa-arrow-left"></i> Retour aux produits
                    </a>
                    <h1><?php echo $product ? 'Modifier le Produit' : 'Ajouter un Produit'; ?></h1>
                </div>
            </div>

            <form id="productForm" class="content-grid" enctype="multipart/form-data">
                <div class="left-col">
                    <div class="card">
                        <div class="card-header">
                            <h3>Informations Générales</h3>
                        </div>
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Nom du produit</label>
                            <input type="text" name="name"
                                value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Description courte</label>
                            <textarea name="short_description"
                                rows="2"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Description complète</label>
                            <textarea name="description"
                                rows="8"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Prix & Inventaire</h3>
                        </div>
                        <div class="form-row-grid">
                            <div class="form-group">
                                <label>Prix de vente (DT)</label>
                                <input type="number" step="0.01" name="price"
                                    value="<?php echo $product['price'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Prix comparatif (DT)</label>
                                <input type="number" step="0.01" name="compare_price"
                                    value="<?php echo $product['compare_price'] ?? ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Référence produit</label>
                                <div style="display: flex; gap: 10px;">
                                    <input type="text" name="sku" id="sku_input"
                                        value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>"
                                        placeholder="Générer ou saisir...">
                                    <button type="button" class="btn btn-secondary" onclick="generateSKU()"
                                        style="padding: 10px; height: 43px;"><i class="fas fa-sync"></i></button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Stock actuel</label>
                                <input type="number" name="stock_quantity"
                                    value="<?php echo $product['stock_quantity'] ?? 0; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Alerte stock bas</label>
                                <input type="number" name="min_stock_level"
                                    value="<?php echo $product['min_stock_level'] ?? 5; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="right-col">
                    <div class="card">
                        <div class="card-header">
                            <h3>Organisation</h3>
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Catégorie</label>
                            <select name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $cat['category_id']; ?>"
                                    <?php echo (isset($product['category_id']) && $product['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Statut</label>
                            <select name="is_active">
                                <option value="1" <?php echo ($product['is_active'] ?? 1) == 1 ? 'selected' : ''; ?>>
                                    Actif
                                </option>
                                <option value="0" <?php echo ($product['is_active'] ?? 1) == 0 ? 'selected' : ''; ?>>
                                    Inactif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mise en avant</label>
                            <select name="is_featured">
                                <option value="0" <?php echo ($product['is_featured'] ?? 0) == 0 ? 'selected' : ''; ?>>
                                    Non
                                </option>
                                <option value="1" <?php echo ($product['is_featured'] ?? 0) == 1 ? 'selected' : ''; ?>>
                                    Oui
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>Image du produit</h3>
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Choisir une image</label>
                            <input type="file" name="product_image" id="product_image" accept="image/*"
                                onchange="previewLocalImage(this)">
                            <input type="hidden" name="existing_image"
                                value="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>">
                        </div>
                        <div id="imagePreview"
                            style="width: 100%; height: 200px; border: 2px dashed var(--beige-dark); border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: var(--bg-light);">
                            <?php if (isset($product['image_url']) && $product['image_url']): ?>
                            <img src="..<?php echo get_image_url($product['image_url'], 'Produit'); ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                            <i class="fas fa-image" style="font-size: 40px; color: var(--beige-dark);"></i>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                            <i class="fas fa-check"></i> Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const skuInput = document.getElementById('sku_input');
        const productId = document.querySelector('input[name="product_id"]').value;
        if (productId == "0" && !skuInput.value) {
            generateSKU();
        }
    });

    function generateSKU() {
        const prefix = "NR";
        const random = Math.random().toString(36).substring(2, 8).toUpperCase();
        const date = new Date().getFullYear().toString().substr(-2);
        const sku = `${prefix}-${random}`;
        document.getElementById('sku_input').value = sku;
    }

    function previewLocalImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML =
                    `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('productForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'save');

        fetch('api/products.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Produit enregistré avec succès !', 'success');
                    setTimeout(() => {
                        window.location.href = 'products.php';
                    }, 1000);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Une erreur est survenue lors de l\'enregistrement', 'error');
            });
    };
    </script>
</body>

</html>