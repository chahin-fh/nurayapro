<?php
session_start();
require_once 'src/Controllers/cnx.php';

$token = $_GET['token'] ?? '';
$isValidToken = false;

if (!empty($token)) {
    $token_esc = mysqli_real_escape_string($cnx, $token);
    $query = "SELECT id FROM users WHERE reset_token = '$token_esc' AND reset_token_expires > NOW()";
    $result = mysqli_query($cnx, $query);
    if (mysqli_num_rows($result) > 0) {
        $isValidToken = true;
    }
}

include 'templates/navbar_updated.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe — Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/toast.css">
    <script src="assets/js/toast.js"></script>
    <style>
        :root {
            --bg-light: #F5EFE6;
            --bg-white: #FAF7F2;
            --beige-dark: #C8B6A6;
            --text-dark: #1C1C1C;
            --text-gray: #7A7A7A;
            --error-red: #e74c3c;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg-light);
            margin: 0;
            color: var(--text-dark);
        }

        .auth-container {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-card {
            background: var(--bg-white);
            width: 100%;
            max-width: 450px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(200, 182, 166, 0.2);
            overflow: hidden;
        }

        .auth-header {
            padding: 40px 40px 20px;
            text-align: center;
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .auth-header p {
            color: var(--text-gray);
            font-size: 14px;
        }

        .auth-body {
            padding: 0 40px 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(200, 182, 166, 0.3);
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
            background: var(--bg-white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--beige-dark);
            box-shadow: 0 0 0 4px rgba(200, 182, 166, 0.1);
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: var(--beige-dark);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover:not(:disabled) {
            background: var(--text-dark);
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .error-state {
            text-align: center;
            padding: 40px;
        }

        .error-state i {
            font-size: 48px;
            color: var(--error-red);
            margin-bottom: 20px;
        }

        .error-state p {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <?php if ($isValidToken): ?>
                <div class="auth-header">
                    <h1>Nouveau mot de passe</h1>
                    <p>Choisissez un mot de passe sécurisé pour votre compte.</p>
                </div>

                <div class="auth-body">
                    <form id="resetForm">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="form-group">
                            <label class="form-label" for="password">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required minlength="8">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirmer le mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn" id="submitBtn">
                            Mettre à jour
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="error-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <h2>Lien invalide</h2>
                    <p>Ce lien de réinitialisation est invalide ou a expiré.</p>
                    <a href="forgot-password.php" class="btn" style="display: block; text-decoration: none;">Demander un nouveau lien</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        if (document.getElementById('resetForm')) {
            document.getElementById('resetForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const password = document.getElementById('password').value;
                const confirm = document.getElementById('confirm_password').value;
                const btn = document.getElementById('submitBtn');
                
                if (password !== confirm) {
                    showToast('Les mots de passe ne correspondent pas', 'error');
                    return;
                }

                if (password.length < 8) {
                    showToast('Le mot de passe doit contenir au moins 8 caractères', 'error');
                    return;
                }
                
                btn.disabled = true;
                btn.textContent = 'Mise à jour...';
                
                const formData = new FormData(this);
                formData.append('action', 'reset');
                
                fetch('src/Controllers/api/auth.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showToast(data.message, 'error');
                        btn.disabled = false;
                        btn.textContent = 'Mettre à jour';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Une erreur est survenue.', 'error');
                    btn.disabled = false;
                    btn.textContent = 'Mettre à jour';
                });
            });
        }
    </script>
</body>
</html>
