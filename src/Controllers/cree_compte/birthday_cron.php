<?php
/**
 * Script d'envoi d'emails d'anniversaire
 * À exécuter quotidiennement via cron job
 */

// Fix path to include autoload (adjusting for directory depth)
require_once __DIR__ . '/../../../includes/autoload.php';

// Ensure PHPMailer autoload is included if not covered by main autoload
if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
}

/**
 * Réinitialiser les flags d'envoi d'email d'anniversaire
 * (Pour permettre l'envoi l'année suivante)
 */
if (!function_exists('resetBirthdayEmailFlags')) {
    function resetBirthdayEmailFlags() {
        global $cnx;
        
        // Réinitialiser pour les utilisateurs dont ce n'est plus leur anniversaire aujourd'hui
        // Cela permet de remettre birthday_email_sent à 0 pour l'année prochaine
        $query = "UPDATE users 
                  SET birthday_email_sent = 0 
                  WHERE DATE_FORMAT(birth_date, '%m-%d') != DATE_FORMAT(NOW(), '%m-%d')";
        
        if ($cnx) {
            return mysqli_query($cnx, $query);
        }
        return false;
    }
}

/**
 * Vérifier et envoyer les emails d'anniversaire du jour
 */
if (!function_exists('sendDailyBirthdayEmails')) {
    function sendDailyBirthdayEmails() {
        global $cnx;
        
        if (!$cnx) {
            return ['sent' => [], 'failed' => ['Database connection error'], 'total' => 0];
        }

        $today = date('m-d'); // Format mois-jour

        // Sélectionner les utilisateurs nés ce jour qui n'ont pas encore reçu d'email
        $query = "SELECT id, first_name, email, birth_date 
                  FROM users 
                  WHERE DATE_FORMAT(birth_date, '%m-%d') = '$today' 
                  AND birthday_email_sent = 0 
                  AND is_active = 1";

        $result = mysqli_query($cnx, $query);
        $sentEmails = [];
        $failedEmails = [];

        if ($result && mysqli_num_rows($result) > 0) {
            while ($user = mysqli_fetch_assoc($result)) {
                $userName = $user['first_name'];
                $userEmail = $user['email'];
                $userId = $user['id'];

                // Utilise la fonction sendBirthdayEmail qui doit être définie dans functions.php ou ailleurs
                // Si elle n'existe pas, on loggue une erreur
                if (function_exists('sendBirthdayEmail')) {
                    if (sendBirthdayEmail($userEmail, $userName)) {
                        // Marquer l'email comme envoyé
                        $updateQuery = "UPDATE users SET birthday_email_sent = 1 WHERE id = $userId";
                        mysqli_query($cnx, $updateQuery);
                        $sentEmails[] = $userEmail;
                    } else {
                        $failedEmails[] = $userEmail;
                    }
                } else {
                    $failedEmails[] = "$userEmail (function sendBirthdayEmail missing)";
                    error_log("Function sendBirthdayEmail not found in birthday_cron.php");
                }
            }
        }

        return [
            'sent' => $sentEmails,
            'failed' => $failedEmails,
            'total' => count($sentEmails) + count($failedEmails)
        ];
    }
}

// Exécution du script

// 1. Réinitialiser les flags
resetBirthdayEmailFlags();

// 2. Envoyer les emails
$result = sendDailyBirthdayEmails();

// 3. Logger les résultats
$log_message = sprintf(
    "[%s] Emails d'anniversaire envoyés: %d succès, %d échecs, %d total",
    date('Y-m-d H:i:s'),
    count($result['sent']),
    count($result['failed']),
    $result['total']
);

if (function_exists('log_error')) {
    log_error($log_message, 'birthday');
} else {
    // Fallback simple si log_error n'est pas dispo
    echo $log_message . "\n";
}

// Si des emails ont échoué, logger en détail
if (!empty($result['failed'])) {
    $failed_message = "Échecs d'envoi: " . implode(', ', $result['failed']);
    if (function_exists('log_error')) {
        log_error($failed_message, 'birthday_error');
    }
}

echo "Script terminé. " . $result['total'] . " traités.\n";
echo "Succès: " . count($result['sent']) . "\n";
echo "Échecs: " . count($result['failed']) . "\n";
?>