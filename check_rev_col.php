<?php
include 'config/database.php';
$check = $cnx->query("SHOW COLUMNS FROM reviews LIKE 'is_approved'");
if($check->num_rows > 0) {
    echo "Column is_approved EXISTS";
} else {
    echo "Column is_approved MISSING";
}
?>
