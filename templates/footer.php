<?php
// Footer professionnel pour Nuraya

// Pas besoin de chemins dynamiques - tout est à la racine
$base_path = '';
$assets_path = 'assets/';
?>
<style>
:root {
    --bg-light: #F5EFE6;
    --bg-white: #FAF7F2;
    --beige-dark: #C8B6A6;
    --text-dark: #1C1C1C;
    --text-gray: #7A7A7A;
    --accent-pink: #E6B7C8;
}

.footer {
    background: linear-gradient(135deg, var(--text-dark) 0%, #2a2a2a 100%);
    color: var(--bg-white);
    margin-top: 80px;
    position: relative;
    overflow: hidden;
}

.footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--beige-dark) 0%, var(--accent-pink) 50%, var(--beige-dark) 100%);
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 50px 24px 30px;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 40px;
}

.footer-section h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 24px;
    color: var(--bg-white);
    letter-spacing: 1px;
    text-transform: uppercase;
    position: relative;
    padding-bottom: 12px;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background: var(--beige-dark);
}

.footer-brand {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.footer-logo {
    font-size: 32px;
    font-weight: 800;
    color: var(--bg-white);
    text-decoration: none;
    letter-spacing: 3px;
    margin-bottom: 8px;
}

.footer-description {
    color: rgba(250, 247, 242, 0.8);
    line-height: 1.6;
    font-size: 14px;
    margin-bottom: 20px;
}

.social-links {
    display: flex;
    gap: 12px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(200, 182, 166, 0.1);
    border: 1px solid rgba(200, 182, 166, 0.3);
    border-radius: 50%;
    color: var(--bg-white);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 16px;
}

.social-link:hover {
    background: var(--beige-dark);
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(200, 182, 166, 0.3);
}

.footer-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.footer-links a {
    color: rgba(250, 247, 242, 0.8);
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.footer-links a::before {
    content: '›';
    color: var(--beige-dark);
    font-weight: bold;
    transition: transform 0.3s ease;
}

.footer-links a:hover {
    color: var(--bg-white);
    transform: translateX(5px);
}

.footer-links a:hover::before {
    transform: translateX(3px);
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: rgba(250, 247, 242, 0.8);
    font-size: 14px;
    line-height: 1.5;
}

.contact-icon {
    color: var(--beige-dark);
    font-size: 16px;
    margin-top: 2px;
    min-width: 16px;
}

.footer-bottom {
    background: rgba(0, 0, 0, 0.3);
    padding: 20px 24px;
    border-top: 1px solid rgba(200, 182, 166, 0.1);
}

.footer-bottom-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.footer-copyright {
    color: rgba(250, 247, 242, 0.6);
    font-size: 13px;
}

.footer-bottom-links {
    display: flex;
    gap: 24px;
}

.footer-bottom-links a {
    color: rgba(250, 247, 242, 0.6);
    text-decoration: none;
    font-size: 13px;
    transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
    color: var(--beige-dark);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .footer-content {
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .footer-brand {
        grid-column: 1 / -1;
    }
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        gap: 30px;
        padding: 40px 20px 30px;
    }

    .footer-brand {
        grid-column: 1;
    }

    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }

    .footer-bottom-links {
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
    }
}

@media (max-width: 480px) {
    .footer-logo {
        font-size: 28px;
    }
}
</style>

<footer class="footer">
    <div class="footer-content">
        <!-- Brand Section -->
        <div class="footer-section footer-brand">
            <a href="<?php echo $base_path; ?>index.php" class="footer-logo">NURAYA</a>
            <p class="footer-description">
                Découvrez l'élégance intemporelle à travers nos collections uniques.
            </p>
            <div class="social-links">
                <a href="#" class="social-link" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-link" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link" title="Pinterest">
                    <i class="fab fa-pinterest-p"></i>
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-section">
            <h3>Liens Utiles</h3>
            <div class="footer-links">
                <a href="<?php echo $base_path; ?>index.php">Accueil</a>
                <a href="<?php echo $base_path; ?>shop.php">Boutique</a>
                <a href="<?php echo $base_path; ?>about_new.php">À Propos</a>
                <a href="<?php echo $base_path; ?>contact_us.php">Contact</a>
                <a href="<?php echo $base_path; ?>account.php">Mon Compte</a>
            </div>
        </div>

        <!-- Contact -->
        <div class="footer-section">
            <h3>Contact</h3>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt contact-icon"></i>
                    <span>123 Avenue Habib Bourguiba, Tunis</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone contact-icon"></i>
                    <span>+216 71 234 567</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope contact-icon"></i>
                    <span>contact@nuraya.tn</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <div class="footer-copyright">
                &copy; <?php echo date('Y'); ?> NURAYA. Tous droits réservés. Crafted with &hearts; in Tunisia
            </div>
            <div class="footer-bottom-links">
                <a href="#">Mentions Légales</a>
                <a href="#">Politique de Confidentialité</a>
                <a href="#">CGV</a>
                <a href="#">Politique de Cookies</a>
            </div>
        </div>
    </div>
</footer>
<script src="<?php echo $assets_path; ?>js/cart-count.js"></script>