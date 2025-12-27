<?php
require_once 'includes/auth_check.php';

// Pagination et filtres
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($cnx, $_GET['search']) : '';

// Construire la requête
$where = [];
if ($status_filter !== 'all') {
    $where[] = "o.status = '$status_filter'";
}
if ($search) {
    $where[] = "(o.order_number LIKE '%$search%' OR u.email LIKE '%$search%' OR u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%')";
}

$where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Compter le total
$count_query = "SELECT COUNT(*) as total FROM orders o LEFT JOIN users u ON o.user_id = u.id $where_clause";
$count_result = mysqli_query($cnx, $count_query);
$total_orders = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_orders / $limit);

// Récupérer les commandes
$orders_query = "SELECT o.*, u.first_name, u.last_name, u.email, COUNT(oi.id) as item_count
                 FROM orders o
                 LEFT JOIN users u ON o.user_id = u.id
                 LEFT JOIN order_items oi ON o.id = oi.order_id
                 $where_clause
                 GROUP BY o.id
                 ORDER BY o.order_date DESC
                 LIMIT $limit OFFSET $offset";
$orders = mysqli_query($cnx, $orders_query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Commandes - Admin Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #f5f6fa; color: #2c3e50; }
        .admin-layout { display: flex; }
        .main-content { margin-left: 260px; flex: 1; padding: 30px; min-height: 100vh; }
        
        .page-header { margin-bottom: 30px; }
        .page-header h1 { font-size: 28px; margin-bottom: 8px; }
        
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
        
        .filter-group { flex: 1; min-width: 200px; }
        .filter-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 13px; color: #7f8c8d; }
        .filter-group input, .filter-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .orders-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        table { width: 100%; border-collapse: collapse; }
        table th {
            background: #f8f9fa;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        table td { padding: 16px; border-bottom: 1px solid #ecf0f1; }
        table tr:last-child td { border-bottom: none; }
        table tr:hover { background: #f8f9fa; }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #cce5ff; color: #004085; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1e7dd; color: #0f5132; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        .actions { display: flex; gap: 8px; }
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
        
        .btn-view { background: #e3f2fd; color: #2196f3; }
        .btn-view:hover { background: #2196f3; color: white; }
        
        .status-select {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }
        
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
        
        .page-link:hover, .page-link.active { background: #3498db; color: white; }
        
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 20px; }
            .filters { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>Gestion des Commandes</h1>
                <p>Total: <?php echo $total_orders; ?> commande(s)</p>
            </div>
            
            <form class="filters" method="GET">
                <div class="filter-group">
                    <label>Rechercher</label>
                    <input type="text" name="search" placeholder="N° commande, email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label>Statut</label>
                    <select name="status">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Tous</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>En attente</option>
                        <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmée</option>
                        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>En traitement</option>
                        <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Expédiée</option>
                        <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Livrée</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                    </select>
                </div>
                <div class="filter-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn-primary" style="width: 100%;">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
            
            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Articles</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?><br>
                                    <small style="color: #7f8c8d;"><?php echo htmlspecialchars($order['email']); ?></small>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td><?php echo $order['item_count']; ?> article(s)</td>
                                <td><strong><?php echo number_format($order['total'], 2); ?> DT</strong></td>
                                <td>
                                    <select class="status-select status-<?php echo $order['status']; ?>" 
                                            onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>En attente</option>
                                        <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmée</option>
                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>En traitement</option>
                                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Expédiée</option>
                                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Livrée</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-icon btn-view" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" 
                           class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function updateOrderStatus(orderId, newStatus) {
            if (!confirm('Confirmer le changement de statut ?')) {
                location.reload();
                return;
            }
            
            fetch('api/orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update_status&order_id=${orderId}&status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>
