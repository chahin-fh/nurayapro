<?php
include '../cnx.php';
session_start();
if($_SERVER['REQUEST_METHOD'] === "POST"):
    extract($_POST);
    $i = 0 ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #000;
            --accent: #ff6b6b;
            --bg: #f9f9f9;
            --muted: #6b7280;
            --light: #e5e7eb;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--bg);
            color: var(--primary);
        }
        
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 28px;
            background: white;
            border-bottom: 1px solid var(--light);
        }
        
        .navbar .logo {
            font-weight: 700;
            font-size: 20px;
            color: var(--primary);
            text-decoration: none;
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
        }
        
        .checkout-header {
            margin-bottom: 32px;
        }
        
        .checkout-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .checkout-header p {
            color: var(--muted);
            font-size: 14px;
        }
        
        .checkout-main {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 32px;
            align-items: start;
        }
        
        .checkout-card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title svg {
            color: var(--accent);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 8px;
            color: var(--primary);
        }
        
        .form-control {
            padding: 10px 12px;
            border: 1px solid var(--light);
            border-radius: 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }
        
        .form-error {
            border-color: var(--accent) !important;
            background-color: #fff5f5 !important;
        }
        
        .error-message {
            color: var(--accent);
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }
        
        .error-message.show {
            display: block;
        }
        
        .error-banner {
            background-color: #fff5f5;
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .error-banner ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .error-banner li {
            margin: 4px 0;
            font-size: 13px;
        }
        
        .btn {
            padding: 12px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            margin-top: 16px;
        }
        
        .btn:hover {
            background: var(--accent);
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .product-item {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--light);
        }
        
        .product-image {
            width: 70px;
            height: 70px;
            background: #f0f0f0;
            border-radius: 6px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-details {
            flex: 1;
            font-size: 13px;
        }
        
        .product-name {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 4px;
        }
        
        .product-price {
            color: var(--muted);
            font-size: 12px;
        }
        
        .divider {
            height: 1px;
            background: var(--light);
            margin: 16px 0;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 12px;
            color: var(--muted);
        }
        
        .summary-item span:last-child {
            color: var(--primary);
            font-weight: 600;
        }
        
        .summary-total {
            font-size: 16px;
            color: var(--primary);
        }
        
        .summary-total span:last-child {
            color: var(--accent);
            font-size: 18px;
            font-weight: 700;
        }
        
        @media (max-width: 768px) {
            .checkout-main {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../index.php" class="logo">nuraya</a>
    </div>
    
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Complete your order information below</p>
        </div>
        
        <main class="checkout-main">
            <div class="checkout-card">
                <?php 
                if (!empty($_SESSION['order_errors'])) {
                    echo '<div class="error-banner"><ul>';
                    foreach ($_SESSION['order_errors'] as $error) {
                        echo '<li>' . htmlspecialchars($error) . '</li>';
                    }
                    echo '</ul></div>';
                    unset($_SESSION['order_errors']);
                }
                
                $formData = !empty($_SESSION['order_data']) ? $_SESSION['order_data'] : array();
                unset($_SESSION['order_data']);
                ?>
                
                <h2 class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Delivery Information
                </h2>
                
                <form action="/nuraya_pro/submit-order" method="post" id="orderForm" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First name <span style="color: var(--accent);">*</span></label>
                            <input type="text" name="Fname" placeholder="John" class="form-control" value="<?php echo htmlspecialchars($formData['Fname'] ?? ''); ?>" required>
                            <span class="error-message" id="error-Fname"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last name <span style="color: var(--accent);">*</span></label>
                            <input type="text" name="Lname" placeholder="Doe" class="form-control" value="<?php echo htmlspecialchars($formData['Lname'] ?? ''); ?>" required>
                            <span class="error-message" id="error-Lname"></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Address <span style="color: var(--accent);">*</span></label>
                        <input type="text" name="addr" placeholder="123 Main Street" class="form-control" value="<?php echo htmlspecialchars($formData['addr'] ?? ''); ?>" required>
                        <span class="error-message" id="error-addr"></span>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Postal code</label>
                            <input type="text" name="codep" class="form-control" placeholder="Optional" value="<?php echo htmlspecialchars($formData['codep'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">City <span style="color: var(--accent);">*</span></label>
                            <input type="text" name="city" placeholder="Paris" class="form-control" value="<?php echo htmlspecialchars($formData['city'] ?? ''); ?>" required>
                            <span class="error-message" id="error-city"></span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone <span style="color: var(--accent);">*</span></label>
                        <input type="tel" name="tel" placeholder="+1 234 567 8900" class="form-control" value="<?php echo htmlspecialchars($formData['tel'] ?? ''); ?>" required>
                        <span class="error-message" id="error-tel"></span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" placeholder="john@example.com" class="form-control" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>">
                    </div>
                    
                    <?php
                    // Add hidden fields for product IDs and quantities inside the form
                    $product_total = 0;
                    foreach($idp as $idx => $id) : 
                        $product_total = $product_total + ($qua[$idx] ?? 0);
                    ?>
                    <input type="hidden" name="idp[]" value="<?php echo $id ; ?>">
                    <input type="hidden" name="qua[]" value="<?php echo $qua[$idx] ; ?>">
                    <?php endforeach; ?>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-check"></i> Complete Order
                    </button>
                </form>
            </div>
            
            <aside>
                <div class="checkout-card">
                    <h3 class="summary-title">
                        <i class="fas fa-shopping-bag" style="color: var(--accent);"></i>
                        Order Summary
                    </h3>
                    
                    <?php
                    $total = 0;
                    foreach($idp as $idx => $id) : 
                        $result = mysqli_query($cnx,"SELECT * from products where product_id = '$id' ;");
                        while($t = mysqli_fetch_assoc($result)):
                            $total = $total + ($t['price']*$qua[$idx]);
                        ?>
                    <div class="product-item">
                        <div class="product-image"><img src="<?php echo $t['image_url'] ; ?>" alt="<?php echo htmlspecialchars($t['name']); ?>"></div>
                        <div class="product-details">
                            <div class="product-name"><?php echo $t['name'] ; ?></div>
                            <div class="product-price"><?php echo $t['price'] ; ?> DT × <?php echo $qua[$idx] ; ?></div>
                        </div>
                    </div>
                    <?php endwhile ;
                        endforeach; ?>
                    
                    <div class="divider"></div>
                    
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span><?php echo number_format($total, 2); ?> DT</span>
                    </div>
                    <div class="summary-item">
                        <span>Shipping</span>
                        <span>9.00 DT</span>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="summary-item summary-total">
                        <span>Total</span>
                        <span><?php echo number_format($total + 9, 2); ?> DT</span>
                    </div>
                </div>
            </aside>
        </main>
    </div>
    
    <script>
        const form = document.getElementById('orderForm');
        const fields = {
            Fname: { regex: /^[a-zA-Z\s'-]{2,}$/, message: 'First name must be at least 2 characters (letters only)' },
            Lname: { regex: /^[a-zA-Z\s'-]{2,}$/, message: 'Last name must be at least 2 characters (letters only)' },
            addr: { regex: /^.{5,}$/, message: 'Address must be at least 5 characters' },
            city: { regex: /^[a-zA-Z\s'-]{2,}$/, message: 'City must be at least 2 characters (letters only)' },
            tel: { regex: /^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,9}$/, message: 'Phone must be 10-15 digits' }
        };
        
        Object.keys(fields).forEach(fieldName => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
                input.addEventListener('blur', function() {
                    validateField(fieldName, this.value);
                });
                
                input.addEventListener('input', function() {
                    const errorEl = document.getElementById(`error-${fieldName}`);
                    if (errorEl) {
                        errorEl.classList.remove('show');
                    }
                    this.classList.remove('form-error');
                });
            }
        });
        
        function validateField(fieldName, value) {
            const field = fields[fieldName];
            const input = form.querySelector(`[name="${fieldName}"]`);
            const errorEl = document.getElementById(`error-${fieldName}`);
            
            if (!field || !input || !errorEl) return true;
            
            const trimmedValue = value.trim();
            if (trimmedValue === '') {
                errorEl.textContent = 'This field is required';
                errorEl.classList.add('show');
                input.classList.add('form-error');
                return false;
            }
            
            if (!field.regex.test(trimmedValue)) {
                errorEl.textContent = field.message;
                errorEl.classList.add('show');
                input.classList.add('form-error');
                return false;
            }
            
            errorEl.classList.remove('show');
            input.classList.remove('form-error');
            return true;
        }
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            Object.keys(fields).forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input) {
                    const isFieldValid = validateField(fieldName, input.value);
                    if (!isFieldValid) isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.form-error');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
<?php 
mysqli_close($cnx);
endif; ?>