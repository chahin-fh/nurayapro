<?php
header('Content-Type: application/json');

// Connexion à la base de données
include '../cnx.php';

// Récupérer l'action
$action = $_POST['action'] ?? '';

if ($action === 'send') {
    sendMessage();
}

function sendMessage()
{
    global $cnx;

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Nom, email et message sont obligatoires']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide']);
        return;
    }

    if (strlen($message) < 10) {
        echo json_encode(['success' => false, 'message' => 'Le message doit contenir au moins 10 caractères']);
        return;
    }

    // Récupérer l'IP et le user agent
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Insérer le message
    $insert_query = "INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($cnx, $insert_query);
    mysqli_stmt_bind_param($stmt, 'ssssss', $name, $email, $phone, $subject, $message, $ip_address, $user_agent);

    if (mysqli_stmt_execute($stmt)) {
        // TODO: Envoyer un email de notification à l'admin

        echo json_encode([
            'success' => true,
            'message' => 'Message envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi du message']);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($cnx);
?>