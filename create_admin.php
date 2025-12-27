<?php
// Script pour créer un utilisateur admin via PHP
require_once 'includes/autoload.php';

// Configuration de l'admin
$admin_email = 'admin@nuraya.com';
$admin_password = 'Admin@2024'; // Changez ce mot de passe !
$admin_first_name = 'Admin';
$admin_last_name = 'Nuraya';

// Vérifier si l'admin existe déjà
$check_query = "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($cnx, $admin_email) . "'";
$result = mysqli_query($cnx, $check_query);

if (mysqli_num_rows($result) > 0) {
    // L'utilisateur existe, le mettre à jour en admin
    $user = mysqli_fetch_assoc($result);
    $update_query = "UPDATE users SET role = 'admin' WHERE id = " . $user['id'];
    
    if (mysqli_query($cnx, $update_query)) {
        echo "✅ Utilisateur existant mis à jour en admin avec succès !<br>";
        echo "Email: $admin_email<br>";
    } else {
        echo "❌ Erreur lors de la mise à jour: " . mysqli_error($cnx);
    }
} else {
    // Créer un nouvel utilisateur admin
    $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
    
    $insert_query = "INSERT INTO users (first_name, last_name, email, password_hash, role, is_verified, is_active, created_at) 
                     VALUES (
                         '" . mysqli_real_escape_string($cnx, $admin_first_name) . "',
                         '" . mysqli_real_escape_string($cnx, $admin_last_name) . "',
                         '" . mysqli_real_escape_string($cnx, $admin_email) . "',
                         '$password_hash',
                         'admin',
                         1,
                         1,
                         NOW()
                     )";
    
    if (mysqli_query($cnx, $insert_query)) {
        echo "✅ Nouvel utilisateur admin créé avec succès !<br>";
        echo "Email: $admin_email<br>";
        echo "Mot de passe: $admin_password<br>";
        echo "<br>⚠️ IMPORTANT: Changez ce mot de passe après la première connexion !<br>";
    } else {
        echo "❌ Erreur lors de la création: " . mysqli_error($cnx);
    }
}

echo "<br><br>";
echo "<a href='login.php'>→ Aller à la page de connexion</a><br>";
echo "<a href='admin/index.php'>→ Aller au panneau d'administration</a>";
?>
