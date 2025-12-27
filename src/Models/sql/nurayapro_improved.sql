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
--
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
    `verification_code` varchar(6) DEFAULT NULL,
    `is_verified` tinyint(1) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `role` enum('user', 'admin') NOT NULL DEFAULT 'user',
    `created_at` datetime DEFAULT current_timestamp(),
    `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `last_login` datetime DEFAULT NULL,
    `code_expires_at` datetime DEFAULT NULL,
    `verified_at` datetime DEFAULT NULL,
    `reset_token` varchar(255) DEFAULT NULL,
    `reset_token_expires` datetime DEFAULT NULL
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
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    `unsubscribed_at` datetime DEFAULT NULL
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
    `replied_at` datetime DEFAULT NULL
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
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `key` varchar(100) NOT NULL,
    `value` text DEFAULT NULL,
    `description` varchar(255) DEFAULT NULL,
    `type` enum(
        'text',
        'number',
        'boolean',
        'json'
    ) NOT NULL DEFAULT 'text',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
ADD PRIMARY KEY (`category_id`),
ADD UNIQUE KEY `slug` (`slug`),
ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `email` (`email`),
ADD KEY `verification_code` (`verification_code`),
ADD KEY `reset_token` (`reset_token`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
ADD PRIMARY KEY (`product_id`),
ADD KEY `category_id` (`category_id`),
ADD KEY `slug` (`slug`),
ADD KEY `is_active` (`is_active`),
ADD KEY `is_featured` (`is_featured`),
ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `order_number` (`order_number`),
ADD KEY `user_id` (`user_id`),
ADD KEY `status` (`status`),
ADD KEY `order_date` (`order_date`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
ADD PRIMARY KEY (`id`),
ADD KEY `order_id` (`order_id`),
ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
ADD PRIMARY KEY (`id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `session_id` (`session_id`),
ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
ADD PRIMARY KEY (`id`),
ADD KEY `product_id` (`product_id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `is_approved` (`is_approved`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `user_product` (`user_id`, `product_id`),
ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `email` (`email`),
ADD KEY `token` (`token`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
ADD PRIMARY KEY (`id`),
ADD KEY `status` (`status`),
ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
ADD PRIMARY KEY (`id`),
ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `key` (`key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Sample data for settings
--

INSERT INTO
    `settings` (
        `key`,
        `value`,
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