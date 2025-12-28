<?php
require_once 'includes/autoload.php';

// Si l'utilisateur est déjà connecté, le rediriger
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — Nuraya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap">
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
        max-width: 500px
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px
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

    .password-requirements {
        margin-top: 8px;
        font-size: 12px;
        color: var(--text-gray);
        line-height: 1.4
    }

    .password-requirements ul {
        margin-left: 20px;
        margin-top: 4px
    }

    @media (max-width:480px) {
        .auth-container {
            padding: 20px 16px
        }

        .auth-body {
            padding: 30px 20px
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0
        }
    }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Inscription</h1>
                <p>Rejoignez Nuraya</p>
            </div>

            <div class="auth-body">
                <div class="alert" id="alertMessage"></div>

                <form id="registerForm" method="POST" action="javascript:void(0);">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="firstName">Prénom</label>
                            <input type="text" id="firstName" name="first_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="lastName">Nom</label>
                            <input type="text" id="lastName" name="last_name" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="birthDate">Date de naissance</label>
                            <input type="date" id="birthDate" name="birth_date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Téléphone (optionnel)</label>
                            <input type="tel" id="phone" name="phone" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Mot de passe</label>
                        <div class="password-toggle">
                            <input type="password" id="password" name="password" class="form-control" required
                                minlength="8">
                            <button type="button" class="toggle-password"
                                onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <div class="password-requirements">
                            Le mot de passe doit contenir au moins 8 caractères
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirmPassword">Confirmer le mot de passe</label>
                        <div class="password-toggle">
                            <input type="password" id="confirmPassword" name="confirm_password" class="form-control"
                                required minlength="8">
                            <button type="button" class="toggle-password"
                                onclick="togglePasswordVisibility('confirmPassword')">
                                <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn" id="registerBtn">
                        S'inscrire
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function togglePasswordVisibility(fieldId) {
        try {
            const passwordInput = document.getElementById(fieldId);
            const iconId = fieldId + 'Icon';
            const passwordIcon = document.getElementById(iconId);

            if (!passwordInput || !passwordIcon) {
                console.error('Elements not found:', {
                    fieldId,
                    passwordInput,
                    passwordIcon
                });
                return;
            }

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        } catch (error) {
            console.error('Error in togglePasswordVisibility:', error);
        }
    }

    function showAlert(message, type) {
        showToast(message, type === 'error' ? 'error' : 'success');
    }

    // Fonction de vérification par email
    function verifyEmail(email) {
        return fetch('src/Controllers/api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=verify_email&email=${encodeURIComponent(email)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                return data;
            })
            .catch(error => {
                console.error('Email verification error:', error);
                return {
                    success: false,
                    message: 'Erreur de vérification email'
                };
            });
    }

    // Fonction pour envoyer le code de vérification
    function sendVerificationCode(email, firstName = '', lastName = '') {
        return fetch('src/Controllers/api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=send_verification_code&first_name=${encodeURIComponent(firstName)}&last_name=${encodeURIComponent(lastName)}&email=${encodeURIComponent(email)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                return data;
            })
            .catch(error => {
                console.error('Send verification code error:', error);
                return {
                    success: false,
                    message: 'Erreur d\'envoi du code'
                };
            });
    }

    // Attendre que le DOM soit chargé
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('registerForm');
        if (!registerForm) {
            console.error('Register form not found');
            return;
        }

        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const birthDate = document.getElementById('birthDate').value;
            const phone = document.getElementById('phone').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const registerBtn = document.getElementById('registerBtn');

            // Validation
            if (!firstName || !lastName || !birthDate || !email || !password || !confirmPassword) {
                showAlert('Veuillez remplir tous les champs obligatoires', 'error');
                return;
            }

            if (password !== confirmPassword) {
                showAlert('Les mots de passe ne correspondent pas', 'error');
                return;
            }

            if (password.length < 8) {
                showAlert('Le mot de passe doit contenir au moins 8 caractères', 'error');
                return;
            }

            if (!email.includes('@') || !email.includes('.')) {
                showAlert('Email invalide', 'error');
                return;
            }

            // Étape 1: Vérifier si l'email existe déjà
            registerBtn.disabled = true;
            registerBtn.textContent = 'Vérification de l\'email...';

            verifyEmail(email).then(verifyResult => {
                if (!verifyResult.success) {
                    if (verifyResult.message && verifyResult.message.includes('existe déjà')) {
                        showAlert('Cet email est déjà utilisé. Veuillez vous connecter.',
                            'error');
                    } else {
                        showAlert(verifyResult.message || 'Erreur de vérification email',
                            'error');
                    }
                    registerBtn.disabled = false;
                    registerBtn.textContent = 'S\'inscrire';
                    return;
                }

                // Étape 2: Envoyer le code de vérification
                registerBtn.textContent = 'Envoi du code de vérification...';

                sendVerificationCode(email, firstName, lastName).then(sendResult => {
                    if (!sendResult.success) {
                        showAlert(sendResult.message ||
                            'Erreur d\'envoi du code de vérification', 'error');
                        registerBtn.disabled = false;
                        registerBtn.textContent = 'S\'inscrire';
                        return;
                    }

                    // Stocker les données temporaires pour la page de vérification
                    sessionStorage.setItem('temp_first_name', firstName);
                    sessionStorage.setItem('temp_last_name', lastName);
                    sessionStorage.setItem('temp_birth_date', birthDate);
                    sessionStorage.setItem('temp_phone', phone);
                    sessionStorage.setItem('temp_email', email);
                    sessionStorage.setItem('temp_password', password);

                    // Rediriger vers la page de vérification
                    showAlert(
                        `Un code de vérification a été envoyé à ${email}. Redirection...`,
                        'success');

                    setTimeout(() => {
                        window.location.href = 'verify-code.php';
                    }, 1500);
                });
            });
        });

        // Validation en temps réel de l'email
        let emailVerificationTimeout;
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();

                if (email && email.includes('@') && email.includes('.')) {
                    clearTimeout(emailVerificationTimeout);
                    emailVerificationTimeout = setTimeout(() => {
                        verifyEmail(email).then(result => {
                            if (!result.success && result.message && result.message
                                .includes('existe déjà')) {
                                showAlert('Cet email est déjà utilisé', 'error');
                                this.style.borderColor = '#e74c3c';
                            } else {
                                this.style.borderColor = '#27ae60';
                            }
                        });
                    }, 1000);
                }
            });

            // Réinitialiser la couleur de l'email lors de la modification
            emailInput.addEventListener('input', function() {
                this.style.borderColor = '';
            });
        }
    });
    </script>
</body>

</html>