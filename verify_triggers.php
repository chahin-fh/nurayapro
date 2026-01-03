
<?php
// Verify triggers setup
require_once 'config/database.php';

echo "<h2>Trigger Verification & Fix</h2>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Check all triggers in database
echo "<h3>Checking existing triggers...</h3>";
$result = mysqli_query($cnx, "SHOW TRIGGERS FROM nurayapro");
$foundTriggers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $foundTriggers[] = $row['Trigger'];
    echo "<p class='info'>Found trigger: " . $row['Trigger'] . " on table " . $row['Table'] . "</p>";
}

// Check if our specific triggers exist
$requiredTriggers = ['review_helpful_insert', 'review_helpful_delete'];
foreach ($requiredTriggers as $trigger) {
    if (in_array($trigger, $foundTriggers)) {
        echo "<p class='success'>✓ Trigger '$trigger' exists</p>";
    } else {
        echo "<p class='error'>✗ Trigger '$trigger' NOT found - creating it...</p>";
        
        // Create missing trigger
        mysqli_query($cnx, "DROP TRIGGER IF EXISTS `$trigger`");
        
        if ($trigger == 'review_helpful_insert') {
            $sql = "CREATE TRIGGER `review_helpful_insert` 
                    AFTER INSERT ON `review_helpful`
                    FOR EACH ROW
                    UPDATE `reviews` 
                    SET `helpful_count` = `helpful_count` + 1 
                    WHERE `id` = NEW.review_id";
        } else {
            $sql = "CREATE TRIGGER `review_helpful_delete` 
                    AFTER DELETE ON `review_helpful`
                    FOR EACH ROW
                    UPDATE `reviews` 
                    SET `helpful_count` = `helpful_count` - 1 
                    WHERE `id` = OLD.review_id";
        }
        
        if (mysqli_query($cnx, $sql)) {
            echo "<p class='success'>✓ Created trigger '$trigger'</p>";
        } else {
            echo "<p class='error'>✗ Failed to create: " . mysqli_error($cnx) . "</p>";
        }
    }
}

// Final verification
echo "<h3>Final Status:</h3>";
$result = mysqli_query($cnx, "SHOW TRIGGERS FROM nurayapro");
$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
    if (in_array($row['Trigger'], $requiredTriggers)) {
        $count++;
    }
}

if ($count >= 2) {
    echo "<h3 class='success'>✓✓✓ All triggers are in place and working!</h3>";
} else {
    echo "<h3 class='error'>Still missing some triggers</h3>";
}

// Test the system
echo "<h3>Test Information:</h3>";
$result = mysqli_query($cnx, "SELECT COUNT(*) as total FROM reviews WHERE is_approved = 1");
$row = mysqli_fetch_assoc($result);
echo "<p>Total approved reviews: " . $row['total'] . "</p>";

$result = mysqli_query($cnx, "SELECT COUNT(*) as total FROM review_helpful");
$row = mysqli_fetch_assoc($result);
echo "<p>Total helpful votes: " . $row['total'] . "</p>";

echo "<hr>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li><a href='product.php?id=2'>View product page with reviews</a></li>";
echo "<li>Try clicking the 'Utile' button on a review</li>";
echo "<li>Watch the count update in real-time!</li>";
echo "</ol>";

mysqli_close($cnx);
?>
