<?php
include 'config/database.php';

$tables = ['reviews', 'review_helpful', 'review_reports'];
$results = [];

foreach ($tables as $table) {
    if ($cnx->query("SHOW TABLES LIKE '$table'")->num_rows > 0) {
        $columns = [];
        $res = $cnx->query("SHOW COLUMNS FROM $table");
        while ($row = $res->fetch_assoc()) {
            $columns[] = $row['Field'] . ' (' . $row['Type'] . ')';
        }
        $results[$table] = "Exists. Columns: " . implode(', ', $columns);
    } else {
        $results[$table] = "Does NOT exist";
    }
}

// Check products table for is_active column
if ($cnx->query("SHOW TABLES LIKE 'products'")->num_rows > 0) {
    $res = $cnx->query("SHOW COLUMNS FROM products LIKE 'is_active'");
    if ($res->num_rows > 0) {
        $results['products_is_active'] = "Exists";
    } else {
        $results['products_is_active'] = "Does NOT exist";
    }
}

echo json_encode($results, JSON_PRETTY_PRINT);
?>
