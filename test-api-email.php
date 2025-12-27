<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test API Email</h1>";

// Simuler une requête POST pour tester l'envoi de code
$_POST['action'] = 'send_verification_code';
$_POST['first_name'] = 'Test';
$_POST['last_name'] = 'User';
$_POST['email'] = 'malekhima1f@gmail.com';

echo "<h2>Données de test:</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Appel de l'API...</h2>";

// Inclure et exécuter l'API
ob_start();
include 'api/auth.php';
$output = ob_get_clean();

echo "<h2>Réponse de l'API:</h2>";
echo "<pre>";
echo $output;
echo "</pre>";

echo "<h2>Session après appel:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>