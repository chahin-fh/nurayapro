<?php
require_once __DIR__ . '/src/Controllers/cnx.php';
$query = "CREATE TABLE IF NOT EXISTS `contact_messages` (
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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;";
if (mysqli_query($cnx, $query)) {
    echo "Table contact_messages created or already exists.";
} else {
    echo "Error: " . mysqli_error($cnx);
}
unlink(__FILE__);
