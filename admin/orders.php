<?php
require_once 'includes/auth_check.php';

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filtres
$search = isset($_GET['search']) ? mysqli_real_escape_string($cnx, $_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Construire la requête
$where = ["1=1"];
if ($search) {
    $where[] = "(order_number LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%')";
}
if ($status_filter !== 'all') {
    $status_val = mysqli_real_escape_string($cnx, $status_filter);
    $where[] = "status = '$status_val'";
}

$where_clause = implode(' AND ', $where);

// Compter le total
$count_query = "SELECT COUNT(*) as total FROM orders WHERE $where_clause";
$count_result = mysqli_query($cnx, $count_query);
$total_orders = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_orders / $limit);

// Récupérer les commandes
$query = "SELECT * FROM orders WHERE $where_clause ORDER BY order_date DESC LIMIT $limit OFFSET $offset";
$orders_result = mysqli_query($cnx, $query);

// Si c'est une requête AJAX, on ne renvoie que le contenu nécessaire
if (isset($_GET['ajax'])) {
    ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr>
                        <td><strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                        <td>
                            <div style="font-weight: 600;">
                                <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                            </div>
                            <div style="font-size: 12px; color: var(--text-gray);">
                                <?php echo htmlspecialchars($order['email']); ?>
                            </div>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                        <td><strong><?php echo number_format($order['total_amount'], 2); ?> DT</strong></td>
                        <td>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-icon btn-view"
                                    title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <select onchange="updateStatus(<?php echo $order['id']; ?>, this.value)">
                                    <option value="">Changer statut</option>
                                    <option value="pending">En attente</option>
                                    <option value="confirmed">Confirmée</option>
                                    <option value="processing">Préparation</option>
                                    <option value="shipped">Expédiée</option>
                                    <option value="delivered">Livrée</option>
                                    <option value="cancelled">Annulée</option>
                                </select>
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
    <?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Commandes - Admin Nuraya</title>
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
                <h1>Gestion des Commandes</h1>
                <p><?php echo $total_orders; ?> commande(s) au total</p>
            </div>

            <form class="filters" method="GET">
                <div class="filter-group">
                    <label>Rechercher</label>
                    <input type="text" name="search" placeholder="N° commande, client..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label>Statut</label>
                    <select name="status">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Tous les statuts
                        </option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>En attente
                        </option>
                        <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>
                            Confirmée</option>
                        <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>En
                            préparation</option>
                        <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Expédiée
                        </option>
                        <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Livrée
                        </option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>
                            Annulée
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
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                <td>
                                    <div style="font-weight: 600;">
                                        <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-gray);">
                                        <?php echo htmlspecialchars($order['email']); ?>
                                    </div>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td><strong><?php echo number_format($order['total_amount'], 2); ?> DT</strong></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn-icon btn-view"
                                            title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <select onchange="updateStatus(<?php echo $order['id']; ?>, this.value)">
                                            <option value="">Changer statut</option>
                                            <option value="pending">En attente</option>
                                            <option value="confirmed">Confirmée</option>
                                            <option value="processing">Préparation</option>
                                            <option value="shipped">Expédiée</option>
                                            <option value="delivered">Livrée</option>
                                            <option value="cancelled">Annulée</option>
                                        </select>
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
        function updateStatus(orderId, newStatus) {
            if (!newStatus) return;
            fetch('api/orders.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=update_status&order_id=${orderId}&status=${newStatus}`
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