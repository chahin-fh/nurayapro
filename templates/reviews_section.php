<!-- Reviews Section -->
<section class="reviews-section">
    <div class="container">
        <div class="reviews-header">
            <h2 class="section-title">Avis des Clients</h2>

            <!-- Quick Rating Section -->
            <div class="quick-rating-section">
                <div class="rating-prompt">
                    <h3>Quelle est votre expérience avec ce produit ?</h3>
                    <div class="quick-rating-stars" id="quickRatingStars">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                    <p class="rating-hint">Cliquez sur les étoiles pour noter ce produit</p>
                </div>
            </div>

            <!-- Rating Summary -->
            <div class="rating-summary">
                <div class="rating-overview">
                    <div class="average-rating">
                        <span class="rating-number" id="avgRating">0.0</span>
                        <div class="stars" id="avgStars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="total-reviews" id="totalReviews">(0 avis)</span>
                    </div>
                </div>

                <div class="rating-distribution" id="ratingDistribution">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Write Review Button -->
        <div class="write-review-section">
            <button class="btn btn-primary btn-large" id="writeReviewBtn">
                <i class="fas fa-pen"></i>
                Rédiger un avis détaillé
            </button>
        </div>

        <!-- Reviews List -->
        <div class="reviews-list" id="reviewsList">
            <!-- Reviews will be loaded here -->
        </div>

        <!-- Load More -->
        <div class="load-more-container" id="loadMoreContainer" style="display: none;">
            <button class="btn btn-secondary" id="loadMoreBtn">
                <i class="fas fa-plus"></i>
                Charger plus d'avis
            </button>
        </div>
    </div>
</section>

<!-- Review Modal -->
<div class="modal" id="reviewModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Rédiger un avis</h3>
            <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="reviewForm">
                <div class="form-group">
                    <label for="rating">Note *</label>
                    <div class="rating-input" id="ratingInput">
                        <i class="far fa-star" data-rating="1"></i>
                        <i class="far fa-star" data-rating="2"></i>
                        <i class="far fa-star" data-rating="3"></i>
                        <i class="far fa-star" data-rating="4"></i>
                        <i class="far fa-star" data-rating="5"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title">Titre (optionnel)</label>
                    <input type="text" id="reviewTitle" name="title" placeholder="Résumez votre expérience">
                </div>

                <div class="form-group">
                    <label for="comment">Votre avis *</label>
                    <textarea id="reviewComment" name="comment" rows="5"
                        placeholder="Décrivez votre expérience avec ce produit..." required></textarea>
                    <small>Minimum 10 caractères</small>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelReview">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer l'avis</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal" id="reportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Signaler un avis</h3>
            <button class="modal-close" id="closeReportModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="reportForm">
                <div class="form-group">
                    <label for="reportReason">Raison du signalement *</label>
                    <textarea id="reportReason" name="reason" rows="4"
                        placeholder="Expliquez pourquoi vous signalez cet avis..." required></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelReport">Annuler</button>
                    <button type="submit" class="btn btn-danger">Signaler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.reviews-section {
    padding: 60px 0;
    background: var(--bg-light);
}

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 40px;
    gap: 40px;
}

.quick-rating-section {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(200, 182, 166, 0.1);
    border: 1px solid rgba(200, 182, 166, 0.2);
    text-align: center;
    min-width: 300px;
}

.rating-prompt h3 {
    margin-bottom: 20px;
    color: var(--text-dark);
    font-size: 20px;
    font-weight: 600;
}

.quick-rating-stars {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 15px;
    font-size: 32px;
}

.quick-rating-stars i {
    cursor: pointer;
    color: #ddd;
    transition: all 0.3s ease;
    transform-origin: center;
}

.quick-rating-stars i:hover {
    color: #ffc107;
    transform: scale(1.1);
}

.quick-rating-stars i.active {
    color: #ffc107;
    animation: starPulse 0.4s ease;
}

.rating-hint {
    color: var(--text-gray);
    font-size: 14px;
    margin: 0;
}

@keyframes starPulse {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.3);
    }

    100% {
        transform: scale(1);
    }
}

.rating-summary {
    display: flex;
    gap: 40px;
    align-items: flex-start;
}

.rating-overview {
    text-align: center;
}

.average-rating {
    margin-bottom: 20px;
}

.rating-number {
    font-size: 48px;
    font-weight: 700;
    color: var(--beige-dark);
    display: block;
    line-height: 1;
}

