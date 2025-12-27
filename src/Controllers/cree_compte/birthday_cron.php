<?php
/**
 * Script d'envoi d'emails d'anniversaire
 * À exécuter quotidiennement via cron job
 */

require_once __DIR__ . '/../../includes/autoload.php';

// Réinitialiser les flags pour les utilisateurs dont ce n'est plus l'anniversaire
resetBirthdayEmailFlags();

// Envoyer les emails d'anniversaire du jour
$result = sendDailyBirthdayEmails();

// Logger les résultats
$log_message = sprintf(
    "[%s] Emails d'anniversaire envoyés: %d succès, %d échecs, %d total",
    date('Y-m-d H:i:s'),
    count($result['sent']),
    count($result['failed']),
    $result['total']
);

log_error($log_message, 'birthday');

// Si des emails ont échoué, logger en détail
if (!empty($result['failed'])) {
    $failed_message = "Échecs d'envoi: " . implode(', ', $result['failed']);
    log_error($failed_message, 'birthday_error');
}

echo "Script terminé. Emails envoyés: " . $result['total'];
?>