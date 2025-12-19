<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#000;--muted:#6b7280;--accent:#ff6b6b;--bg:#f9f9f9}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Montserrat',sans-serif;background:var(--bg);color:#111}
        
        /* Page */
        .page-wrap{max-width:900px;margin:40px auto;padding:30px}
        .page-title{font-size:28px;margin-bottom:12px;color:var(--primary);font-weight:700}
        .page-subtitle{font-size:16px;color:var(--muted);margin-bottom:30px}
        .contact-card{background:white;border-radius:12px;padding:30px;box-shadow:0 4px 16px rgba(0,0,0,0.08);display:grid;grid-template-columns:1fr 300px;gap:30px;align-items:start}
        
        /* Form */
        .field{margin-bottom:16px}
        label{display:block;font-weight:600;color:#374151;margin-bottom:6px;font-size:14px}
        input[type="text"],input[type="email"],input[type="tel"],textarea{width:100%;padding:12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit}
        input[type="text"]:focus,input[type="email"]:focus,input[type="tel"]:focus,textarea:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(255,107,107,0.1)}
        textarea{min-height:140px;resize:vertical}
        .btn-submit{background:var(--primary);color:white;padding:12px 24px;border-radius:8px;border:none;font-weight:700;cursor:pointer;transition:all 0.3s}
        .btn-submit:hover{background:var(--accent)}
        
        /* Aside */
        .contact-aside{background:white;padding:24px;border-radius:12px;border:1px solid #e5e7eb}
        .aside-title{font-weight:700;margin-bottom:12px;color:var(--primary)}
        .aside-desc{color:var(--muted);font-size:13px;margin-bottom:18px}
        .contact-item{display:flex;gap:12px;align-items:flex-start;margin-bottom:16px}
        .contact-item i{font-size:18px;color:var(--accent);min-width:24px;margin-top:2px}
        .contact-item-label{font-weight:600;color:var(--primary);font-size:13px}
        .contact-item-value{color:var(--muted);font-size:13px;margin-top:2px}
        
        /* Responsive */
        @media (max-width:900px){.contact-card{grid-template-columns:1fr;}.page-wrap{padding:20px;margin:20px}}
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="page-wrap">
        <h1 class="page-title">Contactez-nous</h1>
        <p class="page-subtitle">Vous avez une question ? Envoyez-nous un message — nous vous répondrons rapidement.</p>

        <div class="contact-card">
            <div>
                <form method="POST" action="/send-message">
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
                        <label for="comment">Message</label>
                        <textarea name="comment" id="comment" required></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn-submit">Envoyer le message</button>
                    </div>
                </form>
            </div>

            <aside class="contact-aside">
                <div class="aside-title">Informations</div>
                <p class="aside-desc">Nous sommes disponibles du lundi au vendredi de 9h à 18h. Réponse généralement sous 24 heures.</p>

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

                <p class="aside-desc" style="margin-top:18px">Suivez-nous sur les réseaux pour les dernières nouveautés.</p>
            </aside>
        </div>
    </div>
        </div>
    </div>
</body>
</html>
