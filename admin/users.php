<?php
require_once 'includes/auth_check.php';

// Pagination
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filtres
$search = isset($_GET['search']) ? mysqli_real_escape_string($cnx, $_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';

// Construire la requête
$where = ["1=1"];
if ($search) {
    $where[] = "(first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%')";
}
if ($role_filter !== 'all') {
    $role_val = mysqli_real_escape_string($cnx, $role_filter);
    $where[] = "role = '$role_val'";
}

$where_clause = implode(' AND ', $where);

// Compter le total
$count_query = "SELECT COUNT(*) as total FROM users WHERE $where_clause";
$count_result = mysqli_query($cnx, $count_query);
$total_users = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_users / $limit);

// Récupérer les utilisateurs
$users_query = "SELECT * FROM users WHERE $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$users_result = mysqli_query($cnx, $users_query);

// Si c'est une requête AJAX, on ne renvoie que le contenu nécessaire
if (isset($_GET['ajax'])) {
    ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date d'inscription</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div
                                    style="width: 40px; height: 40px; background: var(--beige-dark); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                    <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>"
                                style="background: <?php echo $user['role'] === 'admin' ? 'rgba(28,28,28,0.1)' : 'rgba(200,182,166,0.1)'; ?>; color: <?php echo $user['role'] === 'admin' ? 'var(--text-dark)' : 'var(--beige-dark)'; ?>;">
                                <?php echo $user['role']; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <span class="badge <?php echo $user['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo $user['is_active'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <button
                                    onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? 0 : 1; ?>)"
                                    class="btn-icon btn-edit" title="Changer statut">
                                    <i class="fas fa-power-off"></i>
                                </button>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn-icon btn-delete"
                                    title="Supprimer">
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
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role_filter; ?>"
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
    <title>Gestion Utilisateurs - Admin Nuraya</title>
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
                <h1>Gestion des Utilisateurs</h1>
                <p><?php echo $total_users; ?> utilisateur(s) au total</p>
            </div>

            <form class="filters" method="GET">
                <div class="filter-group">
                    <label>Rechercher</label>
                    <input type="text" name="search" placeholder="Nom, email..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label>Rôle</label>
                    <select name="role">
                        <option value="all" <?php echo $role_filter === 'all' ? 'selected' : ''; ?>>Tous les rôles
                        </option>
                        <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>Utilisateurs
                        </option>
                        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Administrateurs
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
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Date d'inscription</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div
                                            style="width: 40px; height: 40px; background: var(--beige-dark); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span
                                        class="badge <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>"
                                        style="background: <?php echo $user['role'] === 'admin' ? 'rgba(28,28,28,0.1)' : 'rgba(200,182,166,0.1)'; ?>; color: <?php echo $user['role'] === 'admin' ? 'var(--text-dark)' : 'var(--beige-dark)'; ?>;">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <span
                                        class="badge <?php echo $user['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo $user['is_active'] ? 'Actif' : 'Inactif'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <button
                                            onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? 0 : 1; ?>)"
                                            class="btn-icon btn-edit" title="Changer statut">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                        <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn-icon btn-delete"
                                            title="Supprimer">
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
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo $role_filter; ?>"
                            class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleUserStatus(userId, newStatus) {
            if (!confirm('Changer le statut de cet utilisateur ?')) return;

            fetch('api/users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=toggle_status&user_id=${userId}&status=${newStatus}`
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

        function deleteUser(userId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) return;

            fetch('api/users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete&user_id=${userId}`
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