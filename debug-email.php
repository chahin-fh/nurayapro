<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug Envoi Email PHPMailer</h1>";

// Charger PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<h2>1. Test de chargement PHPMailer</h2>";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer chargé avec succès<br>";
} else {
    echo "❌ PHPMailer non trouvé<br>";
    exit;
}

echo "<h2>2. Configuration SMTP</h2>";
$config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'malekhima1f@gmail.com',
    'password' => 'hvvj xmfl lvzu qbzb',
    'encryption' => 'tls'
];

echo "Host: " . $config['host'] . "<br>";
echo "Port: " . $config['port'] . "<br>";
echo "Username: " . $config['username'] . "<br>";
echo "Password: " . str_repeat('*', strlen($config['password'])) . "<br>";

echo "<h2>3. Test d'envoi</h2>";

try {
    $mail = new PHPMailer(true);

    echo "✅ Instance PHPMailer créée<br>";

    // Configuration du serveur SMTP
    $mail->isSMTP();
    $mail->Host = $config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['username'];
    $mail->Password = $config['password'];
    $mail->SMTPSecure = $config['encryption'];
    $mail->Port = $config['port'];

    echo "✅ Configuration SMTP appliquée<br>";

    // Configuration de l'email
    $mail->setFrom('noreply@nuraya.com', 'Nuraya');
    $mail->addAddress($config['username']); // Envoyer à soi-même pour tester
    $mail->addReplyTo('support@nuraya.com', 'Support Nuraya');

    echo "✅ Adresses email configurées<br>";

    // Contenu de l'email
    $mail->isHTML(true);
    $mail->Subject = '🧪 Test Debug - Nuraya';
    $mail->Body = "<h1>Test de debug</h1><p>Ceci est un test de configuration PHPMailer.</p>";
    $mail->AltBody = "Test de debug - Ceci est un test de configuration PHPMailer.";

    echo "✅ Contenu email configuré<br>";

    // Envoi
    $result = $mail->send();

    if ($result) {
        echo "<h2 style='color: green;'>✅ Email envoyé avec succès !</h2>";
        echo "<p>Vérifiez votre boîte de réception Gmail.</p>";
    } else {
        echo "<h2 style='color: red;'>❌ Échec de l'envoi</h2>";
    }

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erreur PHPMailer</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Code:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Fichier:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";

    if (strpos($e->getMessage(), 'SMTP') !== false) {
        echo "<h3>🔧 Solutions possibles pour erreur SMTP:</h3>";
        echo "<ul>";
        echo "<li>Vérifiez que l'authentification 2FA est activée sur Gmail</li>";
        echo "<li>Vérifiez le mot de passe d'application Gmail</li>";
        echo "<li>Assurez-vous que 'Accès aux applications moins sécurisées' est activé</li>";
        echo "<li>Vérifiez que le port 587 n'est pas bloqué</li>";
        echo "</ul>";
    }
}

echo "<h2>4. Configuration PHP</h2>";
echo "<p><strong>Version PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Extensions:</strong> " . implode(', ', get_loaded_extensions()) . "</p>";
echo "<p><strong>OpenSSL:</strong> " . (extension_loaded('openssl') ? '✅' : '❌') . "</p>";
echo "<p><strong>Socket:</strong> " . (extension_loaded('sockets') ? '✅' : '❌') . "</p>";

echo "<h2>5. Test de connexion SMTP</h2>";
$socket = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 10);
if ($socket) {
    echo "✅ Connexion à smtp.gmail.com:587 réussie<br>";
    fclose($socket);
} else {
    echo "❌ Connexion échouée: $errstr ($errno)<br>";
}

echo "<p><a href='register.php'>← Retour à l'inscription</a></p>";
?>