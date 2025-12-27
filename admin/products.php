<?php
require_once 'includes/auth_check.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filtres
$search = isset($_GET['search']) ? mysqli_real_escape_string($cnx, $_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Construire la requête
$where = [];
if ($search) {
    $where[] = "(name LIKE '%$search%' OR sku LIKE '%$search%')";
}
if ($category_filter) {
    $where[] = "category_id = $category_filter";
}
if ($status_filter === 'active') {
    $where[] = "is_active = 1";
} elseif ($status_filter === 'inactive') {
    $where[] = "is_active = 0";
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Compter le total
$count_query = "SELECT COUNT(*) as total FROM products $where_clause";
$count_result = mysqli_query($cnx, $count_query);
$total_products = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_products / $limit);

// Récupérer les produits
$products_query = "SELECT p.*, c.name as category_name 
                   FROM products p 
                   LEFT JOIN categories c ON p.category_id = c.category_id 
                   $where_clause 
                   ORDER BY p.created_at DESC 
                   LIMIT $limit OFFSET $offset";
$products = mysqli_query($cnx, $products_query);

// Récupérer les catégories pour le filtre
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories = mysqli_query($cnx, $categories_query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Produits - Admin Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #f5f6fa; color: #2c3e50; }
        .admin-layout { display: flex; }
        .main-content { margin-left: 260px; flex: 1; padding: 30px; min-height: 100vh; }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h1 { font-size: 28px; }
        
        .btn-primary {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover { background: #2980b9; transform: translateY(-2px); }
        
        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 13px;
            color: #7f8c8d;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .products-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th {
            background: #f8f9fa;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        
        table td {
            padding: 16px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        table tr:last-child td { border-bottom: none; }
        table tr:hover { background: #f8f9fa; }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .product-sku {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-icon {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .btn-edit { background: #e3f2fd; color: #2196f3; }
        .btn-edit:hover { background: #2196f3; color: white; }
        .btn-delete { background: #ffebee; color: #f44336; }
        .btn-delete:hover { background: #f44336; color: white; }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        
        .page-link {
            padding: 8px 12px;
            border-radius: 6px;
            background: white;
            color: #2c3e50;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .page-link:hover,
        .page-link.active {
            background: #3498db;
            color: white;
        }
        
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 20px; }
            .filters { flex-direction: column; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>Gestion des Produits</h1>
                <a href="product-form.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Produit
                </a>
            </div>
            
            <form class="filters" method="GET">
                <div class="filter-group">
                    <label>Rechercher</label>
                    <input type="text" name="search" placeholder="Nom ou SKU..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label>Catégorie</label>
                    <select name="category">
                        <option value="0">Toutes les catégories</option>
                        <?php mysqli_data_seek($categories, 0); while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo $category_filter == $cat['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Statut</label>
                    <select name="status">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Tous</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactif</option>
                    </select>
                </div>
                <div class="filter-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn-primary" style="width: 100%;">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
            
            <div class="products-table">
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
                        <?php while ($product = mysqli_fetch_assoc($products)): ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image"
                                         onerror="this.src='https://via.placeholder.com/60'">
                                </td>
                                <td>
                                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="product-sku">SKU: <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><strong><?php echo number_format($product['price'], 2); ?> DT</strong></td>
                                <td>
                                    <?php if ($product['stock_quantity'] <= $product['min_stock_level']): ?>
                                        <span class="badge badge-danger"><?php echo $product['stock_quantity']; ?></span>
                                    <?php elseif ($product['stock_quantity'] <= $product['min_stock_level'] * 2): ?>
                                        <span class="badge badge-warning"><?php echo $product['stock_quantity']; ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?php echo $product['stock_quantity']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['is_active']): ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="product-form.php?id=<?php echo $product['product_id']; ?>" class="btn-icon btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteProduct(<?php echo $product['product_id']; ?>)" class="btn-icon btn-delete">
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
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete&product_id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    </script>
</body>
</html>
