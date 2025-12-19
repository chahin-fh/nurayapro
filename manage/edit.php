<?php
include '../cnx.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    extract($_POST);
    if(empty($_FILES)){
    $result_del_img = mysqli_query($cnx, "SELECT image_url from products where product_id = '$id'");
    while($t = mysqli_fetch_array($result_del_img)){
        unlink($t[0]);
    }
    $img_name = $_FILES["pro_images"]["name"];
    $pro_images = "../uploads/uploaded/" . $img_name;
    move_uploaded_file($_FILES['pro_images']["tmp_name"],$pro_images);
    $result = mysqli_query($cnx, "UPDATE products SET name = '$title', description= '$description', price = '$price' , stock_quantity = '$quantity' , category_id = '$category' , image_url = '$pro_images' where product_id = '$id' ; ");
    mysqli_close($cnx);
    header('location:index.php');
    }else{
        $result = mysqli_query($cnx, "UPDATE products SET name = '$title', description= '$description', price = '$price' , stock_quantity = '$quantity' , category_id = '$category' where product_id = '$id' ; ");
        mysqli_close($cnx);
        header('location:index.php');
    }
    }