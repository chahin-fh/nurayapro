<?php
/**
 * Pont pour la connexion à la base de données
 * Ce fichier est utilisé par les anciens contrôleurs qui pointaient vers ../cnx.php
 */
require_once __DIR__ . '/../../config/database.php';

// Si $cnx n'est pas définie dans database.php mais qu'on a besoin d'elle ici
if (!isset($cnx)) {
    // Les paramètres devraient être dans config/database.php
    // Mais si database.php ne fait qu'ouvrir la connexion, alors c'est bon
}
?>
