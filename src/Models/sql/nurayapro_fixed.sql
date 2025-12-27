-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 10:41 AM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Database: `nuraya`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
    `category_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `image_url` varchar(255) DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `sort_order` int(11) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`category_id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `is_active` (`is_active`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO
    `categories` (
        `category_id`,
        `name`,
        `slug`,
        `description`,
        `is_active`,
        `sort_order`
    )
VALUES (
        1,
        'Mode',
        'mode',
        'Vêtements et accessoires de mode pour toutes les saisons',
        1,
        1
    ),
    (
        2,
        'Accessoires',
        'accessoires',
        'Accessoires complémentaires pour votre style',
        1,
        2
    ),
    (
        3,
        'Chaussures',
        'chaussures',
        'Chaussures élégantes et confortables',
        1,
        3
    ),
    (
        4,
        'Sacs',
        'sacs',
        'Sacs à la mode pour toutes occasions',
        1,
        4
    ),
    (
        5,
        'Bijoux',
        'bijoux',
        'Bijoux fins et élégants',
        1,
        5
    ),
    (
        6,
        'Printemps - Été 2025',
        'printemps-ete-2025',
        'Collection printanière aux couleurs vibrantes',
        1,
        6
    ),
    (
        7,
        'Collection Classique',
        'collection-classique',
        'Pièces intemporelles et élégantes',
        1,
        7
    ),
    (
        8,
        'Collection Exclusive',
        'collection-exclusive',
        'Pièces uniques en édition limitée',
        1,
        8
    );

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `first_name` varchar(100) DEFAULT NULL,
    `last_name` varchar(100) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `password_hash` varchar(255) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `birth_date` DATE NULL COMMENT 'Date de naissance de l\'utilisateur'',
    `verification_code` varchar(6) DEFAULT NULL,
    `is_verified` tinyint(1) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `birthday_email_sent` BOOLEAN DEFAULT FALSE COMMENT 'Indique si l\'email d\'anniversaire a été envoyé cette année',
    `role` enum('user', 'admin') NOT NULL DEFAULT 'user',
    `created_at` datetime DEFAULT current_timestamp(),
    `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `last_login` datetime DEFAULT NULL,
    `code_expires_at` datetime DEFAULT NULL,
    `verified_at` datetime DEFAULT NULL,
    `reset_token` varchar(255) DEFAULT NULL,
    `reset_token_expires` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `verification_code` (`verification_code`),
    KEY `reset_token` (`reset_token`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
    `product_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL,
    `slug` varchar(200) NOT NULL,
    `description` text DEFAULT NULL,
    `short_description` varchar(500) DEFAULT NULL,
    `price` decimal(10, 2) NOT NULL,
    `compare_price` decimal(10, 2) DEFAULT NULL,
    `cost_price` decimal(10, 2) DEFAULT NULL,
    `sku` varchar(100) DEFAULT NULL,
    `stock_quantity` int(11) NOT NULL DEFAULT 0,
    `min_stock_level` int(11) DEFAULT 5,
    `category_id` int(11) NOT NULL,
    `image_url` varchar(255) NOT NULL,
    `additional_images` json DEFAULT NULL,
    `is_featured` tinyint(1) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `weight` decimal(8, 2) DEFAULT NULL,
    `dimensions` varchar(100) DEFAULT NULL,
    `tags` varchar(500) DEFAULT NULL,
    `meta_title` varchar(200) DEFAULT NULL,
    `meta_description` varchar(300) DEFAULT NULL,
    `view_count` int(11) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`product_id`),
    KEY `category_id` (`category_id`),
    KEY `slug` (`slug`),
    KEY `is_active` (`is_active`),
    KEY `is_featured` (`is_featured`),
    KEY `created_at` (`created_at`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_number` varchar(50) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `status` enum(
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
        'refunded'
    ) NOT NULL DEFAULT 'pending',
    `payment_status` enum(
        'pending',
        'paid',
        'failed',
        'refunded'
    ) NOT NULL DEFAULT 'pending',
    `payment_method` varchar(50) DEFAULT NULL,
    `subtotal` decimal(10, 2) NOT NULL,
    `tax_amount` decimal(10, 2) DEFAULT 0.00,
    `shipping_amount` decimal(10, 2) DEFAULT 0.00,
    `total_amount` decimal(10, 2) NOT NULL,
    `currency` varchar(3) NOT NULL DEFAULT 'TND',
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `email` varchar(150) NOT NULL,
    `phone` varchar(30) DEFAULT NULL,
    `address` text NOT NULL,
    `city` varchar(100) NOT NULL,
    `postal_code` varchar(20) DEFAULT NULL,
    `country` varchar(100) DEFAULT NULL,
    `order_notes` text DEFAULT NULL,
    `tracking_number` varchar(100) DEFAULT NULL,
    `shipped_at` datetime DEFAULT NULL,
    `delivered_at` datetime DEFAULT NULL,
    `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `order_number` (`order_number`),
    KEY `user_id` (`user_id`),
    KEY `status` (`status`),
    KEY `order_date` (`order_date`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `price` decimal(10, 2) NOT NULL,
    `total` decimal(10, 2) NOT NULL,
    `product_name` varchar(200) NOT NULL,
    `product_image` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `order_id` (`order_id`),
    KEY `product_id` (`product_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `session_id` varchar(255) DEFAULT NULL,
    `product_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `session_id` (`session_id`),
    KEY `product_id` (`product_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `rating` int(1) NOT NULL,
    `title` varchar(200) DEFAULT NULL,
    `comment` text DEFAULT NULL,
    `is_verified_purchase` tinyint(1) DEFAULT 0,
    `is_approved` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `product_id` (`product_id`),
    KEY `user_id` (`user_id`),
    KEY `is_approved` (`is_approved`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_product` (`user_id`, `product_id`),
    KEY `product_id` (`product_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `first_name` varchar(100) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `token` varchar(255) DEFAULT NULL,
    `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `unsubscribed_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `token` (`token`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(30) DEFAULT NULL,
    `subject` varchar(200) DEFAULT NULL,
    `message` text NOT NULL,
    `status` enum('new', 'read', 'replied') NOT NULL DEFAULT 'new',
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `replied_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `status` (`status`),
    KEY `created_at` (`created_at`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `sku` varchar(100) DEFAULT NULL,
    `price` decimal(10, 2) DEFAULT NULL,
    `stock_quantity` int(11) NOT NULL DEFAULT 0,
    `attributes` json DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `product_id` (`product_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_addresses` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `title` varchar(50) DEFAULT 'Mon adresse',
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `address_line1` varchar(255) NOT NULL,
    `address_line2` varchar(255) DEFAULT NULL,
    `city` varchar(100) NOT NULL,
    `postal_code` varchar(20) NOT NULL,
    `country` varchar(100) DEFAULT 'Tunisie',
    `is_default` tinyint(1) DEFAULT 0,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `fk_user_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL,
    `setting_value` text DEFAULT NULL,
    `description` varchar(255) DEFAULT NULL,
    `type` enum(
        'text',
        'number',
        'boolean',
        'json'
    ) NOT NULL DEFAULT 'text',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Sample data for settings
--

INSERT INTO
    `settings` (
        `setting_key`,
        `setting_value`,
        `description`,
        `type`
    )
VALUES (
        'site_name',
        'Nuraya',
        'Nom du site',
        'text'
    ),
    (
        'site_description',
        'Mode élégante et accessible pour tous',
        'Description du site',
        'text'
    ),
    (
        'contact_email',
        'contact@nuraya.com',
        'Email de contact',
        'text'
    ),
    (
        'shipping_cost',
        '7.00',
        'Frais de livraison par défaut',
        'number'
    ),
    (
        'tax_rate',
        '19.0',
        'Taux de TVA par défaut',
        'number'
    ),
    (
        'currency',
        'TND',
        'Devise par défaut',
        'text'
    ),
    (
        'social_media',
        '{"facebook":"","instagram":"","twitter":"","pinterest":""}',
        'Réseaux sociaux',
        'json'
    ),
    (
        'maintenance_mode',
        '0',
        'Mode maintenance',
        'boolean'
    );

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;