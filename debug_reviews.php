<?php
include 'config/database.php';
$res = $cnx->query("DESCRIBE reviews");
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - Default: " . $row['Default'] . "\n";
    }
} else {
    echo "Error: " . $cnx->error;
}
?>
