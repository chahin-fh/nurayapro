<?php
include 'cnx.php';
include 'functions.php';

// Vérifier si l'utilisateur est connecté
if (!is_logged_in()) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

// Récupérer le panier de l'utilisateur
$session_id = $_SESSION['cart_session_id'] ?? '';
$cart_query = "SELECT c.*, p.name, p.price, p.stock_quantity
               FROM cart c 
               LEFT JOIN products p ON c.product_id = p.product_id 
               WHERE c.session_id = '$session_id' AND p.is_active = 1";
$cart_result = mysqli_query($cnx, $cart_query);

if (mysqli_num_rows($cart_result) === 0) {
    header('Location: cart.php');
    exit;
}

// Calculer les totaux
$subtotal = 0;  
while ($item = mysqli_fetch_assoc($cart_result)) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping = 7.000;
$tax = $subtotal * 0.19;
$total = $subtotal + $shipping + $tax;

// Récupérer les informations de l'utilisateur
$user_query = "SELECT * FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($cnx, $user_query);
$user = mysqli_fetch_assoc($user_result);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Nuraya</title>
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

    .checkout-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 24px
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px
    }

    .checkout-steps {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-bottom: 40px
    }

    .step {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-gray);
        font-size: 14px;
        font-weight: 500
    }

    .step.active {
        color: var(--beige-dark)
    }

    .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--bg-light);
        border: 2px solid var(--text-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px
    }

    .step.active .step-number {
        background: var(--beige-dark);
        color: var(--bg-white);
        border-color: var(--beige-dark)
    }

    .checkout-content {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px
    }

    .checkout-form {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15)
    }

    .form-section {
        margin-bottom: 30px
    }

    .form-section:last-child {
        margin-bottom: 0
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(200, 182, 166, 0.2)
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px
    }

    .form-group {
        margin-bottom: 16px
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
        padding: 12px 16px;
        border: 1px solid rgba(200, 182, 166, 0.3);
        border-radius: 8px;
        font-size: 14px;
        background: var(--bg-white);
        transition: all 0.3s ease
    }

    .form-control:focus {
        outline: none;
        border-color: var(--beige-dark);
        box-shadow: 0 0 0 3px rgba(200, 182, 166, 0.1)
    }

    .order-summary {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(200, 182, 166, 0.15);
        position: sticky;
        top: 100px
    }

    .summary-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 20px
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(200, 182, 166, 0.1)
    }

    .summary-row:last-child {
        border-bottom: none
    }

    .summary-label {
        color: var(--text-gray);
        font-size: 14px
    }

    .summary-value {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 16px
    }

    .summary-total {
        padding-top: 12px;
        border-top: 2px solid var(--beige-dark);
        margin-top: 12px
    }

    .summary-total .summary-label {
        font-weight: 700;
        font-size: 16px;
        color: var(--text-dark)
    }

    .summary-total .summary-value {
        font-size: 24px;
        color: var(--beige-dark)
    }

    .place-order-btn {
        width: 100%;
        padding: 16px;
        background: var(--beige-dark);
        color: var(--bg-white);
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px
    }

    .place-order-btn:hover {
        background: var(--text-dark);
        transform: translateY(-2px)
    }

    .place-order-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none
    }

    .payment-methods {
        display: flex;
        gap: 12px;
        margin-bottom: 16px
    }

    .payment-method {
        flex: 1;
        padding: 16px;
        border: 2px solid rgba(200, 182, 166, 0.3);
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease
    }

    .payment-method:hover {
        border-color: var(--beige-dark)
    }

    .payment-method.selected {
        border-color: var(--beige-dark);
        background: rgba(200, 182, 166, 0.1)
    }

    .payment-method i {
        font-size: 24px;
        color: var(--beige-dark);
        margin-bottom: 8px
    }

    .payment-method span {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark)
    }

    @media (max-width:768px) {
        .checkout-container {
            padding: 0 16px
        }

        .checkout-steps {
            flex-direction: row;
            gap: 15px;
            justify-content: center;
        }

        .step span {
            display: none;
        }

        .step::after {
            display: none;
        }

        .checkout-content {
            grid-template-columns: 1fr;
            gap: 20px
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0
        }

        .order-summary {
            position: static
        }
    }
    </style>
</head>

<body>
    <?php include 'navbar_updated.php'; ?>

    <div class="checkout-container">
        <div class="page-header">
            <h1 class="page-title">Checkout</h1>
        </div>

        <div class="checkout-steps">
            <div class="step active">
                <div class="step-number">1</div>
                <span>Livraison</span>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <span>Paiement</span>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <span>Confirmation</span>
            </div>
        </div>

        <div class="checkout-content">
            <div class="checkout-form">
                <form id="checkoutForm">
                    <div class="form-section">
                        <h2 class="section-title">Informations de livraison</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="first_name" class="form-control"
                                    value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nom</label>
                                <input type="text" name="last_name" class="form-control"
                                    value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="phone" class="form-control"
                                value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Adresse de livraison</label>
                            <input type="text" name="address" class="form-control" placeholder="Rue, numéro..."
                                required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Ville</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Code postal</label>
                                <input type="text" name="postal_code" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="section-title">Méthode de paiement</h2>

                        <div class="payment-methods">
                            <div class="payment-method selected" onclick="selectPayment(this, 'cash')">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Paiement à la livraison</span>
                            </div>
                            <div class="payment-method" onclick="selectPayment(this, 'card')">
                                <i class="fas fa-credit-card"></i>
                                <span>Carte bancaire</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="order-summary">
                <h2 class="summary-title">Récapitulatif de la commande</h2>

                <div class="summary-row">
                    <span class="summary-label">Sous-total</span>
                    <span class="summary-value"><?php echo number_format($subtotal, 3); ?> DT</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Livraison</span>
                    <span class="summary-value"><?php echo number_format($shipping, 3); ?> DT</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">TVA (19%)</span>
                    <span class="summary-value"><?php echo number_format($tax, 3); ?> DT</span>
                </div>

                <div class="summary-total">
                    <span class="summary-label">Total</span>
                    <span class="summary-value"><?php echo number_format($total, 3); ?> DT</span>
                </div>

                <button class="place-order-btn" onclick="placeOrder()">
                    Confirmer la commande
                </button>
            </div>
        </div>
    </div>

    <script>
    let selectedPayment = 'cash';

    function selectPayment(element, method) {
        document.querySelectorAll('.payment-method').forEach(el => {
            el.classList.remove('selected');
        });
        element.classList.add('selected');
        selectedPayment = method;
    }

    function placeOrder() {
        const form = document.getElementById('checkoutForm');
        const formData = new FormData(form);

        // Validation basique
        const requiredFields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'postal_code'];
        for (let field of requiredFields) {
            if (!formData.get(field)) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }
        }

        // Ajouter les informations supplémentaires
        formData.append('action', 'create');
        formData.append('payment_method', selectedPayment);
        formData.append('subtotal', <?php echo $subtotal; ?>);
        formData.append('shipping', <?php echo $shipping; ?>);
        formData.append('tax', <?php echo $tax; ?>);
        formData.append('total', <?php echo $total; ?>);

        const btn = document.querySelector('.place-order-btn');
        btn.disabled = true;
        btn.textContent = 'Traitement en cours...';

        fetch('api/orders.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Commande confirmée ! Redirection...');
                    window.location.href = 'order-confirmation.php?id=' + data.order_id;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la commande. Veuillez réessayer.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Confirmer la commande';
            });
    }
    </script>
</body>

</html>