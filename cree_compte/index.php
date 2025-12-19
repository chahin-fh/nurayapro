<?php
session_start();
if(isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Nuraya</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../img/brand-logo.png" alt="Logo Nuraya" style="max-width: 150px; display: block; margin: 0 auto 20px;">
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; 
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; 
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <h2 style="text-align: center;">Connexion</h2>
        <p style="text-align: center;">Entrez votre email pour recevoir un code de vérification</p>
        
        <form action="main.php" method="POST">
            <input type="email" name="mail" placeholder="Votre email" required>
            <button type="submit" name="register_btn">Continuer</button>
        </form>
    </div>
</body>
</html>