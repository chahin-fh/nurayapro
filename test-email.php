<?php
// Fichier de test pour l'envoi d'email
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test d'envoi d'email</h1>";

// Test avec la fonction mail() native
$to = 'malekhima1f@gmail.com';
$subject = 'Test de vérification - Nuraya';
$verification_code = '123456';

$message = "
Bonjour,

Merci de vous être inscrit sur Nuraya ! Pour finaliser votre inscription, veuillez utiliser le code de vérification ci-dessous :

CODE : $verification_code

⏰ Important : Ce code expire dans 5 minutes.

Si vous n'avez pas demandé cette inscription, vous pouvez ignorer cet email.

© 2025 Nuraya. Tous droits réservés.
";

$headers = [
    'From: Nuraya <noreply@nuraya.com>',
    'Reply-To: Support Nuraya <support@nuraya.com>',
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion()
];

$headers_string = implode("\r\n", $headers);
$subject_encoded = '=?UTF-8?B?' . base64_encode($subject) . '?=';

echo "<h2>Tentative d'envoi...</h2>";
echo "<p><strong>Destinataire:</strong> $to</p>";
echo "<p><strong>Sujet:</strong> $subject_encoded</p>";
echo "<p><strong>Headers:</strong><br><pre>" . htmlspecialchars($headers_string) . "</pre></p>";

$result = mail($to, $subject_encoded, $message, $headers_string);

if ($result) {
    echo "<h2 style='color: green;'>✅ Email envoyé avec succès !</h2>";
    echo "<p>Vérifiez votre boîte de réception Gmail.</p>";
} else {
    echo "<h2 style='color: red;'>❌ Échec de l'envoi</h2>";
    echo "<p>Erreur: " . error_get_last()['message'] . "</p>";
}

echo "<h2>Configuration PHP</h2>";
echo "<p><strong>Version PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>mail.log:</strong> " . ini_get('mail.log') . "</p>";
echo "<p><strong>sendmail_path:</strong> " . ini_get('sendmail_path') . "</p>";
echo "<p><strong>SMTP:</strong> " . ini_get('SMTP') . "</p>";
echo "<p><strong>smtp_port:</strong> " . ini_get('smtp_port') . "</p>";

echo "<h2>Test de configuration XAMPP</h2>";
echo "<p>Pour que l'envoi d'email fonctionne avec XAMPP :</p>";
echo "<ol>";
echo "<li>Allez dans <strong>php.ini</strong> (C:/xampp/php/php.ini)</li>";
echo "<li>Cherchez <strong>[mail function]</strong></li>";
echo "<li>Configurez :</li>";
echo "<pre>";
echo "sendmail_path = \"C:/xampp/sendmail/sendmail.exe\" -t\n";
echo "SMTP = smtp.gmail.com\n";
echo "smtp_port = 587\n";
echo "sendmail_from = votre-email@gmail.com\n";
echo "auth_username = votre-email@gmail.com\n";
echo "auth_password = votre-mot-de-passe-app\n";
echo "</pre>";
echo "<li>Redémarrez Apache</li>";
echo "</ol>";

echo "<p><a href='register.php'>← Retour à l'inscription</a></p>";
?>