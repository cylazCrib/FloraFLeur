// ================================
// PAGE NAVIGATION
// ================================
function showPage(pageId) {
    // Hide all app containers
    document.querySelectorAll('.app-container').forEach(container => {
        container.classList.remove('active');
    });
    
    // Hide all page content sections
    document.querySelectorAll('.page-content').forEach(page => {
        page.classList.remove('active');
    });

    let targetContainerId;
    let targetPageId = 'page-' + pageId;

    // Determine which container to show
    if (pageId === 'dashboard') {
        targetContainerId = 'app-container-dashboard';
    } else if (
        pageId === 'cart' || 
        pageId === 'favorite' || 
        pageId === 'aboutus' || 
        pageId === 'account' || 
        pageId === 'toship' || 
        pageId === 'completed' || 
        pageId === 'refund' || 
        pageId === 'bouquetflowers' || 
        pageId === 'basketflowers' || 
        pageId === 'standeeflowers' || 
        pageId === 'valentines' || 
        pageId === 'wedding'
    ) {
        targetContainerId = 'app-container-app-floral';
    } else {
        targetContainerId = 'app-container-main';
    }

    // Show the target container and page
    const containerEl = document.getElementById(targetContainerId);
    const pageEl = document.getElementById(targetPageId);

    if (containerEl) {
        containerEl.classList.add('active');
    } else {
        console.error('Could not find container with ID:', targetContainerId);
    }

    if (pageEl) {
        pageEl.classList.add('active');
    } else {
        console.error('Could not find page with ID:', targetPageId);
    }
}

// ================================
// QUANTITY BUTTONS
// ================================
function changeQuantity(button, amount, unitPrice) {
    const productItem = button.closest('.product-item');
    if (!productItem) return;

    const quantitySpan = productItem.querySelector('.quantity-value');
    if (!quantitySpan) return;
    
    const priceSpan = productItem.querySelector('.product-price');
    if (!priceSpan) return;

    let currentQuantity = parseInt(quantitySpan.innerText, 10);
    let newQuantity = currentQuantity + amount;
    if (newQuantity < 0) newQuantity = 0;

    const newTotal = newQuantity * (unitPrice || productItem.dataset.unitPrice || 0);

    quantitySpan.innerText = newQuantity;
    priceSpan.innerText = '₱' + newTotal;
}

// ================================
// ACCOUNT PAGE EDIT
// ================================
function toggleProfileEdit() {
    const card = document.getElementById('account-card');
    const btn = document.getElementById('editProfileBtn');
    if (!card || !btn) return;

    const isEditing = card.classList.contains('is-editing');

    if (isEditing) {
        const newName = document.getElementById('inputName').value;
        const newEmail = document.getElementById('inputEmail').value;
        const newMobile = document.getElementById('inputMobile').value;
        const newAddress = document.getElementById('inputAddress').value;

        document.getElementById('displayName').innerText = newName;
        document.getElementById('displayEmail').innerText = newEmail;
        document.getElementById('displayMobile').innerText = newMobile;
        document.getElementById('displayAddress').innerText = newAddress;
        document.getElementById('displayProfileName').innerText = newName;
        document.getElementById('displayProfileEmail').innerText = newEmail;

        card.classList.remove('is-editing');
        btn.innerText = 'Edit Profile';
    } else {
        const oldName = document.getElementById('displayName').innerText;
        const oldEmail = document.getElementById('displayEmail').innerText;
        const oldMobile = document.getElementById('displayMobile').innerText;
        const oldAddress = document.getElementById('displayAddress').innerText;

        document.getElementById('inputName').value = oldName;
        document.getElementById('inputEmail').value = oldEmail;
        document.getElementById('inputMobile').value = oldMobile;
        document.getElementById('inputAddress').value = oldAddress;

        card.classList.add('is-editing');
        btn.innerText = 'Save Changes';
    }
}

// ================================
// PRODUCT MODAL FUNCTIONS
// ================================
const productModal = document.getElementById('product-modal');

function showProductModal(imageSrc, title, description, price) {
    if (!productModal) return;

    document.getElementById('modal-product-image').src = imageSrc;
    document.getElementById('modal-product-title').innerText = title;
    document.getElementById('modal-product-description').innerText = description;
    document.getElementById('modal-product-price').innerText = price;
    
    const quantitySpan = document.getElementById('modal-quantity-value');
    if (quantitySpan) quantitySpan.innerText = '1';
    
    productModal.classList.add('active');
}

function hideProductModal() {
    if (!productModal) return;
    productModal.classList.remove('active');
}

function changeModalQuantity(amount) {
    const quantitySpan = document.getElementById('modal-quantity-value');
    if (!quantitySpan) return;

    let currentQuantity = parseInt(quantitySpan.innerText, 10);
    let newQuantity = currentQuantity + amount;
    if (newQuantity < 1) newQuantity = 1;

    quantitySpan.innerText = newQuantity;
}

// ================================
// FAVORITE BUTTON
// ================================
function toggleFavorite(button) {
    button.classList.toggle('is-favorite');
}

// ================================
// PAYMENT MODAL FUNCTIONS
// ================================
const paymentModal = document.getElementById('payment-modal');
let currentProductPrice = 0; 

