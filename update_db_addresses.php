<?php
require_once 'includes/autoload.php';

$sql = file_get_contents('create_addresses_table.sql');

if (mysqli_multi_query($cnx, $sql)) {
    echo "Table user_addresses created successfully.";
} else {
    echo "Error creating table: " . mysqli_error($cnx);
}
?>
