<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Test PHPMailer Complet</h1>";

// Charger PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

echo "<h2>1. Vérification de PHPMailer</h2>";
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
    'password' => 'nbgv ezhd qpkb btmk',
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
    $mail->Password = 'nbgv ezhd qpkb btmk'; // Mettre à jour le mot de passe
    $mail->SMTPSecure = 'tls';
    $mail->Port = $config['port'];

    echo "✅ Configuration SMTP appliquée<br>";

    // Configuration de l'email
    $mail->setFrom('noreply@nuraya.com', 'Nuraya');
    $mail->addAddress($config['username']); // Envoyer à soi-même pour tester
    $mail->addReplyTo('support@nuraya.com', 'Support Nuraya');

    echo "✅ Adresses email configurées<br>";

    // Contenu de l'email
    $mail->isHTML(true);
    $mail->Subject = '🧪 Test PHPMailer - Nuraya';

    $verification_code = sprintf('%06d', mt_rand(0, 999999));

    // Email HTML
    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: 'Montserrat', Arial, sans-serif; line-height: 1.6; color: #1C1C1C; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #C8B6A6 0%, #d4c4b0 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { color: #FAF7F2; margin: 0; font-size: 28px; font-weight: 700; }
            .content { background: #FAF7F2; padding: 30px; border-radius: 0 0 10px 10px; }
            .code-box { background: #F5EFE6; border: 2px solid #C8B6A6; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0; }
            .code { font-size: 32px; font-weight: 700; color: #C8B6A6; letter-spacing: 8px; font-family: monospace; }
            .info { background: #E6B7C8; color: #1C1C1C; padding: 15px; border-radius: 8px; margin: 20px 0; font-size: 14px; }
            .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #7A7A7A; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Nuraya</h1>
            </div>
            <div class='content'>
                <h2>🧪 Test de configuration PHPMailer</h2>
                <p>Ceci est un email de test pour vérifier que PHPMailer fonctionne correctement avec Gmail SMTP.</p>
                
                <div class='code-box'>
                    <div class='code'>" . $verification_code . "</div>
                </div>
                
                <div class='info'>
                    <strong>✅ Succès :</strong> PHPMailer est correctement configuré et fonctionne !
                </div>
                
                <p>Si vous recevez cet email, cela signifie que l'envoi d'emails avec PHPMailer est opérationnel.</p>
                
                <div class='footer'>
                    <p>&copy; 2025 Nuraya. Tous droits réservés.</p>
                    <p>Email de test envoyé le " . date('Y-m-d H:i:s') . "</p>
                </div>
            </div>
        </div>
    </body>
    </html>";

    // Version texte alternative
    $mail->AltBody = "Test PHPMailer - Nuraya\n\n" .
        "Ceci est un email de test pour vérifier que PHPMailer fonctionne correctement.\n\n" .
        "Code de test : " . $verification_code . "\n\n" .
        "Si vous recevez cet email, PHPMailer est opérationnel.\n\n" .
        "© 2025 Nuraya. Tous droits réservés.";

    echo "✅ Contenu email configuré<br>";

    // Envoi
    $result = $mail->send();

    if ($result) {
        echo "<h2 style='color: green;'>✅ Email envoyé avec succès !</h2>";
        echo "<p><strong>Vérifiez votre boîte Gmail</strong> pour l'email de test.</p>";
        echo "<p>Code de test généré : <strong>" . $verification_code . "</strong></p>";
        echo "<p>Envoyé à : " . $config['username'] . "</p>";
        echo "<p>Heure d'envoi : " . date('Y-m-d H:i:s') . "</p>";
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
        echo "<li>Vérifiez que le port 587 n'est pas bloqué par votre firewall</li>";
        echo "</ul>";
    }
}

echo "<h2>4. Configuration PHP</h2>";
echo "<p><strong>Version PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Extensions:</strong> " . implode(', ', get_loaded_extensions()) . "</p>";
echo "<p><strong>OpenSSL:</strong> " . (extension_loaded('openssl') ? '✅' : '❌') . "</p>";
echo "<p><strong>Socket:</strong> " . (extension_loaded('sockets') ? '✅' : '❌') . "</p>";
echo "<p><strong>mbstring:</strong> " . (extension_loaded('mbstring') ? '✅' : '❌') . "</p>";

echo "<h2>5. Test de connexion SMTP</h2>";
$socket = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 10);
if ($socket) {
    echo "✅ Connexion à smtp.gmail.com:587 réussie<br>";
    fclose($socket);
} else {
    echo "❌ Connexion échouée: $errstr ($errno)<br>";
}

echo "<p><a href='register.php'>← Retour à l'inscription</a></p>";
echo "<p><a href='api/auth_phpmailer.php'>→ Tester l'API</a></p>";
?>