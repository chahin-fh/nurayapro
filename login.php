<?php
session_start();

// Si l'utilisateur est déjà connecté, le rediriger
if (isset($_SESSION['user_id'])) {
    // Rediriger les admins vers le panneau d'administration
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-light: #F5EFE6;
            --bg-white: #FAF7F2;
            --beige-dark: #C8B6A6;
            --text-dark: #1C1C1C;
            --text-gray: #7A7A7A;
            --accent-pink: #E6B7C8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column
        }

        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px
        }

        .auth-card {
            background: var(--bg-white);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(200, 182, 166, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 450px
        }

        .auth-header {
            background: var(--beige-dark);
            color: var(--bg-white);
            padding: 30px;
            text-align: center
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px
        }

        .auth-header p {
            opacity: 0.9;
            font-size: 14px
        }

        .auth-body {
            padding: 40px 30px
        }

        .form-group {
            margin-bottom: 20px
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(200, 182, 166, 0.3);
            border-radius: 10px;
            font-size: 15px;
            background: var(--bg-white);
            transition: all 0.3s ease
        }

        .form-control:focus {
            outline: none;
            border-color: var(--beige-dark);
            box-shadow: 0 0 0 3px rgba(200, 182, 166, 0.1)
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: var(--beige-dark);
            color: var(--bg-white);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease
        }

        .btn:hover {
            background: var(--text-dark);
            transform: translateY(-2px)
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(200, 182, 166, 0.2)
        }

        .auth-footer p {
            color: var(--text-gray);
            font-size: 14px
        }

        .auth-footer a {
            color: var(--beige-dark);
            text-decoration: none;
            font-weight: 600
        }

        .auth-footer a:hover {
            text-decoration: underline
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none
        }

        .alert.show {
            display: block
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc
        }

        .password-toggle {
            position: relative
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-gray);
            cursor: pointer;
            padding: 4px
        }

        .toggle-password:hover {
            color: var(--beige-dark)
        }

        @media (max-width:480px) {
            .auth-container {
                padding: 20px 16px
            }

            .auth-body {
                padding: 30px 20px
            }
        }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Connexion</h1>
                <p>Bienvenue sur Nuraya</p>
            </div>

            <div class="auth-body">
                <div class="alert" id="alertMessage"></div>

                <form id="loginForm">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Mot de passe</label>
                        <div class="password-toggle">
                            <input type="password" id="password" name="password" class="form-control" required>
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn" id="loginBtn">
                        Se connecter
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
                    <p style="margin-top: 8px;"><a href="forgot-password.php">Mot de passe oublié ?</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        function showAlert(message, type) {
            showToast(message, type === 'error' ? 'error' : 'success');
        }

        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');

            if (!email || !password) {
                showAlert('Veuillez remplir tous les champs', 'error');
                return;
            }

            // Désactiver le bouton
            loginBtn.disabled = true;
            loginBtn.textContent = 'Connexion en cours...';

            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
            formData.append('password', password);

            fetch('api/auth.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Connexion réussie ! Redirection...', 'success');
                        setTimeout(() => {
                            // Rediriger les admins vers le panneau d'administration
                            if (data.user && data.user.role === 'admin') {
                                window.location.href = 'admin/index.php';
                            } else {
                                // Check for redirect param
                                const urlParams = new URLSearchParams(window.location.search);
                                const redirect = urlParams.get('redirect');
                                if(redirect) {
                                    window.location.href = decodeURIComponent(redirect);
                                } else {
                                    window.location.href = 'index.php';
                                }
                            }
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    showAlert('Erreur de connexion. Veuillez réessayer.', 'error');
                })
                .finally(() => {
                    // Réactiver le bouton
                    loginBtn.disabled = false;
                    loginBtn.textContent = 'Se connecter';
                });
        });
    </script>
</body>

</html>