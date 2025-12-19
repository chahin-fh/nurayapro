<?php
include '../cnx.php';
if($_SERVER['REQUEST_METHOD'] === "POST"){
    extract($_POST);
    
    // Validation for all fields except postal code and email
    $errors = array();
    
    // First Name - required, min 2 chars, only letters and spaces
    if (empty($Fname)) {
        $errors[] = "First name is required";
    } elseif (strlen(trim($Fname)) < 2) {
        $errors[] = "First name must be at least 2 characters";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $Fname)) {
        $errors[] = "First name can only contain letters, spaces, hyphens, and apostrophes";
    }
    
    // Last Name - required, min 2 chars, only letters and spaces
    if (empty($Lname)) {
        $errors[] = "Last name is required";
    } elseif (strlen(trim($Lname)) < 2) {
        $errors[] = "Last name must be at least 2 characters";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $Lname)) {
        $errors[] = "Last name can only contain letters, spaces, hyphens, and apostrophes";
    }
    
    // Address - required, min 5 chars
    if (empty($addr)) {
        $errors[] = "Address is required";
    } elseif (strlen(trim($addr)) < 5) {
        $errors[] = "Address must be at least 5 characters";
    }
    
    // City - required, min 2 chars, only letters and spaces
    if (empty($city)) {
        $errors[] = "City is required";
    } elseif (strlen(trim($city)) < 2) {
        $errors[] = "City must be at least 2 characters";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/", $city)) {
        $errors[] = "City can only contain letters, spaces, hyphens, and apostrophes";
    }
    
    // Phone - required, must be valid phone format (10-15 digits, may contain +, -, space)
    if (empty($tel)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match("/^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,9}$/", str_replace(array(' ', '-', '.'), '', $tel))) {
        $errors[] = "Please enter a valid phone number (10-15 digits)";
    }
    
    // If there are validation errors, redirect back with error message
    if (!empty($errors)) {
        $_SESSION['order_errors'] = $errors;
        $_SESSION['order_data'] = $_POST; // Store form data to repopulate
        header("Location: main.php?" . http_build_query($_POST));
        exit;
    }
    
    // If no errors, proceed with insertion
    $list_ids = "/";
    $list_qua = "/";
    foreach($idp as $id){
        $list_ids = $list_ids . $id . "/" ;
    }
    foreach($qua as $q){
        $list_qua = $list_qua . $q . "/" ;
    }
    
    // Sanitize inputs
    $Fname = mysqli_real_escape_string($cnx, trim($Fname));
    $Lname = mysqli_real_escape_string($cnx, trim($Lname));
    $addr = mysqli_real_escape_string($cnx, trim($addr));
    $codep = mysqli_real_escape_string($cnx, trim($codep));
    $city = mysqli_real_escape_string($cnx, trim($city));
    $tel = mysqli_real_escape_string($cnx, trim($tel));
    $email = mysqli_real_escape_string($cnx, trim($email));
    
    $result = mysqli_query($cnx , "INSERT into orders (Fname,Lname,addr,codep,city,tel,email,list_qua,list_ids) values('$Fname','$Lname','$addr','$codep','$city','$tel','$email','$list_qua','$list_ids');");
    
    if ($result) {
        header("Location: done");
    } else {
        $_SESSION['order_errors'] = array("Database error: " . mysqli_error($cnx));
        header("Location: main.php?" . http_build_query($_POST));
    }
    mysqli_close($cnx);
}