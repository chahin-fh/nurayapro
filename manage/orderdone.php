<?php 
include '../cnx.php';
extract($_GET);
$done = mysqli_query($cnx , "DELETE from orders where id = '$id'");
mysqli_close($cnx);
header("Location: orderlist.php");
?>