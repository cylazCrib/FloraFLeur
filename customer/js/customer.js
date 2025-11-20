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
    // Find the parent product item
    const productItem = button.closest('.product-item');
    if (!productItem) {
        console.error('Could not find product item');
        return;
    }

    // Find the quantity span
    const quantitySpan = productItem.querySelector('.quantity-value');
    if (!quantitySpan) {
        console.error('Could not find quantity span');
        return;
    }
    
    // Find the price span
    const priceSpan = productItem.querySelector('.product-price');
    if (!priceSpan) {
        console.error('Could not find price span');
        return;
    }

    // Get the current quantity
    let currentQuantity = parseInt(quantitySpan.innerText, 10);
    
    // Calculate the new quantity
    let newQuantity = currentQuantity + amount;

    // Ensure quantity doesn't go below 0
    if (newQuantity < 0) {
        newQuantity = 0;
    }

    // Calculate new total price
    const newTotal = newQuantity * unitPrice;

    // Update the span's text
    quantitySpan.innerText = newQuantity;
    priceSpan.innerText = '₱' + newTotal;
}

// ================================
// ACCOUNT PAGE EDIT
// ================================
function toggleProfileEdit() {
    const card = document.getElementById('account-card');
    const btn = document.getElementById('editProfileBtn');

    const isEditing = card.classList.contains('is-editing');

    if (isEditing) {
        // Change to display mode (Save Changes)
        // Get values from inputs
        const newName = document.getElementById('inputName').value;
        const newEmail = document.getElementById('inputEmail').value;
        const newMobile = document.getElementById('inputMobile').value;
        const newAddress = document.getElementById('inputAddress').value;

        // Set values to display spans
        document.getElementById('displayName').innerText = newName;
        document.getElementById('displayEmail').innerText = newEmail;
        document.getElementById('displayMobile').innerText = newMobile;
        document.getElementById('displayAddress').innerText = newAddress;
        // Also update profile header
        document.getElementById('displayProfileName').innerText = newName;
        document.getElementById('displayProfileEmail').innerText = newEmail;

        // Toggle class and button text
        card.classList.remove('is-editing');
        btn.innerText = 'Edit Profile';

    } else {
        // Change to edit mode (Edit Profile)
        // Get values from spans
        const oldName = document.getElementById('displayName').innerText;
        const oldEmail = document.getElementById('displayEmail').innerText;
        const oldMobile = document.getElementById('displayMobile').innerText;
        const oldAddress = document.getElementById('displayAddress').innerText;

        // Set values to inputs
        document.getElementById('inputName').value = oldName;
        document.getElementById('inputEmail').value = oldEmail;
        document.getElementById('inputMobile').value = oldMobile;
        document.getElementById('inputAddress').value = oldAddress;

        // Toggle class and button text
        card.classList.add('is-editing');
        btn.innerText = 'Save Changes';
    }
}

// ================================
// PRODUCT MODAL FUNCTIONS
// ================================
const productModal = document.getElementById('product-modal');

function showProductModal(imageSrc, title, description, price) {
    if (!productModal) {
        console.error('Modal not found');
        return;
    }

    // Update modal content
    document.getElementById('modal-product-image').src = imageSrc;
    document.getElementById('modal-product-title').innerText = title;
    document.getElementById('modal-product-description').innerText = description;
    document.getElementById('modal-product-price').innerText = price;
    
    // Reset quantity to 1
    const quantitySpan = document.getElementById('modal-quantity-value');
    if (quantitySpan) {
        quantitySpan.innerText = '1';
    }
    
    // Show the modal
    productModal.classList.add('active');
}

function hideProductModal() {
    if (!productModal) return;
    productModal.classList.remove('active');
}

// Modal Quantity Buttons
function changeModalQuantity(amount) {
    const quantitySpan = document.getElementById('modal-quantity-value');
    if (!quantitySpan) {
        console.error('Could not find modal quantity span');
        return;
    }

    let currentQuantity = parseInt(quantitySpan.innerText, 10);
    let newQuantity = currentQuantity + amount;

    // Ensure quantity doesn't go below 1 in the modal
    if (newQuantity < 1) {
        newQuantity = 1;
    }

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
let currentProductPrice = 0; // Store price for payment modal

function showPaymentModal() {
    if (!paymentModal) {
        console.error('Payment modal not found');
        return;
    }

    // Get price from product modal
    const priceString = document.getElementById('modal-product-price').innerText.replace('₱', '').replace(',', '');
    const price = parseFloat(priceString);
    
    // Get quantity from product modal
    const quantityString = document.getElementById('modal-quantity-value').innerText;
    const quantity = parseInt(quantityString, 10);
    
    if (isNaN(price) || isNaN(quantity)) {
        console.error('Invalid price or quantity');
        return;
    }

    // Calculate and set total
    const total = price * quantity;
    currentProductPrice = total;
    document.getElementById('payment-total-price').innerText = '₱' + total.toLocaleString();

    // Deselect all payment buttons
    document.querySelectorAll('.payment-btn').forEach(btn => btn.classList.remove('selected'));

    // Hide product modal, show payment modal
    hideProductModal();
    paymentModal.classList.add('active');
}

function showPaymentModalFromCart(button, unitPrice) {
    if (!paymentModal) {
        console.error('Payment modal not found');
        return;
    }
    
    // Find the parent product item
    const productItem = button.closest('.product-item');
    if (!productItem) {
        console.error('Could not find product item');
        return;
    }

    // Find the quantity span
    const quantitySpan = productItem.querySelector('.quantity-value');
    if (!quantitySpan) {
        console.error('Could not find quantity span');
        return;
    }
    
    const quantity = parseInt(quantitySpan.innerText, 10);
    const price = parseFloat(unitPrice);

    if (isNaN(price) || isNaN(quantity)) {
        console.error('Invalid price or quantity from cart');
        return;
    }
    
    // Calculate and set total
    const total = price * quantity;
    currentProductPrice = total;
    document.getElementById('payment-total-price').innerText = '₱' + total.toLocaleString();

    // Deselect all payment buttons
    document.querySelectorAll('.payment-btn').forEach(btn => btn.classList.remove('selected'));

    // Show payment modal
    paymentModal.classList.add('active');
}

function hidePaymentModal() {
    if (!paymentModal) return;
    paymentModal.classList.remove('active');
}

function selectPayment(selectedButton) {
    // Remove 'selected' from all payment buttons
    document.querySelectorAll('.payment-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    // Add 'selected' to the clicked button
    selectedButton.classList.add('selected');
}

function placeOrder() {
    hidePaymentModal();
    // Show the Thank You modal
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
    
    // Animate in
    toast.classList.remove('translate-x-[120%]');
    toast.classList.add('translate-x-0');

    // Animate out after 3 seconds
    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-[120%]');
    }, 3000);
}

// ================================
// INITIALIZE PAGE ON LOAD
// ================================
document.addEventListener('DOMContentLoaded', () => {
    showPage('home');
});