.stars {
    margin: 10px 0;
    color: #ffc107;
    font-size: 20px;
}

.total-reviews {
    color: var(--text-gray);
    font-size: 14px;
}

.rating-distribution {
    min-width: 200px;
}

.rating-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.rating-bar-label {
    display: flex;
    align-items: center;
    gap: 5px;
    min-width: 40px;
    font-size: 14px;
}

.rating-bar-track {
    flex: 1;
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}

.rating-bar-fill {
    height: 100%;
    background: #ffc107;
    transition: width 0.3s ease;
}

.rating-bar-count {
    min-width: 30px;
    text-align: right;
    font-size: 14px;
    color: var(--text-gray);
}

.write-review-section {
    margin-bottom: 40px;
}

.reviews-list {
    margin-bottom: 40px;
}

.review-item {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(200, 182, 166, 0.1);
    border: 1px solid rgba(200, 182, 166, 0.2);
    transition: all 0.3s ease;
    position: relative;
}

.review-item:hover {
    box-shadow: 0 8px 30px rgba(200, 182, 166, 0.15);
    transform: translateY(-2px);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.review-author {
    display: flex;
    align-items: center;
    gap: 16px;
}

.review-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--beige-dark), #d4a574);
    color: var(--bg-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 18px;
    box-shadow: 0 2px 8px rgba(200, 182, 166, 0.3);
}

.review-info {
    display: flex;
    flex-direction: column;
}

.review-name {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 16px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.verified-badge,
.admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.verified-badge {
    background: #e8f5e8;
    color: #2e7d32;
}

.admin-badge {
    background: #fff3e0;
    color: #f57c00;
}

.review-date {
    color: var(--text-gray);
    font-size: 14px;
}

.review-rating {
    color: #ffc107;
    font-size: 18px;
}

.review-title {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 18px;
    margin-bottom: 12px;
    line-height: 1.4;
}

.review-comment {
    color: var(--text-dark);
    line-height: 1.6;
    font-size: 15px;
    margin-bottom: 20px;
}

.review-actions {
    display: flex;
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(200, 182, 166, 0.2);
}

.review-action-btn {
    background: none;
    border: 1px solid rgba(200, 182, 166, 0.3);
    color: var(--text-gray);
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.review-action-btn:hover {
    background: var(--bg-light);
    color: var(--text-dark);
    border-color: var(--beige-dark);
}

.review-action-btn.active {
    background: var(--beige-dark);
    color: var(--bg-white);
    border-color: var(--beige-dark);
}

.write-review-section {
    margin-bottom: 40px;
    text-align: center;
}

.btn-large {
    padding: 16px 32px;
    font-size: 16px;
    border-radius: 12px;
    font-weight: 600;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal.show {
    display: flex;
}

.modal-content {
    background: var(--bg-white);
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid rgba(200, 182, 166, 0.2);
}

.modal-header h3 {
    margin: 0;
    color: var(--text-dark);
    font-size: 20px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: var(--text-gray);
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: var(--bg-light);
    color: var(--text-dark);
}

.modal-body {
    padding: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-dark);
}

.rating-input {
    display: flex;
    gap: 8px;
    font-size: 24px;
}

.rating-input i {
    cursor: pointer;
    color: #ddd;
    transition: color 0.3s ease;
}

.rating-input i:hover,
.rating-input i.active {
    color: #ffc107;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid rgba(200, 182, 166, 0.3);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--beige-dark);
    box-shadow: 0 0 0 3px rgba(200, 182, 166, 0.1);
}

.form-group small {
    display: block;
    margin-top: 4px;
    color: var(--text-gray);
    font-size: 12px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-primary {
    background: var(--beige-dark);
    color: var(--bg-white);
}

.btn-primary:hover {
    background: var(--text-dark);
    transform: translateY(-2px);
}

.btn-secondary {
    background: var(--bg-light);
    color: var(--text-dark);
    border: 1px solid rgba(200, 182, 166, 0.3);
}

.btn-secondary:hover {
    background: rgba(200, 182, 166, 0.1);
}

.btn-danger {
    background: #f44336;
    color: white;
}

.btn-danger:hover {
    background: #d32f2f;
}

@media (max-width: 768px) {
    .reviews-header {
        flex-direction: column;
        gap: 30px;
    }

    .rating-summary {
        flex-direction: column;
        gap: 30px;
        width: 100%;
    }

    .rating-number {
        font-size: 36px;
    }

    .review-header {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }

    .modal-content {
        width: 95%;
        margin: 20px;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>

<script>
// Reviews functionality
let currentPage = 1;
let selectedRating = 0;
let totalReviews = 0;
let totalPages = 1;

// Get product ID from URL to avoid conflicts
function getProductId() {
    const urlParams = new URLSearchParams(window.location.search);
    return parseInt(urlParams.get('id')) || 0;
}

let currentProductId = getProductId();

// Initialize reviews
document.addEventListener('DOMContentLoaded', function() {
    if (currentProductId > 0) {
        loadReviews();
        loadRatingSummary();
    }

    // Setup event listeners
    setupEventListeners();
});

function setupEventListeners() {
    // Quick Rating Stars
    document.querySelectorAll('#quickRatingStars i').forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            handleQuickRating(rating);
        });

        star.addEventListener('mouseenter', function() {
            const hoverRating = parseInt(this.dataset.rating);
            updateQuickRatingDisplay(hoverRating);
        });
    });

    document.getElementById('quickRatingStars').addEventListener('mouseleave', function() {
        updateQuickRatingDisplay(selectedRating);
    });

    // Review modal
    document.getElementById('writeReviewBtn').addEventListener('click', openReviewModal);
    document.getElementById('closeModal').addEventListener('click', closeReviewModal);
    document.getElementById('cancelReview').addEventListener('click', closeReviewModal);

    // Rating input - improved with hover effects
    document.querySelectorAll('#ratingInput i').forEach(star => {
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.rating);
            updateRatingDisplay(selectedRating);
        });

        star.addEventListener('mouseenter', function() {
            const hoverRating = parseInt(this.dataset.rating);
            updateRatingDisplay(hoverRating);
        });
    });

    document.getElementById('ratingInput').addEventListener('mouseleave', function() {
        updateRatingDisplay(selectedRating);
    });

    // Review form
    document.getElementById('reviewForm').addEventListener('submit', submitReview);

    // Report modal
    document.getElementById('closeReportModal').addEventListener('click', closeReportModal);
    document.getElementById('cancelReport').addEventListener('click', closeReportModal);
    document.getElementById('reportForm').addEventListener('submit', submitReport);

    // Load more
    document.getElementById('loadMoreBtn').addEventListener('click', loadMoreReviews);

    // Close modals on outside click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });
}

