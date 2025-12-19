<?php
session_start();

// Admin credentials (should be moved to environment variables or config file in production)
$ADMIN_USERNAME = 'nuraya11220365';
$ADMIN_PASSWORD = 'nuraya11220365';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $ADMIN_USERNAME && $password === $ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_login_time'] = time();
        header('Location: upload-product');
        exit();
    } else {
        echo "<script>alert('Invalid credentials');</script>";
        header('Refresh: 2; url=index.html');
        exit();
    }
}
?>