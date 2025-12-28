<?php
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — Nuraya</title>
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
        color: var(--text-dark)
    }

    /* Page */
    .page-wrap {
        max-width: 900px;
        margin: 40px auto;
        padding: 30px
    }

    .page-title {
        font-size: 28px;
        margin-bottom: 12px;
        color: var(--text-dark);
        font-weight: 700
    }

    .page-subtitle {
        font-size: 16px;
        color: var(--text-gray);
        margin-bottom: 30px
    }

    .contact-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 30px;
        align-items: start
    }

    /* Form */
    .field {
        margin-bottom: 16px
    }

    label {
        display: block;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 6px;
        font-size: 14px
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid rgba(200, 182, 166, 0.3);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background: var(--bg-white);
        transition: border-color 0.3s
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    textarea:focus {
        outline: none;
        border-color: var(--beige-dark);
        box-shadow: 0 0 0 3px rgba(200, 182, 166, 0.1)
    }

    textarea {
        min-height: 140px;
        resize: vertical
    }

    .btn-submit {
        background: var(--beige-dark);
        color: var(--bg-white);
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s
    }

    .btn-submit:hover {
        background: var(--text-dark);
        transform: translateY(-2px)
    }

    /* Aside */
    .contact-aside {
        background: var(--bg-white);
        padding: 24px;
        border-radius: 16px;
        border: 1px solid rgba(200, 182, 166, 0.2);
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
    }

    .aside-title {
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--text-dark)
    }

    .aside-desc {
        color: var(--text-gray);
        font-size: 13px;
        margin-bottom: 18px
    }

    .contact-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 16px
    }

    .contact-item i {
        font-size: 18px;
        color: var(--beige-dark);
        min-width: 24px;
        margin-top: 2px
    }

    .contact-item-label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 13px
    }

    .contact-item-value {
        color: var(--text-gray);
        font-size: 13px;
        margin-top: 2px
    }

    /* Responsive */
    @media (max-width:900px) {
        .contact-card {
            grid-template-columns: 1fr;
        }

        .page-wrap {
            padding: 20px;
            margin: 20px
        }
    }
    </style>
</head>

<body>
    <?php include 'templates/navbar_updated.php'; ?>

    <div class="page-wrap">
        <h1 class="page-title">Contactez-nous</h1>
        <p class="page-subtitle">Vous avez une question ? Envoyez-nous un message — nous vous répondrons rapidement.</p>

        <div class="contact-card">
            <div>
                <form id="contactForm">
                    <div class="field">
                        <label for="name">Nom</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="field">
                        <label for="phone">Téléphone</label>
                        <input type="tel" name="phone" id="phone">
                    </div>
                    <div class="field">
                        <label for="subject">Sujet</label>
                        <input type="text" name="subject" id="subject" placeholder="Sujet de votre message">
                    </div>
                    <div class="field">
                        <label for="comment">Message</label>
                        <textarea name="message" id="comment" required></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn-submit">Envoyer le message</button>
                    </div>
                </form>
            </div>

            <aside class="contact-aside">
                <div class="aside-title">Informations</div>
                <p class="aside-desc">Nous sommes disponibles du lundi au vendredi de 9h à 18h. Réponse généralement
                    sous 24 heures.</p>

                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <div class="contact-item-label">Adresse</div>
                        <div class="contact-item-value">123 Rue Exemple, Ville, Pays</div>
                    </div>
                </div>

                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <div class="contact-item-label">Téléphone</div>
                        <div class="contact-item-value">+216 00 000 000</div>
                    </div>
                </div>

                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <div class="contact-item-label">Email</div>
                        <div class="contact-item-value">support@nuraya.example</div>
                    </div>
                </div>

                <p class="aside-desc" style="margin-top:18px">Suivez-nous sur les réseaux pour les dernières nouveautés.
                </p>
            </aside>
        </div>
    </div>

    <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'send');

        const submitBtn = this.querySelector('.btn-submit');
        const originalText = submitBtn.textContent;

        // Désactiver le bouton et afficher le chargement
        submitBtn.disabled = true;
        submitBtn.textContent = 'Envoi en cours...';

        fetch('api/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    this.reset();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur lors de l\'envoi du message. Veuillez réessayer.', 'error');
            })
            .finally(() => {
                // Réactiver le bouton
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
    });
    </script>

    <?php include 'templates/footer.php'; ?>
</body>

</html>