function handleQuickRating(rating) {
    // Check if user is logged in
    fetch('api/auth.php?action=check', { credentials: 'include' })
        .then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                if(confirm('Vous devez être connecté pour noter ce produit. Voulez-vous vous connecter ?')) {
                     const currentUrl = encodeURIComponent(window.location.href);
                     window.location.href = `login.php?redirect=${currentUrl}`;
                }
                return;
            }

            // Show quick review modal with pre-selected rating
            selectedRating = rating;
            updateRatingDisplay(selectedRating);
            openReviewModal();
        })
        .catch(error => {
            console.error('Auth check error:', error);
            showMessage('Erreur lors de la vérification de connexion', 'error');
        });
}

function updateQuickRatingDisplay(rating) {
    document.querySelectorAll('#quickRatingStars i').forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas', 'active');
        } else {
            star.classList.remove('fas', 'active');
            star.classList.add('far');
        }
    });
}

function updateRatingDisplay(rating) {
    document.querySelectorAll('#ratingInput i').forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas', 'active');
        } else {
            star.classList.remove('fas', 'active');
            star.classList.add('far');
        }
    });
}

function openReviewModal() {
    // Check if user is logged in
    fetch('api/auth.php?action=check', { credentials: 'include' })
        .then(response => response.json())
        .then(data => {
            if (!data.authenticated) {
                if(confirm('Vous devez être connecté pour noter ce produit. Voulez-vous vous connecter ?')) {
                    const currentUrl = encodeURIComponent(window.location.href);
                    window.location.href = `login.php?redirect=${currentUrl}`;
                }
                return;
            }
            document.getElementById('reviewModal').classList.add('show');
        })
        .catch(error => {
            console.error('Auth check error:', error);
            showMessage('Erreur lors de la vérification de connexion', 'error');
        });
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.remove('show');
    resetReviewForm();
}

function resetReviewForm() {
    document.getElementById('reviewForm').reset();
    selectedRating = 0;
    updateRatingDisplay(0);
}