function showPaymentModal() {
    if (!paymentModal) return;

    const priceString = document.getElementById('modal-product-price').innerText.replace('₱', '').replace(',', '');
    const price = parseFloat(priceString);
    
    const quantityString = document.getElementById('modal-quantity-value').innerText;
    const quantity = parseInt(quantityString, 10);
    
    if (isNaN(price) || isNaN(quantity)) return;

    const total = price * quantity;
    currentProductPrice = total;
    document.getElementById('payment-total-price').innerText = '₱' + total.toLocaleString();

    document.querySelectorAll('.payment-btn').forEach(btn => btn.classList.remove('selected'));
    hideProductModal();
    paymentModal.classList.add('active');
}

function showPaymentModalFromCart(button, unitPrice) {
    if (!paymentModal) return;
    
    const productItem = button.closest('.product-item');
    if (!productItem) return;

    const quantitySpan = productItem.querySelector('.quantity-value');
    if (!quantitySpan) return;
    
    const quantity = parseInt(quantitySpan.innerText, 10);
    const price = parseFloat(unitPrice);
    if (isNaN(price) || isNaN(quantity)) return;
    
    const total = price * quantity;
    currentProductPrice = total;
    document.getElementById('payment-total-price').innerText = '₱' + total.toLocaleString();

    document.querySelectorAll('.payment-btn').forEach(btn => btn.classList.remove('selected'));
    paymentModal.classList.add('active');
}

function hidePaymentModal() {
    if (!paymentModal) return;
    paymentModal.classList.remove('active');
}

function selectPayment(selectedButton) {
    document.querySelectorAll('.payment-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    selectedButton.classList.add('selected');
}

function placeOrder() {
    hidePaymentModal();
    const thankYouModal = document.getElementById('thank-you-modal');
    if (thankYouModal) {
        thankYouModal.classList.add('active');
    }
}

function backToMain() {
    const thankYouModal = document.getElementById('thank-you-modal');
    if (thankYouModal) {
        thankYouModal.classList.remove('active');
    }
    showPage('dashboard');
}

// ================================
// TOAST NOTIFICATION
// ================================
function showToast(title, message) {
    const toast = document.getElementById('order-toast');
    if (!toast) return;
    
    toast.classList.remove('translate-x-[120%]');
    toast.classList.add('translate-x-0');

    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-[120%]');
    }, 3000);
}

// ================================
// NEW: EVENT LISTENERS
// ================================
document.addEventListener('DOMContentLoaded', () => {
    // Initial page load
    showPage('home');

    // --- Navigation ---
    // Use data-page attributes to navigate
    document.body.addEventListener('click', e => {
        const link = e.target.closest('[data-page]');
        if (link) {
            e.preventDefault();
            const pageId = link.getAttribute('data-page');
            showPage(pageId);
        }
    });

    // --- Account Page ---
    const editProfileBtn = document.getElementById('editProfileBtn');
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', toggleProfileEdit);
    }

    // --- Modals ---
    const productModalEl = document.getElementById('product-modal');
    const paymentModalEl = document.getElementById('payment-modal');
    const thankYouModalEl = document.getElementById('thank-you-modal');

    // Close buttons
    if (productModalEl) productModalEl.querySelector('[data-modal-close]').addEventListener('click', hideProductModal);
    if (paymentModalEl) paymentModalEl.querySelector('[data-modal-close]').addEventListener('click', hidePaymentModal);
    if (thankYouModalEl) thankYouModalEl.querySelector('[data-modal-close]').addEventListener('click', backToMain);

    // Product Modal: Quantity
    if (productModalEl) {
        productModalEl.querySelector('.quantity-btn-minus').addEventListener('click', () => changeModalQuantity(-1));
        productModalEl.querySelector('.quantity-btn-plus').addEventListener('click', () => changeModalQuantity(1));
        productModalEl.querySelector('.btn-checkout').addEventListener('click', showPaymentModal);
    }
    
    // Payment Modal
    if (paymentModalEl) {
        paymentModalEl.querySelectorAll('.payment-btn').forEach(btn => {
            btn.addEventListener('click', () => selectPayment(btn));
        });
        paymentModalEl.querySelector('.btn-place-order').addEventListener('click', placeOrder);
    }
    
    // Thank You Modal
    if (thankYouModalEl) {
        thankYouModalEl.querySelector('.btn-back-main').addEventListener('click', backToMain);
    }

    // --- Dynamic Content Clicks (using event delegation) ---
    document.body.addEventListener('click', e => {
        const target = e.target;

        // Favorite button
        const favBtn = target.closest('.favorite-btn');
        if (favBtn) {
            toggleFavorite(favBtn);
            return; // Stop further processing
        }
        
        // Product Card: Buy Now / Show Modal
        const showModalBtn = target.closest('[data-modal-product]');
        if (showModalBtn) {
            const card = showModalBtn.closest('.product-card');
            if (card) {
                showProductModal(
                    card.dataset.image,
                    card.dataset.title,
                    card.dataset.description,
                    card.dataset.price
                );
            }
            return;
        }

        // Cart: Quantity Buttons
        const quantityBtn = target.closest('.quantity-btn');
        if (quantityBtn && quantityBtn.closest('.product-item')) {
            const amount = quantityBtn.classList.contains('quantity-btn-plus') ? 1 : -1;
            changeQuantity(quantityBtn, amount);
            return;
        }

        // Cart: Buy Now Button
        const cartBuyBtn = target.closest('.btn-buy-from-cart');
        if (cartBuyBtn) {
            const item = cartBuyBtn.closest('.product-item');
            if (item) {
                showPaymentModalFromCart(cartBuyBtn, item.dataset.unitPrice);
            }
            return;
        }
    });
});