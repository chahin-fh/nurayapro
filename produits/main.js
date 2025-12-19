document.addEventListener('DOMContentLoaded', function() {
    console.log('main.js loaded');
    
    // Cart Functionality
    const floatingCartBadge = document.querySelector('#floatingCartBadge');
    const cartModal = document.querySelector('.modal-overlay');
    const closeCart = document.querySelector('.close-cart');
    const cartItemsContainer = document.querySelector('.cart-items');
    const cartTotalSpan = document.querySelector('.cart-total span');
    const cartBadgeCount = document.querySelector('#cartBadgeCount');
    const addToCartBtns = document.querySelectorAll('.add-to-cart');

    console.log('Elements found:', { floatingCartBadge, cartModal, closeCart, cartItemsContainer, cartTotalSpan, cartBadgeCount, btnCount: addToCartBtns.length });

    let cart = [];

    // Initialize cart count visibility
    if (cartBadgeCount) {
        cartBadgeCount.style.visibility = 'hidden';
        cartBadgeCount.textContent = '0';
    }

    // Open/Close Cart with floating badge
    if (floatingCartBadge && cartModal) {
        floatingCartBadge.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Floating cart badge clicked, toggling show class');
            cartModal.classList.toggle('show');
        });
    }

    if (closeCart && cartModal) {
        closeCart.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Close button clicked');
            cartModal.classList.remove('show');
        });
    }

    // Close modal when clicking outside
    if (cartModal) {
        cartModal.addEventListener('click', function(e) {
            if (e.target === cartModal) {
                console.log('Clicked outside modal');
                cartModal.classList.remove('show');
            }
        });
    }

    // Add to Cart Button Listeners
    addToCartBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price')) || 0;
            const image = "../"+this.getAttribute('data-image') ;
            const idp = this.getAttribute('data-product-id');

            console.log('Add to cart:', { id, name, price, idp });

            // Check if item already in cart
            const existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id,
                    idp,
                    name,
                    price,
                    image,
                    quantity: 1
                });
            }

            updateCart();
            showAddToCartFeedback(this);
        });
    });

    // Update Cart Display
    function updateCart() {
        console.log('Updating cart, items:', cart.length);
        
        if (!cartItemsContainer) {
            console.error('Cart items container not found');
            return;
        }

        // Update cart count
        if (cartBadgeCount) {
            const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            cartBadgeCount.textContent = totalItems;
            cartBadgeCount.style.visibility = totalItems > 0 ? 'visible' : 'hidden';
            
            // Add pulse animation to floating cart when it has items
            if (floatingCartBadge) {
                if (totalItems > 0) {
                    floatingCartBadge.classList.add('has-items');
                } else {
                    floatingCartBadge.classList.remove('has-items');
                }
            }
        }

        // Update cart items display
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-shopping-bag" style="font-size: 40px; margin-bottom: 15px;"></i>
                    <p>Your cart is empty</p>
                </div>
            `;
        } else {
            cartItemsContainer.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <input type="hidden" name="idp[]" value="${item.idp}">
                    <input type="hidden" name="qua[]" value="${item.quantity}">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-img" onerror="this.src='https://via.placeholder.com/70';">
                    <div class="cart-item-details">
                        <h4 class="cart-item-title">${item.name}</h4>
                        <p class="cart-item-price">${item.price.toFixed(2)} DT</p>
                        <button class="cart-item-remove" data-id="${item.id}" type="button">
                            <i class="fas fa-trash-alt"></i> Remove
                        </button>
                    </div>
                    <div class="cart-item-quantity">${item.quantity}</div>
                </div>
            `).join('');

            // Attach remove button listeners
            document.querySelectorAll('.cart-item-remove').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    console.log('Removing item:', id);
                    cart = cart.filter(item => item.id !== id);
                    updateCart();
                });
            });
        }

        // Update total
        if (cartTotalSpan) {
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotalSpan.textContent = `${total.toFixed(2)} DT`;
        }
    }

    // Show feedback animation
    function showAddToCartFeedback(button) {
        const originalHTML = button.innerHTML;
        const originalBg = button.style.backgroundColor;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.style.backgroundColor = '#4CAF50';
        button.style.color = '#fff';

        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.style.backgroundColor = originalBg;
            button.style.color = '';
        }, 900);
    }

    console.log('Cart initialization complete');
});

// Product Detail Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const productDetailOverlay = document.querySelector('#productDetailOverlay');
    const closeProductDetail = document.querySelector('#closeProductDetail');
    const productCards = document.querySelectorAll('.product-card');
    const detailAddToCartBtn = document.querySelector('#detailAddToCartBtn');
    const detailContinueShoppingBtn = document.querySelector('#detailContinueShoppingBtn');
    const detailQuantityInput = document.querySelector('#detailQuantityInput');
    
    let currentProductData = null;

    // Open product detail modal
    productCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't open detail if clicking on add-to-cart button
            if (e.target.closest('.add-to-cart')) {
                return;
            }

            currentProductData = {
                id: this.getAttribute('data-product-id'),
                name: this.getAttribute('data-name'),
                price: this.getAttribute('data-price'),
                description: this.getAttribute('data-description'),
                image: this.getAttribute('data-image')
            };

            // Populate modal
            document.querySelector('#detailProductName').textContent = currentProductData.name;
            document.querySelector('#detailProductPrice').textContent = parseFloat(currentProductData.price).toFixed(2);
            document.querySelector('#detailProductDescription').textContent = currentProductData.description;
            document.querySelector('#detailProductImage').src = currentProductData.image.replace('uploads/', '');
            detailQuantityInput.value = 1;

            // Show modal
            productDetailOverlay.classList.add('show');
            console.log('Product detail modal opened:', currentProductData);
        });
    });

    // Close product detail modal
    closeProductDetail.addEventListener('click', function() {
        productDetailOverlay.classList.remove('show');
    });

    productDetailOverlay.addEventListener('click', function(e) {
        if (e.target === productDetailOverlay) {
            productDetailOverlay.classList.remove('show');
        }
    });

    // Add to cart from detail modal
    detailAddToCartBtn.addEventListener('click', function() {
        if (!currentProductData) return;

        const quantity = parseInt(detailQuantityInput.value) || 1;
        
        // Find the add-to-cart button for this product to get data-id
        const addBtn = Array.from(document.querySelectorAll('.add-to-cart')).find(btn => 
            btn.getAttribute('data-product-id') === currentProductData.id
        );

        if (addBtn) {
            for (let i = 0; i < quantity; i++) {
                addBtn.click();
            }
            alert('Product added to cart!');
            productDetailOverlay.classList.remove('show');
        }
    });

    // Continue shopping
    detailContinueShoppingBtn.addEventListener('click', function() {
        productDetailOverlay.classList.remove('show');
    });
});