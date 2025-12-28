<?php
session_start();
include 'templates/navbar_updated.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — Nuraya</title>
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
            background: linear-gradient(135deg, var(--bg-white) 0%, #fdfbf8 100%);
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

        .btn:hover {
            background: var(--text-dark);
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(200, 182, 166, 0.2);
        }

        .auth-footer a {
            color: var(--beige-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Mot de passe oublié</h1>
                <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
            </div>

            <div class="auth-body">
                <form id="forgotForm">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required
                            placeholder="votre@email.com">
                    </div>

                    <button type="submit" class="btn" id="submitBtn">
                        Envoyer le lien
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Vous vous en souvenez ? <a href="login.php">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forgotForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const btn = document.getElementById('submitBtn');

            if (!email) return;

            btn.disabled = true;
            btn.textContent = 'Envoi en cours...';

            const formData = new FormData();
            formData.append('action', 'forgot');
            formData.append('email', email);

            fetch('api/auth.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        document.getElementById('forgotForm').reset();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Une erreur est survenue.', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = 'Envoyer le lien';
                });
        });
    </script>
</body>

</html>