-- Database improvements for rating and review system
-- Run this SQL to enhance the existing database

-- Add helpful_count column to reviews table for performance
ALTER TABLE `reviews` 
ADD COLUMN `helpful_count` INT(11) NOT NULL DEFAULT 0 AFTER `is_approved`;

-- Create index for better query performance
ALTER TABLE `reviews` 
ADD INDEX `idx_product_approved` (`product_id`, `is_approved`),
ADD INDEX `idx_rating` (`rating`);

-- Add index to review_helpful for better performance
ALTER TABLE `review_helpful`
ADD INDEX `idx_review` (`review_id`);

-- Update helpful_count for existing reviews
UPDATE `reviews` r
SET `helpful_count` = (
    SELECT COUNT(*) 
    FROM `review_helpful` rh 
    WHERE rh.review_id = r.id
);

-- Create trigger to automatically update helpful_count when vote is added
DELIMITER $$
CREATE TRIGGER `review_helpful_insert` 
AFTER INSERT ON `review_helpful`
FOR EACH ROW
BEGIN
    UPDATE `reviews` 
    SET `helpful_count` = `helpful_count` + 1 
    WHERE `id` = NEW.review_id;
END$$

-- Create trigger to automatically update helpful_count when vote is removed
CREATE TRIGGER `review_helpful_delete` 
AFTER DELETE ON `review_helpful`
FOR EACH ROW
BEGIN
    UPDATE `reviews` 
    SET `helpful_count` = `helpful_count` - 1 
    WHERE `id` = OLD.review_id;
END$$
DELIMITER ;

-- Add response column for admin replies to reviews (optional feature)
ALTER TABLE `reviews`
ADD COLUMN `admin_response` TEXT DEFAULT NULL AFTER `comment`,
ADD COLUMN `admin_response_date` DATETIME DEFAULT NULL AFTER `admin_response`;
