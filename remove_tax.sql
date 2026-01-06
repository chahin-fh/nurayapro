-- Script pour supprimer toutes les fonctionnalités de TVA de la base de données NURAYA
-- Date: 2026-01-06

-- 1. Supprimer la colonne tax_amount de la table orders
ALTER TABLE `orders` DROP COLUMN IF EXISTS `tax_amount`;

-- 2. Supprimer le paramètre tax_rate de la table settings
DELETE FROM `settings` WHERE `key` = 'tax_rate';

-- 3. Mettre à jour les totaux existants dans les commandes (recalculer sans TVA)
-- Note: Cela suppose que tax_amount était 19% du subtotal
UPDATE `orders` 
SET `total_amount` = `subtotal` + `shipping_amount`
WHERE `total_amount` > 0;

-- Vérification des modifications
SELECT 'Tables modifiées avec succès' as status;
SELECT COUNT(*) as orders_updated FROM `orders`;
