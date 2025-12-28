<?php
require_once 'includes/auth_check.php';
require_once '../includes/functions.php';

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filtres
$search = isset($_GET['search']) ? mysqli_real_escape_string($cnx, $_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Construire la requête
$where = ["1=1"];
if ($search) {
    $where[] = "(p.name LIKE '%$search%' OR p.sku LIKE '%$search%')";
}
if ($category_filter > 0) {
    $where[] = "p.category_id = $category_filter";
}
if ($status_filter === 'active') {
    $where[] = "p.is_active = 1";
} elseif ($status_filter === 'inactive') {
    $where[] = "p.is_active = 0";
}

$where_clause = implode(' AND ', $where);

// Compter le total
$count_query = "SELECT COUNT(*) as total FROM products p WHERE $where_clause";
$count_result = mysqli_query($cnx, $count_query);
$total_products = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_products / $limit);

// Récupérer les produits
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE $where_clause 
          ORDER BY p.product_id DESC 
          LIMIT $limit OFFSET $offset";
$products_result = mysqli_query($cnx, $query);

// Récupérer les catégories pour le filtre
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories = mysqli_query($cnx, $categories_query);

// Si c'est une requête AJAX, on ne renvoie que le contenu nécessaire
if (isset($_GET['ajax'])) {
    ?>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Produit</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
            <tr>
                <td>
                    <?php if ($product['image_url']): ?>
                    <img src="<?php echo get_image_url($product['image_url'], 'Produit'); ?>" alt=""
                        class="product-image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    <?php else: ?>
                    <div
                        style="width: 60px; height: 60px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image" style="color: #ccc;"></i>
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="font-weight: 700; color: var(--text-dark);">
                        <?php echo htmlspecialchars($product['name']); ?></div>
                    <div style="font-size: 12px; color: var(--text-gray);">SKU:
                        <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></div>
                </td>
                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                <td><strong><?php echo number_format($product['price'], 2); ?> DT</strong></td>
                <td>
                    <?php if ($product['stock_quantity'] <= $product['min_stock_level']): ?>
                    <span class="badge badge-inactive"><?php echo $product['stock_quantity']; ?></span>
                    <?php else: ?>
                    <span class="badge badge-active"><?php echo $product['stock_quantity']; ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge <?php echo $product['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                        <?php echo $product['is_active'] ? 'Actif' : 'Inactif'; ?>
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a href="product-form.php?id=<?php echo $product['product_id']; ?>" class="btn-icon btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteProduct(<?php echo $product['product_id']; ?>)"
                            class="btn-icon btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>"
        class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
        <?php echo $i; ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Produits - Admin Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-common.css">
    <script src="../assets/js/toast.js"></script>
    <script src="js/dynamic-filters.js" defer></script>
</head>

<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <div class="page-header">
                <h1>Gestion des Produits</h1>
                <a href="product-form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Produit
                </a>
            </div>

            <form class="filters" method="GET">
                <div class="filter-group">
                    <label>Rechercher</label>
                    <input type="text" name="search" placeholder="Nom ou SKU..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label>Catégorie</label>
                    <select name="category">
                        <option value="0">Toutes les catégories</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['category_id']; ?>"
                            <?php echo $category_filter == $cat['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Statut</label>
                    <select name="status">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Tous</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Actif
                        </option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactif
                        </option>
                    </select>
                </div>
                <div class="filter-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                        <tr>
                            <td>
                                <?php if ($product['image_url']): ?>
                                <img src="<?php echo get_image_url($product['image_url'], 'Produit'); ?>" alt=""
                                    class="product-image"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                <div
                                    style="width: 60px; height: 60px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="color: #ccc;"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-dark);">
                                    <?php echo htmlspecialchars($product['name']); ?></div>
                                <div style="font-size: 12px; color: var(--text-gray);">SKU:
                                    <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td><strong><?php echo number_format($product['price'], 2); ?> DT</strong></td>
                            <td>
                                <?php if ($product['stock_quantity'] <= $product['min_stock_level']): ?>
                                <span class="badge badge-inactive"><?php echo $product['stock_quantity']; ?></span>
                                <?php else: ?>
                                <span class="badge badge-active"><?php echo $product['stock_quantity']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span
                                    class="badge <?php echo $product['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                    <?php echo $product['is_active'] ? 'Actif' : 'Inactif'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="product-form.php?id=<?php echo $product['product_id']; ?>"
                                        class="btn-icon btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteProduct(<?php echo $product['product_id']; ?>)"
                                        class="btn-icon btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>"
                    class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function deleteProduct(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) return;

        fetch('api/products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=delete&product_id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showToast(data.message, 'error');
                }
            });
    }
    </script>
</body>

</html>