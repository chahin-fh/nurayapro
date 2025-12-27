<?php
require_once 'includes/autoload.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Traitement du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $phone = trim($_POST['phone']);
        $birth_date = trim($_POST['birth_date']);

        if (empty($first_name) || empty($last_name)) {
            $error_message = "Le prénom et le nom sont obligatoires.";
        } else {
            $update_query = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, birth_date = ? WHERE id = ?";
            $stmt = mysqli_prepare($cnx, $update_query);
            mysqli_stmt_bind_param($stmt, "ssssi", $first_name, $last_name, $phone, $birth_date, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Profil mis à jour avec succès.";
            } else {
                $error_message = "Erreur lors de la mise à jour : " . mysqli_error($cnx);
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = "Tous les champs de mot de passe sont obligatoires.";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "Les nouveaux mots de passe ne correspondent pas.";
        } elseif (strlen($new_password) < 6) {
            $error_message = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
        } else {
            // Vérifier l'ancien mot de passe
            $check_query = "SELECT password_hash FROM users WHERE id = ?";
            $stmt = mysqli_prepare($cnx, $check_query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user_data = mysqli_fetch_assoc($result);

            if (password_verify($current_password, $user_data['password_hash'])) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_pass = "UPDATE users SET password_hash = ? WHERE id = ?";
                $stmt_pass = mysqli_prepare($cnx, $update_pass);
                mysqli_stmt_bind_param($stmt_pass, "si", $new_hash, $user_id);
                
                if (mysqli_stmt_execute($stmt_pass)) {
                    $success_message = "Mot de passe modifié avec succès.";
                } else {
                    $error_message = "Erreur lors de la modification du mot de passe.";
                }
            } else {
                $error_message = "Mot de passe actuel incorrect.";
            }
        }
    }
}

// Récupérer les informations de l'utilisateur
$user_query = "SELECT id, first_name, last_name, email, phone, birth_date FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($cnx, $user_query);
$user = mysqli_fetch_assoc($user_result);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres — Nuraya</title>
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
        --error-red: #e74c3c;
        --success-green: #27ae60;
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

    .account-content {
        flex: 1;
        background: var(--bg-white);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.1);
    }

    .content-header {
        margin-bottom: 30px;
        border-bottom: 1px solid rgba(200, 182, 166, 0.2);
        padding-bottom: 20px;
    }

    .content-header h1 {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: var(--text-dark);
    }

    .form-input {
        width: 100%;
        padding: 12px;
        border: 1px solid rgba(200, 182, 166, 0.3);
        border-radius: 8px;
        font-size: 16px;
        font-family: inherit;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--beige-dark);
        box-shadow: 0 0 0 3px rgba(200, 182, 166, 0.1);
    }

    .btn-save {
        background: var(--beige-dark);
        color: var(--bg-white);
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background: var(--text-dark);
        transform: translateY(-2px);
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin: 30px 0 20px;
        color: var(--text-dark);
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(200, 182, 166, 0.1);
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background: rgba(39, 174, 96, 0.1);
        color: var(--success-green);
        border: 1px solid rgba(39, 174, 96, 0.2);
    }

    .alert-error {
        background: rgba(231, 76, 60, 0.1);
        color: var(--error-red);
        border: 1px solid rgba(231, 76, 60, 0.2);
    }

    @media (max-width: 900px) {
        .account-container {
            flex-direction: column;
        }
        
        .account-sidebar {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar-menu {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            width: 100%;
            padding-bottom: 5px;
        }

        .sidebar-menu li {
            margin-bottom: 0;
            flex-shrink: 0;
        }
    }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="account-container">
        <!-- Sidebar Identical to account.php -->
        <div class="account-sidebar">
            <div class="user-profile-summary">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>

            <ul class="sidebar-menu">
                <li><a href="account.php"><i class="fas fa-user"></i> Mon Profil</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> Mes Commandes</a></li>
                <li><a href="wishlist.php"><i class="fas fa-heart"></i> Mes Favoris</a></li>
                <li><a href="addresses.php"><i class="fas fa-map-marker-alt"></i> Adresses</a></li>
                <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> Paramètres</a></li>
                <li><a href="src/Controllers/api/auth.php?action=logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>

        <div class="account-content">
            <div class="content-header">
                <h1>Paramètres</h1>
                <p>Mettez à jour vos informations personnelles et votre mot de passe</p>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="first_name" class="form-input" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input type="text" name="last_name" class="form-input" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email (non modifiable)</label>
                    <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background: #f9f9f9; color: #777;">
                </div>

                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="birth_date" class="form-input" value="<?php echo htmlspecialchars($user['birth_date'] ?? ''); ?>">
                </div>

                <button type="submit" class="btn-save">Enregistrer les modifications</button>
            </form>

            <h2 class="section-title">Changer le mot de passe</h2>

            <form method="POST" action="">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label class="form-label">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="form-input" required minlength="6">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="confirm_password" class="form-input" required minlength="6">
                </div>

                <button type="submit" class="btn-save">Mettre à jour le mot de passe</button>
            </form>
        </div>
    </div>
</body>
</html>
