// Enhanced JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initAnimations();
    
    // Currency selection
    const currencyLinks = document.querySelectorAll('.currency-select');
    currencyLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const currency = this.dataset.currency;
            
            showLoading(this);
            
            fetch('pages/set_currency.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({currency: currency})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Currency updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Failed to update currency', 'error');
                }
            })
            .catch(() => {
                showNotification('Network error occurred', 'error');
            })
            .finally(() => {
                hideLoading(this);
            });
        });
    });

    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            
            showLoading(this);
            
            fetch('pages/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({product_id: productId, quantity: 1})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Product added to cart!', 'success');
                    updateCartCount();
                } else {
                    showNotification('Error adding product to cart', 'error');
                }
            })
            .catch(() => {
                showNotification('Network error occurred', 'error');
            })
            .finally(() => {
                hideLoading(this);
            });
        });
    });

    // Update cart quantity
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = this.value;
            
            if (quantity < 1) {
                this.value = 1;
                return;
            }
            
            fetch('pages/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({product_id: productId, quantity: quantity})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Cart updated!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Failed to update cart', 'error');
                }
            });
        });
    });
    
    // Remove from cart
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove this item?')) {
                const productId = this.dataset.productId;
                
                fetch('pages/update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({product_id: productId, quantity: 0})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Item removed from cart', 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                });
            }
        });
    });
    
    // Add to wishlist functionality
    const addToWishlistButtons = document.querySelectorAll('.add-to-wishlist');
    addToWishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            
            showLoading(this);
            
            fetch('pages/add_wishlist.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({product_id: productId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Added to wishlist!', 'success');
                } else {
                    showNotification(data.message || 'Error adding to wishlist', 'error');
                }
            })
            .finally(() => {
                hideLoading(this);
            });
        });
    });
    
    // Search functionality
    initSearch();
});

// Utility functions
function showLoading(element) {
    const originalText = element.innerHTML;
    element.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    element.disabled = true;
    element.dataset.originalText = originalText;
}

function hideLoading(element) {
    element.innerHTML = element.dataset.originalText;
    element.disabled = false;
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    notification.className = `notification ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg`;
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        const currentCount = parseInt(cartCount.textContent) || 0;
        cartCount.textContent = currentCount + 1;
    }
}

function initAnimations() {
    const elements = document.querySelectorAll('.fade-in');
    elements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
    });
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

function initSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length > 2 || this.value.length === 0) {
                    this.form.submit();
                }
            }, 500);
        });
    }
}