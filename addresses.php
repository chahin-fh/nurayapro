<?php
require_once 'includes/autoload.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$user_query = "SELECT id, first_name, last_name, email FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($cnx, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Récupérer les adresses
$address_query = "SELECT * FROM user_addresses WHERE user_id = $user_id ORDER BY is_default DESC, created_at DESC";
$address_result = mysqli_query($cnx, $address_query);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Adresses — Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --bg-light: #F5EFE6;
        --bg-white: #FAF7F2;
        --beige-dark: #C8B6A6;
        --text-dark: #1C1C1C;
        --text-gray: #7A7A7A;
        --accent-pink: #E6B7C8;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background: var(--bg-light);
        color: var(--text-dark);
        min-height: 100vh;
    }

    .account-container {
        display: flex;
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        gap: 30px;
    }

    /* Sidebar Styles (Same as account.php) */
    .account-sidebar {
        width: 280px;
        background: var(--bg-white);
        border-radius: 12px;
        padding: 24px;
        align-self: flex-start;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.1);
    }

    .user-profile-summary {
        text-align: center;
        padding-bottom: 24px;
        border-bottom: 1px solid rgba(200, 182, 166, 0.2);
        margin-bottom: 24px;
    }

    .user-avatar {
        width: 80px;
        height: 80px;
        background: var(--beige-dark);
        color: var(--bg-white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 700;
        margin: 0 auto 16px;
    }

    .user-name {
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 4px;
    }

    .user-email {
        color: var(--text-gray);
        font-size: 14px;
    }

    .sidebar-menu {
        list-style: none;
    }

    .sidebar-menu li {
        margin-bottom: 8px;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: var(--text-dark);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: var(--bg-light);
        color: var(--beige-dark);
    }

    .sidebar-menu a.active {
        font-weight: 600;
    }

    /* Content Area */
    .account-content {
        flex: 1;
        background: var(--bg-white);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.1);
    }

    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        border-bottom: 1px solid rgba(200, 182, 166, 0.2);
        padding-bottom: 20px;
    }

    .header-text h1 {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .header-text p {
        color: var(--text-gray);
        font-size: 14px;
    }

    .btn-add {
        background: var(--beige-dark);
        color: var(--bg-white);
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-add:hover {
        background: var(--text-dark);
        transform: translateY(-2px);
    }

    .address-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .address-card {
        background: var(--bg-light);
        border: 1px solid rgba(200, 182, 166, 0.3);
        border-radius: 12px;
        padding: 20px;
        position: relative;
        transition: all 0.3s ease;
    }

    .address-card.default {
        border-color: var(--beige-dark);
        background: #fff;
        box-shadow: 0 4px 12px rgba(200, 182, 166, 0.15);
    }

    .default-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: var(--beige-dark);
        color: #fff;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .address-title {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 12px;
        color: var(--text-dark);
        padding-right: 60px;
    }

    .address-details p {
        margin-bottom: 6px;
        font-size: 14px;
        color: var(--text-gray);
        line-height: 1.5;
    }

    .address-actions {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        gap: 10px;
    }

    .btn-action {
        background: none;
        border: none;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        padding: 5px 0;
        transition: color 0.3s ease;
    }

    .btn-edit {
        color: var(--text-dark);
    }

    .btn-delete {
        color: #e74c3c;
    }

    .btn-default {
        color: var(--beige-dark);
    }

    .btn-action:hover {
        text-decoration: underline;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 700;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--text-gray);
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        font-weight: 600;
    }

    .form-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .form-row {
        display: flex;
        gap: 15px;
    }

    .form-row .form-group {
        flex: 1;
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        background: var(--beige-dark);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        margin-top: 10px;
    }

    @media (max-width: 900px) {
        .account-container {
            flex-direction: column;
        }

        .account-sidebar {
            width: 100%;
        }

        .sidebar-menu {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 10px;
        }

        .sidebar-menu li {
            flex-shrink: 0;
        }
    }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="account-container">
        <!-- Sidebar -->
        <div class="account-sidebar">
            <div class="user-profile-summary">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                </div>
                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="account.php"><i class="fas fa-user"></i> Mon Profil</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Mes Commandes</a></li>
                <li><a href="wishlist.php"><i class="fas fa-heart"></i> Mes Favoris</a></li>
                <li><a href="addresses.php" class="active"><i class="fas fa-map-marker-alt"></i> Adresses</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                <li><a href="api/auth.php?action=logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>

        <div class="account-content">
            <div class="content-header">
                <div class="header-text">
                    <h1>Mes Adresses</h1>
                    <p>Gérez vos adresses de livraison et de facturation</p>
                </div>
                <button class="btn-add" onclick="openModal('add')">
                    <i class="fas fa-plus"></i> Nouvelle adresse
                </button>
            </div>

            <div class="address-grid">
                <?php while ($addr = mysqli_fetch_assoc($address_result)): ?>
                <div class="address-card <?php echo $addr['is_default'] ? 'default' : ''; ?>">
                    <?php if ($addr['is_default']): ?>
                    <span class="default-badge">Défaut</span>
                    <?php endif; ?>

                    <div class="address-title"><?php echo htmlspecialchars($addr['title']); ?></div>
                    <div class="address-details">
                        <p><strong><?php echo htmlspecialchars($addr['first_name'] . ' ' . $addr['last_name']); ?></strong>
                        </p>
                        <p><?php echo htmlspecialchars($addr['address_line1']); ?></p>
                        <?php if (!empty($addr['address_line2'])): ?>
                        <p><?php echo htmlspecialchars($addr['address_line2']); ?></p>
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($addr['postal_code'] . ' ' . $addr['city']); ?></p>
                        <p><?php echo htmlspecialchars($addr['country']); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($addr['phone']); ?></p>
                    </div>

                    <div class="address-actions">
                        <button class="btn-action btn-edit"
                            onclick='openModal("edit", <?php echo json_encode($addr); ?>)'>Modifier</button>
                        <span style="color:#ddd">|</span>
                        <?php if (!$addr['is_default']): ?>
                        <button class="btn-action btn-default" onclick="setDefault(<?php echo $addr['id']; ?>)">Définir
                            par défaut</button>
                        <span style="color:#ddd">|</span>
                        <button class="btn-action btn-delete"
                            onclick="deleteAddress(<?php echo $addr['id']; ?>)">Supprimer</button>
                        <?php else: ?>
                        <span class="btn-action" style="cursor: default; color: var(--success-green);">Adresse
                            principale</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal" id="addressModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nouvelle Adresse</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="addressForm">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label class="form-label">Titre (ex: Maison, Bureau)</label>
                    <input type="text" name="title" class="form-input" placeholder="Mon adresse">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="first_name" class="form-input" required
                            value="<?php echo htmlspecialchars($user['first_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom</label>
                        <input type="text" name="last_name" class="form-input" required
                            value="<?php echo htmlspecialchars($user['last_name']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="phone" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="address_line1" class="form-input" required placeholder="Rue, numéro...">
                </div>

                <div class="form-group">
                    <label class="form-label">Complément (optionnel)</label>
                    <input type="text" name="address_line2" class="form-input" placeholder="Appartement, étage...">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Code Postal</label>
                        <input type="text" name="postal_code" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ville</label>
                        <input type="text" name="city" class="form-input" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Ajouter l'adresse</button>
            </form>
        </div>
    </div>

    <script>
    function openModal(mode = 'add', data = null) {
        const modal = document.getElementById('addressModal');
        const form = document.getElementById('addressForm');
        const title = modal.querySelector('.modal-title');
        const submitBtn = modal.querySelector('.btn-submit');

        // Reset form
        form.reset();

        // Remove hidden id input if exists
        const existingId = form.querySelector('input[name="address_id"]');
        if (existingId) existingId.remove();

        if (mode === 'edit' && data) {
            title.textContent = 'Modifier l\'adresse';
            submitBtn.textContent = 'Mettre à jour';
            form.querySelector('input[name="action"]').value = 'update';

            // Add hidden id input
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'address_id';
            idInput.value = data.id;
            form.appendChild(idInput);

            // Fill fields
            form.querySelector('input[name="title"]').value = data.title;
            form.querySelector('input[name="first_name"]').value = data.first_name;
            form.querySelector('input[name="last_name"]').value = data.last_name;
            form.querySelector('input[name="phone"]').value = data.phone;
            form.querySelector('input[name="address_line1"]').value = data.address_line1;
            form.querySelector('input[name="address_line2"]').value = data.address_line2 || '';
            form.querySelector('input[name="postal_code"]').value = data.postal_code;
            form.querySelector('input[name="city"]').value = data.city;
        } else {
            title.textContent = 'Nouvelle Adresse';
            submitBtn.textContent = 'Ajouter l\'adresse';
            form.querySelector('input[name="action"]').value = 'add';
            // Pre-fill name from PHP user data if adding new
            form.querySelector('input[name="first_name"]').value =
                '<?php echo htmlspecialchars($user['first_name']); ?>';
            form.querySelector('input[name="last_name"]').value = '<?php echo htmlspecialchars($user['last_name']); ?>';
        }

        modal.classList.add('active');
    }

    function closeModal() {
        document.getElementById('addressModal').classList.remove('active');
    }

    // Close on outside click
    window.onclick = function(event) {
        if (event.target == document.getElementById('addressModal')) {
            closeModal();
        }
    }

    document.getElementById('addressForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('api/addresses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });

    function deleteAddress(id) {
        if (!confirm('Voulez-vous vraiment supprimer cette adresse ?')) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('address_id', id);

        fetch('api/addresses.php', {
                method: 'POST',
                body: formData
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

    function setDefault(id) {
        const formData = new FormData();
        formData.append('action', 'set_default');
        formData.append('address_id', id);

        fetch('api/addresses.php', {
                method: 'POST',
                body: formData
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