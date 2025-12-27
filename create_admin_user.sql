-- La colonne 'role' existe déjà dans la table users
-- Voici comment créer ou modifier un utilisateur admin

-- Option 1: Mettre à jour un utilisateur existant pour le rendre admin
-- Remplacez 'votre.email@example.com' par l'email de l'utilisateur
UPDATE users 
SET role = 'admin' 
WHERE email = 'votre.email@example.com';

-- Option 2: Créer un nouvel utilisateur admin directement
-- Note: Le mot de passe doit être hashé avec password_hash() en PHP
-- Exemple avec mot de passe 'admin123' (à changer!)
INSERT INTO users (first_name, last_name, email, password_hash, role, is_verified, is_active, created_at)
VALUES (
    'Admin',
    'Nuraya',
    'admin@nuraya.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Mot de passe: 'password'
    'admin',
    1,
    1,
    NOW()
);

-- Option 3: Vérifier les utilisateurs admin existants
SELECT id, first_name, last_name, email, role, is_active 
FROM users 
WHERE role = 'admin';

-- Option 4: Révoquer les droits admin d'un utilisateur
UPDATE users 
SET role = 'user' 
WHERE email = 'ancien.admin@example.com';
