<?php
// Middleware de vérification d'authentification admin
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Charger la connexion à la base de données
require_once __DIR__ . '/../../includes/autoload.php';
?>
