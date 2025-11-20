document.addEventListener('DOMContentLoaded', function() {
    
    // --- CSRF Token Setup ---
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

    // --- Elements ---
    const modalContainer = document.getElementById('modal-container');
    const toast = document.getElementById('toast');
    
    // --- Toast Function ---
    function showToast(message) {
        if (toast) {
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    }

    // --- Helper: Show/Hide Modal ---
    function toggleModal(modal, show = true) {
        if(modal) modal.style.display = show ? 'flex' : 'none';
    }

    // ============================================================
    //  GLOBAL EVENT LISTENER (Delegation for all buttons)
    // ============================================================
    
    // Modals
    const productModal = document.getElementById('product-form-modal');
    const itemModal = document.getElementById('item-form-modal');
    const staffModal = document.getElementById('staff-form-modal');
    const orderDetailsModal = document.getElementById('order-details-modal');
    const orderFormModal = document.getElementById('order-form-modal'); // Manual Order Modal

    // Forms
    const productForm = document.getElementById('product-form');
    const itemForm = document.getElementById('item-form');
    const staffForm = document.getElementById('staff-form');
    const orderForm = document.getElementById('order-form'); // Manual Order Form

    document.body.addEventListener('click', function(e) {
        const target = e.target;

        // --- 1. PRODUCTS ---
        if (target.id === 'new-product-btn') {
            e.preventDefault();
            if(productForm) {
                productForm.reset();
                document.getElementById('product_id').value = ''; 
                const preview = document.getElementById('image-preview');
                if(preview) { preview.src = ''; preview.style.display = 'none'; }
                
                document.getElementById('product-modal-title').textContent = 'Add New Product';
                document.getElementById('save-product-btn').textContent = 'Add Product';
                productForm.dataset.url = '/vendor/products'; 
            }
            toggleModal(productModal, true);
        }

        if (target.closest('.edit-product-btn')) {
            const row = target.closest('tr');
            const data = row.dataset;
            document.getElementById('product_id').value = data.id;
            document.getElementById('p_name').value = data.name;
            document.getElementById('p_description').value = data.description;
            document.getElementById('p_price').value = data.price;
            const preview = document.getElementById('image-preview');
            if(preview) { preview.src = data.imageUrl; preview.style.display = 'block'; }

            document.getElementById('product-modal-title').textContent = 'Edit Product';
            document.getElementById('save-product-btn').textContent = 'Update Product';
            productForm.dataset.url = data.updateUrl;
            toggleModal(productModal, true);
        }

        if (target.closest('.remove-product-btn')) {
            if (!confirm('Delete this product?')) return;
            const row = target.closest('tr');
            fetch(row.dataset.deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => { showToast(data.message); row.remove(); });
        }

        // --- 2. INVENTORY ---
        if (target.id === 'add-item-btn' || target.id === 'add-flower-btn') {
            e.preventDefault();
            if(itemForm) {
                itemForm.reset();
                document.getElementById('item_id').value = '';
                const type = target.dataset.type; 
                document.getElementById('item_type').value = type;
                document.getElementById('item-modal-title').textContent = 'Add New ' + (type === 'flower' ? 'Flower' : 'Item');
                itemForm.dataset.url = '/vendor/inventory'; 
            }
            toggleModal(itemModal, true);
        }
        
        if (target.closest('.update-item-btn')) {
            const item = target.closest('.inventory-item');
            const data = item.dataset;
            document.getElementById('item_id').value = data.id;
            document.getElementById('i_name').value = data.name;
            document.getElementById('i_code').value = data.code || '';
            document.getElementById('i_quantity').value = data.quantity;
            document.getElementById('item_type').value = data.type;
            document.getElementById('item-modal-title').textContent = 'Edit Item';
            itemForm.dataset.url = data.updateUrl;
            toggleModal(itemModal, true);
        }
        
        if (target.closest('.remove-btn')) {
            if (!confirm('Remove this item?')) return;
            const item = target.closest('.inventory-item');
            fetch(item.dataset.deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => { showToast('Item deleted'); item.remove(); });
        }

        // --- 3. STAFF MANAGEMENT ---
        if (target.id === 'add-staff-btn') {
            e.preventDefault();
            if(staffForm) {
                staffForm.reset();
                document.getElementById('staff_id').value = '';
                document.getElementById('staff-modal-title').textContent = 'Add New Staff';
                staffForm.dataset.url = '/vendor/staff';
            }
            toggleModal(staffModal, true);
        }

        if (target.closest('.edit-staff-btn')) {
            const row = target.closest('tr');
            const data = row.dataset;
            document.getElementById('staff_id').value = data.id;
            document.getElementById('s_name').value = data.name;
            document.getElementById('s_email').value = data.email;
            document.getElementById('s_phone').value = data.phone;
            document.getElementById('s_role').value = data.role;
            document.getElementById('staff-modal-title').textContent = 'Edit Staff';
            staffForm.dataset.url = data.updateUrl;
            toggleModal(staffModal, true);
        }

        if (target.closest('.toggle-status-btn')) {
            const btn = target.closest('.toggle-status-btn');
            fetch(btn.dataset.url, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => { showToast(data.message); window.location.reload(); });
        }

        // --- 4. ORDER MANAGEMENT ---
        
        // A. View Details
        if (target.closest('.view-order-btn')) {
            e.preventDefault();
            const btn = target.closest('.view-order-btn');
            const url = btn.dataset.url;
            
            fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(response => response.json())
            .then(order => {
                document.getElementById('modal-order-number').textContent = '#' + order.order_number;
                document.getElementById('modal-order-date').textContent = new Date(order.created_at).toLocaleDateString();
                
                const statusSpan = document.getElementById('modal-order-status');
                statusSpan.textContent = order.status;
                statusSpan.className = 'status status-' + order.status.toLowerCase().replace(' ', '-');

                document.getElementById('modal-customer-name').textContent = order.customer_name;
                document.getElementById('modal-customer-email').textContent = order.customer_email || 'N/A';
                document.getElementById('modal-customer-phone').textContent = order.customer_phone;
                document.getElementById('modal-delivery-address').textContent = order.delivery_address;

                const tbody = document.getElementById('modal-items-list');
                tbody.innerHTML = '';
                order.items.forEach(item => {
                    const row = document.createElement('tr');
                    const itemTotal = item.price * item.quantity;
                    row.innerHTML = `
                        <td style="padding: 8px; border-bottom: 1px solid #eee;">${item.product_name}</td>
                        <td style="padding: 8px; border-bottom: 1px solid #eee;">${item.quantity}</td>
                        <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">₱${parseFloat(item.price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                        <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">₱${itemTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    `;
                    tbody.appendChild(row);
                });
                document.getElementById('modal-grand-total').textContent = '₱' + parseFloat(order.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2});
                toggleModal(orderDetailsModal, true);
            })
            .catch(error => { console.error(error); alert('Failed to load details.'); });
        }

        // B. Add Manual Order
        if (target.id === 'new-order-btn') {
            e.preventDefault();
            if(orderForm) {
                orderForm.reset();
                orderForm.dataset.url = '/vendor/orders';
            }
            toggleModal(orderFormModal, true);
        }

        // --- GLOBAL: CLOSE MODALS ---
        if (target.matches('[data-close-modal]') || target.classList.contains('modal-overlay')) {
            const modal = target.closest('.modal-overlay') || target;
            toggleModal(modal, false);
        }
    });

    // --- GENERIC FORM HANDLER ---
    async function handleFormSubmit(e, form, modal) {
        e.preventDefault();
        const formData = new FormData(form);
        const url = form.dataset.url;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (response.ok) {
                showToast(result.message);
                toggleModal(modal, false);
                window.location.reload();
            } else {
                let msg = result.message || 'Error.';
                if(result.errors) msg = Object.values(result.errors).flat().join('\n');
                alert('Error:\n' + msg);
            }
        } catch(error) { console.error(error); alert('Network Error'); }
    }

    // Attach handlers to all forms if they exist
    if (productForm) productForm.addEventListener('submit', (e) => handleFormSubmit(e, productForm, productModal));
    if (itemForm) itemForm.addEventListener('submit', (e) => handleFormSubmit(e, itemForm, itemModal));
    if (staffForm) staffForm.addEventListener('submit', (e) => handleFormSubmit(e, staffForm, staffModal));
    if (orderForm) orderForm.addEventListener('submit', (e) => handleFormSubmit(e, orderForm, orderFormModal));

    // --- IMAGE PREVIEW ---
    const imageInput = document.getElementById('p_image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const preview = document.getElementById('image-preview');
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // --- LOGOUT ---
    const logoutBtn = document.getElementById('logout-btn');
    const profileLogoutBtn = document.getElementById('profile-logout-btn');

    const handleLogout = function() {
        if(confirm("Are you sure you want to log out?")) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout';
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden'; tokenInput.name = '_token'; tokenInput.value = csrfToken;
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }
    };

    if (logoutBtn) logoutBtn.addEventListener('click', handleLogout);
    if (profileLogoutBtn) profileLogoutBtn.addEventListener('click', handleLogout);
    
    // --- ORDER STATUS DROPDOWN ---
    window.updateOrderStatus = async function(selectElement) {
        const newStatus = selectElement.value;
        const url = selectElement.dataset.url;
        try {
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ status: newStatus })
            });
            const result = await response.json();
            if (response.ok) {
                showToast(result.message);
                selectElement.className = 'status-dropdown status-' + newStatus.toLowerCase().replace(' ', '-');
            } else {
                alert('Error: ' + result.message);
                window.location.reload();
            }
        } catch (error) {
            console.error(error);
            alert('Network error occurred.');
        }
    };
});