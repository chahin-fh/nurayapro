<?php
// Simple script to run database improvements
require_once 'config/database.php';

echo "<h2>Rating System Database Setup</h2>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Step 1: Add helpful_count column
echo "<h3>Step 1: Adding helpful_count column...</h3>";
$sql = "ALTER TABLE `reviews` ADD COLUMN `helpful_count` INT(11) NOT NULL DEFAULT 0 AFTER `is_approved`";
if (mysqli_query($cnx, $sql)) {
    echo "<p class='success'>✓ Column added successfully</p>";
} else {
    $error = mysqli_error($cnx);
    if (strpos($error, 'Duplicate column') !== false) {
        echo "<p class='info'>ℹ Column already exists - skipping</p>";
    } else {
        echo "<p class='error'>✗ Error: " . $error . "</p>";
    }
}

// Step 2: Create indexes
echo "<h3>Step 2: Creating indexes...</h3>";

$indexes = [
    "ALTER TABLE `reviews` ADD INDEX `idx_product_approved` (`product_id`, `is_approved`)",
    "ALTER TABLE `reviews` ADD INDEX `idx_rating` (`rating`)",
    "ALTER TABLE `review_helpful` ADD INDEX `idx_review` (`review_id`)"
];

foreach ($indexes as $sql) {
    if (mysqli_query($cnx, $sql)) {
        echo "<p class='success'>✓ Index created</p>";
    } else {
        $error = mysqli_error($cnx);
        if (strpos($error, 'Duplicate key') !== false || strpos($error, 'already exists') !== false) {
            echo "<p class='info'>ℹ Index already exists - skipping</p>";
        } else {
            echo "<p class='error'>✗ Error: " . $error . "</p>";
        }
    }
}

// Step 3: Update helpful_count for existing reviews
echo "<h3>Step 3: Updating helpful_count for existing reviews...</h3>";
$sql = "UPDATE `reviews` r
        SET `helpful_count` = (
            SELECT COUNT(*) 
            FROM `review_helpful` rh 
            WHERE rh.review_id = r.id
        )";
if (mysqli_query($cnx, $sql)) {
    echo "<p class='success'>✓ Updated " . mysqli_affected_rows($cnx) . " reviews</p>";
} else {
    echo "<p class='error'>✗ Error: " . mysqli_error($cnx) . "</p>";
}

// Step 4: Drop existing triggers if they exist
echo "<h3>Step 4: Setting up triggers...</h3>";
mysqli_query($cnx, "DROP TRIGGER IF EXISTS `review_helpful_insert`");
mysqli_query($cnx, "DROP TRIGGER IF EXISTS `review_helpful_delete`");
echo "<p class='info'>ℹ Dropped old triggers (if any)</p>";

// Step 5: Create INSERT trigger
$sql = "CREATE TRIGGER `review_helpful_insert` 
        AFTER INSERT ON `review_helpful`
        FOR EACH ROW
        UPDATE `reviews` 
        SET `helpful_count` = `helpful_count` + 1 
        WHERE `id` = NEW.review_id";

if (mysqli_query($cnx, $sql)) {
    echo "<p class='success'>✓ INSERT trigger created</p>";
} else {
    echo "<p class='error'>✗ Error creating INSERT trigger: " . mysqli_error($cnx) . "</p>";
}

// Step 6: Create DELETE trigger
$sql = "CREATE TRIGGER `review_helpful_delete` 
        AFTER DELETE ON `review_helpful`
        FOR EACH ROW
        UPDATE `reviews` 
        SET `helpful_count` = `helpful_count` - 1 
        WHERE `id` = OLD.review_id";

if (mysqli_query($cnx, $sql)) {
    echo "<p class='success'>✓ DELETE trigger created</p>";
} else {
    echo "<p class='error'>✗ Error creating DELETE trigger: " . mysqli_error($cnx) . "</p>";
}

// Step 7: Verify installation
echo "<h3>Verification:</h3>";

// Check column
$result = mysqli_query($cnx, "SHOW COLUMNS FROM reviews LIKE 'helpful_count'");
if (mysqli_num_rows($result) > 0) {
    echo "<p class='success'>✓ Column 'helpful_count' exists</p>";
} else {
    echo "<p class='error'>✗ Column 'helpful_count' NOT found</p>";
}

// Check triggers
$result = mysqli_query($cnx, "SHOW TRIGGERS LIKE 'review_helpful_insert'");
if (mysqli_num_rows($result) > 0) {
    echo "<p class='success'>✓ INSERT trigger exists</p>";
} else {
    echo "<p class='error'>✗ INSERT trigger NOT found</p>";
}

$result = mysqli_query($cnx, "SHOW TRIGGERS LIKE 'review_helpful_delete'");
if (mysqli_num_rows($result) > 0) {
    echo "<p class='success'>✓ DELETE trigger exists</p>";
} else {
    echo "<p class='error'>✗ DELETE trigger NOT found</p>";
}

echo "<h3 class='success'>✓ Setup Complete!</h3>";
echo "<p><a href='product.php?id=2' style='background:#667eea;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Test on Product Page</a></p>";
echo "<p><a href='index.php' style='background:#764ba2;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Go to Home</a></p>";

mysqli_close($cnx);
?>