function submitReview(e) {
    e.preventDefault();

    if (selectedRating === 0) {
        showMessage('Veuillez sélectionner une note', 'error');
        return;
    }

    const title = document.getElementById('reviewTitle').value.trim();
    const comment = document.getElementById('reviewComment').value.trim();

    if (comment.length < 10) {
        showMessage('L\'avis doit contenir au moins 10 caractères', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', currentProductId);
    formData.append('rating', selectedRating);
    formData.append('title', title);
    formData.append('comment', comment);

    fetch('api/reviews.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                closeReviewModal();
                // Reload reviews after a short delay
                setTimeout(() => {
                    currentPage = 1;
                    loadReviews();
                    loadRatingSummary();
                }, 1000);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Submit review error:', error);
            showMessage('Erreur lors de l\'envoi de l\'avis', 'error');
        });
}

function loadReviews() {
    fetch(`api/reviews.php?action=get&product_id=${currentProductId}&page=${currentPage}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReviews(data.reviews, currentPage === 1);
                updatePagination(data.pagination);
            }
        })
        .catch(error => {
            console.error('Load reviews error:', error);
            showMessage('Erreur lors du chargement des avis', 'error');
        });
}

function loadRatingSummary() {
    fetch(`api/reviews.php?action=get&product_id=${currentProductId}&page=1`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stats) {
                displayRatingSummary(data.stats);
            }
        })
        .catch(error => {
            console.error('Load rating summary error:', error);
        });
}

function displayReviews(reviews, clear = false) {
    const reviewsList = document.getElementById('reviewsList');

    if (clear) {
        reviewsList.innerHTML = '';
    }

    reviews.forEach(review => {
        const reviewElement = createReviewElement(review);
        reviewsList.appendChild(reviewElement);
    });
}

function createReviewElement(review) {
    const div = document.createElement('div');
    div.className = 'review-item';

    const starsHtml = generateStars(review.rating);
    const dateFormatted = new Date(review.date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const helpfulCount = review.helpful_count || 0;
    const userVoted = review.user_voted || false;
    const activeClass = userVoted ? 'active' : '';

    div.innerHTML = `
        <div class="review-header">
            <div class="review-author">
                <div class="review-avatar">${review.author.charAt(0).toUpperCase()}</div>
                <div class="review-info">
                    <div class="review-name">
                        ${review.author}
                        ${review.verified_purchase ? '<span class="verified-badge"><i class="fas fa-check"></i> Achat vérifié</span>' : ''}
                        ${review.is_admin ? '<span class="admin-badge"><i class="fas fa-shield-alt"></i> Admin</span>' : ''}
                    </div>
                    <div class="review-date">${dateFormatted}</div>
                </div>
            </div>
            <div class="review-rating">${starsHtml}</div>
        </div>
        ${review.title ? `<div class="review-title">${review.title}</div>` : ''}
        <div class="review-comment">${review.comment}</div>
        <div class="review-actions">
            <button class="review-action-btn ${activeClass}" onclick="markHelpful(${review.id}, this)" data-review-id="${review.id}">
                <i class="fas fa-thumbs-up"></i> Utile ${helpfulCount > 0 ? `(${helpfulCount})` : ''}
            </button>
            <button class="review-action-btn" onclick="openReportModal(${review.id})">
                <i class="fas fa-flag"></i> Signaler
            </button>
        </div>
    `;

    return div;
}

function generateStars(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            html += '<i class="fas fa-star"></i>';
        } else {
            html += '<i class="far fa-star"></i>';
        }
    }
    return html;
}

function displayRatingSummary(stats) {
    document.getElementById('avgRating').textContent = stats.avg_rating;
    document.getElementById('totalReviews').textContent = `(${stats.total_reviews} avis)`;

    // Update average stars
    const avgStars = document.getElementById('avgStars');
    avgStars.innerHTML = generateStars(Math.round(stats.avg_rating));

    // Update distribution with animated bars
    const distributionHtml = Object.entries(stats.distribution)
        .reverse()
        .map(([stars, count]) => {
            const percentage = stats.total_reviews > 0 ? (count / stats.total_reviews) * 100 : 0;
            return `
                <div class="rating-bar">
                    <div class="rating-bar-label">
                        <span>${stars}</span>
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="rating-bar-track">
                        <div class="rating-bar-fill" style="width: ${percentage}%"></div>
                    </div>
                    <div class="rating-bar-count">${count}</div>
                </div>
            `;
        })
        .join('');

    document.getElementById('ratingDistribution').innerHTML = distributionHtml;

    updateProductRating(stats);
}

function updateProductRating(stats) {
    const productRating = document.querySelector('.product-rating');
    if (!productRating || !stats) {
        return;
    }

    const avg = typeof stats.avg_rating !== 'undefined' ? parseFloat(stats.avg_rating) : 0;
    const total = typeof stats.total_reviews !== 'undefined' ? parseInt(stats.total_reviews, 10) : 0;

    const starEls = productRating.querySelectorAll('.stars .star');
    if (starEls && starEls.length) {
        starEls.forEach((starEl, index) => {
            const starNumber = index + 1;
            if (starNumber <= avg) {
                starEl.classList.add('filled');
            } else {
                starEl.classList.remove('filled');
            }
        });
    }

    const textEl = productRating.querySelector('.rating-text');
    if (textEl) {
        const displayAvg = isNaN(avg) ? '0.0' : avg.toFixed(1);
        textEl.textContent = `${displayAvg}/5 (${isNaN(total) ? 0 : total} avis)`;
    }
}

function updatePagination(pagination) {
    totalReviews = pagination.total_reviews;
    totalPages = pagination.total_pages;

    const loadMoreContainer = document.getElementById('loadMoreContainer');
    if (currentPage < totalPages) {
        loadMoreContainer.style.display = 'block';
        document.getElementById('loadMoreBtn').textContent =
            `Charger plus d'avis (${totalReviews - (currentPage * 10)} restants)`;
    } else {
        loadMoreContainer.style.display = 'none';
    }
}

function loadMoreReviews() {
    if (currentPage < totalPages) {
        currentPage++;
        loadReviews();
    }
}

function markHelpful(reviewId, button) {
    fetch('api/reviews.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=helpful&review_id=${reviewId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const buttonIcon = button.querySelector('i');
                const buttonText = button.childNodes[button.childNodes.length - 1];
                
                // Get current count from button text
                const match = buttonText.textContent.match(/\((\d+)\)/);
                let currentCount = match ? parseInt(match[1]) : 0;
                
                if (data.action === 'added') {
                    button.classList.add('active');
                    currentCount++;
                    showMessage('Vote ajouté', 'success');
                } else {
                    button.classList.remove('active');
                    currentCount--;
                    showMessage('Vote retiré', 'success');
                }
                
                // Update button text with new count
                const countText = currentCount > 0 ? ` (${currentCount})` : '';
                button.innerHTML = `<i class="fas fa-thumbs-up"></i> Utile${countText}`;
            } else {
                if (data.message === 'Utilisateur non connecté') {
                    if(confirm('Vous devez être connecté pour voter. Voulez-vous vous connecter ?')) {
                        const currentUrl = encodeURIComponent(window.location.href);
                        window.location.href = `login.php?redirect=${currentUrl}`;
                    }
                } else {
                    showMessage(data.message, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Mark helpful error:', error);
            showMessage('Erreur lors du vote', 'error');
        });
}

function openReportModal(reviewId) {
    document.getElementById('reportModal').classList.add('show');
    document.getElementById('reportForm').dataset.reviewId = reviewId;
}

function closeReportModal() {
    document.getElementById('reportModal').classList.remove('show');
    document.getElementById('reportForm').reset();
    delete document.getElementById('reportForm').dataset.reviewId;
}

function submitReport(e) {
    e.preventDefault();

    const reviewId = e.target.dataset.reviewId;
    const reason = document.getElementById('reportReason').value.trim();

    if (!reason) {
        showMessage('Veuillez indiquer la raison du signalement', 'error');
        return;
    }

    fetch('api/reviews.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=report&review_id=${reviewId}&reason=${encodeURIComponent(reason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                closeReportModal();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Report error:', error);
            showMessage('Erreur lors du signalement', 'error');
        });
}

function showMessage(message, type = 'info') {
    // Remove existing messages
    document.querySelectorAll('.message').forEach(msg => msg.remove());

    const messageDiv = document.createElement('div');
    messageDiv.className = `message message-${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        max-width: 300px;
        word-wrap: break-word;
    `;

    document.body.appendChild(messageDiv);

    setTimeout(() => {
        messageDiv.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => messageDiv.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>