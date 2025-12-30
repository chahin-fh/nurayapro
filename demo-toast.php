<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Démo Toast Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/toast.css">
    <style>
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg, #F5EFE6 0%, #FAF7F2 100%);
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .demo-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(189, 161, 138, 0.15);
        }

        .demo-title {
            text-align: center;
            color: #1C1C1C;
            margin-bottom: 10px;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .demo-subtitle {
            text-align: center;
            color: #7A7A7A;
            margin-bottom: 40px;
            font-size: 16px;
        }

        .demo-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .demo-btn {
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Montserrat', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-error {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #ffb300);
            color: #212529;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .demo-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .demo-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #BDA18A;
            margin-top: 20px;
        }

        .demo-info h3 {
            color: #1C1C1C;
            margin-top: 0;
            font-size: 18px;
        }

        .demo-info p {
            color: #7A7A7A;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .responsive-test {
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #FAF7F2, #F5EFE6);
            border-radius: 8px;
        }

        .responsive-test h4 {
            color: #1C1C1C;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .responsive-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .responsive-btn {
            padding: 8px 16px;
            border: 1px solid #BDA18A;
            background: white;
            color: #BDA18A;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .responsive-btn:hover {
            background: #BDA18A;
            color: white;
        }
    </style>
</head>

<body>
    <div class="demo-container">
        <h1 class="demo-title">NURAYA</h1>
        <p class="demo-subtitle">Système de Notifications Toast Unifié</p>

        <div class="demo-buttons">
            <button class="demo-btn btn-success"
                onclick="showToast('Inscription réussie ! Bienvenue chez Nuraya.', 'success')">
                <i class="fas fa-check-circle"></i>
                Succès
            </button>

            <button class="demo-btn btn-error"
                onclick="showToast('Erreur lors de la connexion. Veuillez vérifier vos identifiants.', 'error')">
                <i class="fas fa-times-circle"></i>
                Erreur
            </button>

            <button class="demo-btn btn-warning"
                onclick="showToast('Attention : Votre session expire dans 5 minutes.', 'warning')">
                <i class="fas fa-exclamation-triangle"></i>
                Attention
            </button>

            <button class="demo-btn btn-info"
                onclick="showToast('Un email de vérification a été envoyé à votre adresse.', 'info')">
                <i class="fas fa-info-circle"></i>
                Information
            </button>
        </div>

        <div class="demo-info">
            <h3>🎨 Caractéristiques du Design</h3>
            <p>
                • <strong>Design Nuraya</strong> : Dégradés et couleurs cohérentes avec l'identité visuelle<br>
                • <strong>Responsive</strong> : Adaptation parfaite sur mobile, tablette et desktop<br>
                • <strong>Animations fluides</strong> : Transitions élégantes avec easing personnalisé<br>
                • <strong>Accessibilité</strong> : Icônes claires et contrastes respectés<br>
                • <strong>Multi-toasts</strong> : Supporte plusieurs notifications simultanées
            </p>
        </div>

        <div class="responsive-test">
            <h4>📱 Test Responsive (Redimensionnez la fenêtre)</h4>
            <div class="responsive-buttons">
                <button class="responsive-btn" onclick="showToast('Message pour mobile', 'info')">Mobile</button>
                <button class="responsive-btn" onclick="showToast('Message pour tablette', 'warning')">Tablette</button>
                <button class="responsive-btn" onclick="showToast('Message pour desktop', 'success')">Desktop</button>
                <button class="responsive-btn"
                    onclick="showToast('Test de message très long qui devrait automatiquement se retourner à la ligne sur les petits écrans pour garantir une lisibilité optimale', 'error')">Message
                    Long</button>
            </div>
        </div>
    </div>

    <script src="assets/js/toast.js"></script>
</body>

</html>