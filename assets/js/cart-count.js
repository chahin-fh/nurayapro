(function(){
    async function updateCartCount(){
        try{
            const res = await fetch('api/cart.php?action=count');
            if(!res.ok) throw new Error('Network response was not ok');
            const data = await res.json();
            const count = (data && typeof data.count === 'number') ? data.count : 0;

            const selectors = ['[data-cart-count]','#cartCount','.cart-count','#cartBadgeCount'];
            selectors.forEach(sel => {
                document.querySelectorAll(sel).forEach(el => {
                    el.textContent = count;
                    if (count > 0) {
                        el.style.visibility = 'visible';
                        el.removeAttribute('aria-hidden');
                    } else {
                        el.style.visibility = 'hidden';
                        el.setAttribute('aria-hidden', 'true');
                    }
                });
            });
        }catch(err){
            console.error('Error updating cart count:', err);
        }
    }

    document.addEventListener('DOMContentLoaded', updateCartCount);
    window.updateCartCount = updateCartCount;
})();
