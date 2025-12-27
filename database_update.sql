-- Ajouter les champs birth_date et birthday_email_sent à la table users
ALTER TABLE users
ADD COLUMN birth_date DATE NULL COMMENT 'Date de naissance de l\'utilisateur',
ADD COLUMN birthday_email_sent BOOLEAN DEFAULT FALSE COMMENT 'Indique si l\'email d\'anniversaire a été envoyé cette année',
ADD COLUMN is_active BOOLEAN DEFAULT TRUE COMMENT 'Indique si le compte utilisateur est actif';

-- Créer un index pour optimiser les recherches d'anniversaires
CREATE INDEX idx_users_birth_date ON users (
    DATE_FORMAT(birth_date, '%m-%d')
);

CREATE INDEX idx_users_active_birthday ON users (
    is_active,
    birthday_email_sent,
    DATE_FORMAT(birth_date, '%m-%d')
);