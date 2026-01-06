<?php
/**
 * Script pour supprimer toutes les fonctionnalités de TVA
 * de la base de données NURAYA
 * Date: 2026-01-06
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Suppression de la TVA - NURAYA</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #C8B6A6;
            border-bottom: 2px solid #C8B6A6;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .step h3 {
            margin-top: 0;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🗑️ Suppression des fonctionnalités de TVA</h1>";

echo "<div class='info'>
        <strong>ℹ️ Information :</strong><br>
        Ce script va supprimer toutes les références à la TVA dans la base de données.
      </div>";

$errors = [];
$success = [];

// Étape 1 : Vérifier si la colonne tax_amount existe
echo "<div class='step'>
        <h3>Étape 1 : Vérification de la colonne tax_amount</h3>";

$check_column = "SHOW COLUMNS FROM `orders` LIKE 'tax_amount'";
$result = mysqli_query($cnx, $check_column);

if (mysqli_num_rows($result) > 0) {
    echo "<p>✅ La colonne 'tax_amount' existe dans la table orders.</p>";
    
    // Supprimer la colonne
    $drop_column = "ALTER TABLE `orders` DROP COLUMN `tax_amount`";
    if (mysqli_query($cnx, $drop_column)) {
        $success[] = "Colonne 'tax_amount' supprimée avec succès de la table orders.";
        echo "<div class='success'>✅ Colonne 'tax_amount' supprimée avec succès.</div>";
    } else {
        $errors[] = "Erreur lors de la suppression de la colonne 'tax_amount': " . mysqli_error($cnx);
        echo "<div class='error'>❌ Erreur : " . mysqli_error($cnx) . "</div>";
    }
} else {
    echo "<p>ℹ️ La colonne 'tax_amount' n'existe pas (déjà supprimée ou inexistante).</p>";
}
echo "</div>";

// Étape 2 : Supprimer le paramètre tax_rate
echo "<div class='step'>
        <h3>Étape 2 : Suppression du paramètre tax_rate</h3>";

$delete_setting = "DELETE FROM `settings` WHERE `key` = 'tax_rate'";
if (mysqli_query($cnx, $delete_setting)) {
    $affected = mysqli_affected_rows($cnx);
    if ($affected > 0) {
        $success[] = "Paramètre 'tax_rate' supprimé des settings.";
        echo "<div class='success'>✅ Paramètre 'tax_rate' supprimé ($affected ligne(s)).</div>";
    } else {
        echo "<p>ℹ️ Le paramètre 'tax_rate' n'existe pas dans les settings.</p>";
    }
} else {
    $errors[] = "Erreur lors de la suppression du paramètre tax_rate: " . mysqli_error($cnx);
    echo "<div class='error'>❌ Erreur : " . mysqli_error($cnx) . "</div>";
}
echo "</div>";

// Étape 3 : Mettre à jour les totaux existants
echo "<div class='step'>
        <h3>Étape 3 : Recalcul des totaux des commandes existantes</h3>";

$update_totals = "UPDATE `orders` 
                  SET `total_amount` = `subtotal` + IFNULL(`shipping_amount`, 0)
                  WHERE `total_amount` > 0";

if (mysqli_query($cnx, $update_totals)) {
    $affected = mysqli_affected_rows($cnx);
    $success[] = "Totaux recalculés pour $affected commande(s).";
    echo "<div class='success'>✅ Totaux recalculés pour $affected commande(s).</div>";
} else {
    $errors[] = "Erreur lors du recalcul des totaux: " . mysqli_error($cnx);
    echo "<div class='error'>❌ Erreur : " . mysqli_error($cnx) . "</div>";
}
echo "</div>";

// Résumé final
echo "<div style='margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;'>
        <h2>📊 Résumé</h2>";

if (count($success) > 0) {
    echo "<div class='success'><strong>✅ Opérations réussies:</strong><ul>";
    foreach ($success as $msg) {
        echo "<li>$msg</li>";
    }
    echo "</ul></div>";
}

if (count($errors) > 0) {
    echo "<div class='error'><strong>❌ Erreurs rencontrées:</strong><ul>";
    foreach ($errors as $msg) {
        echo "<li>$msg</li>";
    }
    echo "</ul></div>";
}

if (count($errors) === 0) {
    echo "<div class='success'>
            <strong>🎉 Succès !</strong><br>
            Toutes les fonctionnalités de TVA ont été supprimées avec succès.<br><br>
            <strong>Modifications effectuées :</strong>
            <ul>
                <li>✅ Colonne 'tax_amount' supprimée de la table orders</li>
                <li>✅ Paramètre 'tax_rate' supprimé des settings</li>
                <li>✅ Totaux des commandes recalculés sans TVA</li>
                <li>✅ Code PHP mis à jour (cart.php, checkout.php, order-confirmation.php, api/orders.php)</li>
                <li>✅ Page CGV mise à jour (terms.php)</li>
            </ul>
          </div>";
}

echo "</div>";

echo "<div style='margin-top: 20px; text-align: center;'>
        <a href='index.php' style='display: inline-block; padding: 12px 24px; background: #C8B6A6; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>
            Retour à l'accueil
        </a>
      </div>";

echo "</div></body></html>";

mysqli_close($cnx);
?>
