<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #000;
            --accent: #ff6b6b;
            --bg: #f9f9f9;
            --muted: #6b7280;
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .success-container {
            text-align: center;
            background: white;
            border-radius: 12px;
            padding: 40px;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent), #ff8787);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
            color: white;
            animation: scaleIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-content {
            animation: slideUp 0.6s ease-out 0.2s both;
        }
        
        .success-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
        }
        
        .success-message {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        
        .order-details {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 32px;
            border-left: 4px solid var(--accent);
        }
        
        .order-details p {
            font-size: 13px;
            color: var(--muted);
            margin: 8px 0;
        }
        
        .order-details strong {
            color: var(--primary);
            font-weight: 700;
        }
        
        .cta-button {
            display: inline-block;
            padding: 12px 32px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            margin: 0 8px;
            border: 2px solid var(--primary);
        }
        
        .cta-button:hover {
            background: var(--accent);
            border-color: var(--accent);
            transform: translateY(-2px);
        }
        
        .cta-button.secondary {
            background: transparent;
            color: var(--primary);
        }
        
        .cta-button.secondary:hover {
            background: transparent;
            color: var(--accent);
            border-color: var(--accent);
        }
        
        .cta-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .confetti span {
            position: absolute;
            display: block;
            animation: confettiFall 3s ease-out forwards;
        }
        
        @keyframes confettiFall {
            0% {
                transform: translateY(0) rotateZ(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(800px) rotateZ(720deg);
                opacity: 0;
            }
        }
        
        @media (max-width: 600px) {
            .success-container {
                padding: 24px;
            }
            
            .success-title {
                font-size: 22px;
            }
            
            .cta-group {
                flex-direction: column;
            }
            
            .cta-button {
                width: 100%;
                margin: 6px 0;
            }
        }
    </style>
</head>
<body>
    <div id="confetti" class="confetti"></div>
    
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <div class="success-content">
            <h1 class="success-title">Order Confirmed!</h1>
            <p class="success-message">
                Thank you for your purchase. Your order has been successfully placed and will be delivered to you shortly.
            </p>
            
            <div class="order-details">
                <p><strong>Order Status:</strong> Processing</p>
                <p><strong>Estimated Delivery:</strong> 3-5 business days</p>
                <p>You will receive a confirmation email shortly with tracking information.</p>
            </div>
            
            <div class="cta-group">
                <a href="home" class="cta-button">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="shop" class="cta-button secondary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Generate confetti
        (function() {
            const container = document.getElementById('confetti');
            const colors = ['#ff6b6b', '#000', '#f9f9f9', '#6b7280'];
            const pieces = 20;
            
            for (let i = 0; i < pieces; i++) {
                const el = document.createElement('span');
                const size = 6 + Math.random() * 8;
                el.style.width = size + 'px';
                el.style.height = size + 'px';
                el.style.left = Math.random() * 100 + '%';
                el.style.top = -10 + 'px';
                el.style.background = colors[Math.floor(Math.random() * colors.length)];
                el.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                el.style.opacity = Math.random() * 0.8 + 0.2;
                el.style.animationDelay = Math.random() * 0.3 + 's';
                container.appendChild(el);
            }
        })();
    </script>
</body>
</html>