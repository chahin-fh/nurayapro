<?php
include 'includes/functions.php';

// Vérifier si l'utilisateur est déjà connecté
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

// Vérifier si les données de vérification existent en session
if (!isset($_SESSION['temp_verification_email']) || !isset($_SESSION['temp_verification_code'])) {
    header('Location: register.php');
    exit;
}

$email = $_SESSION['temp_verification_email'];
$code_expires = $_SESSION['temp_verification_expires'];
$remaining_time = strtotime($code_expires) - time();

// Si le code a expiré, rediriger vers l'inscription
if ($remaining_time <= 0) {
    unset($_SESSION['temp_verification_code']);
    unset($_SESSION['temp_verification_email']);
    unset($_SESSION['temp_verification_expires']);
    header('Location: register.php?error=code_expired');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du Code — Nuraya</title>
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
            --success-green: #27ae60;
            --error-red: #e74c3c;
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

        .verify-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px
        }

        .verify-card {
            background: var(--bg-white);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(200, 182, 166, 0.15);
            max-width: 450px;
            width: 100%;
            overflow: hidden
        }

        .verify-header {
            background: linear-gradient(135deg, var(--beige-dark) 0%, #d4c4b0 100%);
            padding: 40px 30px;
            text-align: center;
            color: var(--bg-white)
        }

        .verify-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px
        }

        .verify-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px
        }

        .verify-subtitle {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.4
        }

        .verify-body {
            padding: 40px 30px
        }

        .email-display {
            background: var(--bg-light);
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            border: 1px solid rgba(200, 182, 166, 0.2)
        }

        .email-label {
            font-size: 12px;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px
        }

        .email-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark)
        }

        .timer-container {
            text-align: center;
            margin-bottom: 30px
        }

        .timer-label {
            font-size: 12px;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px
        }

        .timer {
            font-size: 32px;
            font-weight: 700;
            color: var(--beige-dark);
            font-variant-numeric: tabular-nums
        }

        .timer.expired {
            color: var(--error-red)
        }

        .timer.warning {
            color: #f39c12
        }

        .code-input-container {
            margin-bottom: 30px
        }

        .code-label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px
        }

        .code-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px
        }

        .code-input {
            width: 50px;
            height: 60px;
            border: 2px solid rgba(200, 182, 166, 0.3);
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
            background: var(--bg-white);
            transition: all 0.3s ease;
            font-family: 'Montserrat', monospace
        }

        .code-input:focus {
            outline: none;
            border-color: var(--beige-dark);
            box-shadow: 0 0 0 3px rgba(200, 182, 166, 0.1);
            transform: scale(1.05)
        }

        .code-input.filled {
            border-color: var(--success-green);
            background: rgba(39, 174, 96, 0.05)
        }

        .code-input.error {
            border-color: var(--error-red);
            background: rgba(231, 76, 60, 0.05);
            animation: shake 0.5s
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0)
            }

            25% {
                transform: translateX(-5px)
            }

            75% {
                transform: translateX(5px)
            }
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: var(--beige-dark);
            color: var(--bg-white);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative
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

        .resend-container {
            text-align: center;
            margin-top: 20px
        }

        .resend-text {
            color: var(--text-gray);
            font-size: 14px;
            margin-bottom: 10px
        }

        .resend-link {
            color: var(--beige-dark);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s ease
        }

        .resend-link:hover {
            border-bottom-color: var(--beige-dark)
        }

        .resend-link:disabled {
            color: var(--text-gray);
            cursor: not-allowed;
            border-bottom-color: transparent
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

        .back-link {
            text-align: center;
            margin-top: 20px
        }

        .back-link a {
            color: var(--text-gray);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease
        }

        .back-link a:hover {
            color: var(--beige-dark)
        }

        @media (max-width:480px) {
            .verify-container {
                padding: 20px 16px
            }

            .verify-header {
                padding: 30px 20px
            }

            .verify-body {
                padding: 30px 20px
            }

            .code-input {
                width: 45px;
                height: 50px;
                font-size: 20px
            }

            .timer {
                font-size: 28px
            }
        }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-header">
                <div class="verify-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h1 class="verify-title">Vérification du Code</h1>
                <p class="verify-subtitle">Entrez le code à 6 chiffres envoyé à votre adresse email</p>
            </div>

            <div class="verify-body">
                <div class="alert" id="alertMessage"></div>

                <div class="email-display">
                    <div class="email-label">Adresse email</div>
                    <div class="email-value"><?php echo htmlspecialchars($email); ?></div>
                </div>

                <div class="timer-container">
                    <div class="timer-label">Temps restant</div>
                    <div class="timer" id="timer"><?php echo gmdate('i:s', $remaining_time); ?></div>
                </div>

                <form id="verifyForm">
                    <div class="code-input-container">
                        <label class="code-label">Code de vérification</label>
                        <div class="code-inputs">
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                required>
                        </div>
                        <input type="hidden" id="fullCode" name="verification_code">
                    </div>

                    <button type="submit" class="btn" id="verifyBtn">
                        <i class="fas fa-check-circle"></i> Vérifier le code
                    </button>
                </form>

                <div class="resend-container">
                    <p class="resend-text">Vous n'avez pas reçu le code ?</p>
                    <button type="button" class="resend-link" id="resendBtn">
                        <i class="fas fa-redo"></i> Renvoyer le code
                    </button>
                </div>

                <div class="back-link">
                    <a href="register.php">
                        <i class="fas fa-arrow-left"></i>
                        Retour à l'inscription
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Récupérer les données depuis sessionStorage
        const tempFirstName = sessionStorage.getItem('temp_first_name');
        const tempLastName = sessionStorage.getItem('temp_last_name');
        const tempBirthDate = sessionStorage.getItem('temp_birth_date');
        const tempPhone = sessionStorage.getItem('temp_phone');
        const tempEmail = sessionStorage.getItem('temp_email');
        const tempPassword = sessionStorage.getItem('temp_password');

        // Timer countdown
        let timeLeft = <?php echo $remaining_time; ?>;
        const timerElement = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');
        let timerInterval;

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 60) {
                timerElement.classList.add('warning');
            }

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.classList.add('expired');
                timerElement.textContent = '00:00';
                resendBtn.disabled = true;
                showAlert('Le code a expiré. Veuillez demander un nouveau code.', 'error');

                // Rediriger après 3 secondes
                setTimeout(() => {
                    window.location.href = 'register.php?error=code_expired';
                }, 3000);
            }

            timeLeft--;
        }

        // Démarrer le timer
        timerInterval = setInterval(updateTimer, 1000);
        updateTimer();

        // Gestion des inputs du code
        const codeInputs = document.querySelectorAll('.code-input');
        const fullCodeInput = document.getElementById('fullCode');

        codeInputs.forEach((input, index) => {
            input.addEventListener('input', function (e) {
                const value = e.target.value;

                // Accepter uniquement les chiffres
                if (!/^\d$/.test(value)) {
                    e.target.value = '';
                    return;
                }

                // Ajouter la classe filled
                if (value) {
                    e.target.classList.add('filled');
                } else {
                    e.target.classList.remove('filled');
                }

                // Passer au champ suivant
                if (value && index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }

                // Mettre à jour le code complet
                updateFullCode();
            });

            input.addEventListener('keydown', function (e) {
                // Gérer la touche Retour arrière
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    codeInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', function (e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/\D/g, '');

                if (pastedData.length === 6) {
                    // Remplir tous les champs
                    pastedData.split('').forEach((digit, i) => {
                        if (codeInputs[i]) {
                            codeInputs[i].value = digit;
                            codeInputs[i].classList.add('filled');
                        }
                    });
                    updateFullCode();
                    codeInputs[5].focus();
                }
            });
        });

        function updateFullCode() {
            const code = Array.from(codeInputs).map(input => input.value).join('');
            fullCodeInput.value = code;
        }

        function showAlert(message, type) {
            showToast(message, type === 'error' ? 'error' : 'success');
        }

        // Soumission du formulaire
        document.getElementById('verifyForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const code = fullCodeInput.value;

            if (code.length !== 6) {
                showAlert('Veuillez entrer les 6 chiffres du code', 'error');
                return;
            }

            const verifyBtn = document.getElementById('verifyBtn');
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';

            const formData = new FormData();
            formData.append('action', 'register_with_verification');
            formData.append('first_name', tempFirstName || '');
            formData.append('last_name', tempLastName || '');
            formData.append('birth_date', tempBirthDate || '');
            formData.append('phone', tempPhone || '');
            formData.append('email', tempEmail || '<?php echo htmlspecialchars($email); ?>');
            formData.append('password', tempPassword || '');
            formData.append('confirm_password', tempPassword || '');
            formData.append('verification_code', code);

            fetch('src/Controllers/api/auth.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Inscription réussie ! Redirection...', 'success');
                        clearInterval(timerInterval);

                        // Nettoyer sessionStorage
                        sessionStorage.removeItem('temp_first_name');
                        sessionStorage.removeItem('temp_last_name');
                        sessionStorage.removeItem('temp_birth_date');
                        sessionStorage.removeItem('temp_phone');
                        sessionStorage.removeItem('temp_email');
                        sessionStorage.removeItem('temp_password');

                        setTimeout(() => {
                            window.location.href = 'login.php?message=registered';
                        }, 2000);
                    } else {
                        showAlert(data.message || 'Code invalide', 'error');

                        // Ajouter la classe error aux inputs
                        codeInputs.forEach(input => input.classList.add('error'));
                        setTimeout(() => {
                            codeInputs.forEach(input => input.classList.remove('error'));
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Verification error:', error);
                    showAlert('Erreur de vérification. Veuillez réessayer.', 'error');
                })
                .finally(() => {
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Vérifier le code';
                });
        });

        // Renvoyer le code
        resendBtn.addEventListener('click', function () {
            if (this.disabled) return;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

            fetch('src/Controllers/api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=send_verification_code&email=${encodeURIComponent('<?php echo $email; ?>')}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Nouveau code envoyé !', 'success');

                        // Réinitialiser le timer
                        clearInterval(timerInterval);
                        timeLeft = 960; // 16 minutes
                        timerElement.classList.remove('expired', 'warning');
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = '<i class="fas fa-redo"></i> Renvoyer le code';
                        timerInterval = setInterval(updateTimer, 1000);
                        updateTimer();

                        // Vider les champs
                        codeInputs.forEach(input => {
                            input.value = '';
                            input.classList.remove('filled');
                        });
                        codeInputs[0].focus();
                        fullCodeInput.value = '';
                    } else {
                        showAlert(data.message || 'Erreur d\'envoi', 'error');
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-redo"></i> Renvoyer le code';
                    }
                })
                .catch(error => {
                    console.error('Resend error:', error);
                    showAlert('Erreur d\'envoi. Veuillez réessayer.', 'error');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-redo"></i> Renvoyer le code';
                });
        });

        // Focus sur le premier champ
        codeInputs[0].focus();
    </script>
</body>

</html>