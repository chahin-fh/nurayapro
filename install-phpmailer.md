# Installation de PHPMailer

Pour installer PHPMailer, exécutez la commande suivante dans le terminal à la racine du projet :

```bash
composer require phpmailer/phpmailer
```

Si Composer n'est pas installé :

1. **Téléchargez Composer** : https://getcomposer.org/download/
2. **Installez-le** globalement
3. **Exécutez** la commande ci-dessus

## Configuration Gmail

Pour utiliser Gmail avec PHPMailer :

1. **Activez la vérification en 2 étapes** sur votre compte Gmail
2. **Créez un mot de passe d'application** :
   - Allez dans : https://myaccount.google.com/apppasswords
   - Sélectionnez "Autre" comme application
   - Donnez un nom (ex: "Nuraya Email")
   - Copiez le mot de passe généré (16 caractères)
3. **Configurez** le fichier `config/email.php` avec vos identifiants

## Alternative : MailHog (Développement)

Pour le développement local sans envoi réel :

```bash
composer require phpmailer/phpmailer
# Utiliser MailHog pour intercepter les emails
```

## Test d'envoi

Après installation, testez avec :

```php
<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
// ... votre code de test
?>
```
