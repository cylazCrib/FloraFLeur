document.addEventListener('DOMContentLoaded', function() {
    // --- MOCK DATA ---
    let ordersData = [
        { id: 'FF-1101', customer: 'Maria Lopez', phone: '0915-457-8943', email: 'maria@example.com', items: '1234-Basket Bouquet', quantity: '2pcs', delivery: 'October 10, 2:00pm', status: 'Pending', driver: null },
        { id: 'FF-1102', customer: 'John Rivera', phone: '0915-876-0437', email: 'john@example.com', items: '54321-Wedding Bouquet', quantity: '1 pc', delivery: 'October 10, 4:00pm', status: 'Being Made', driver: null },
        { id: 'FF-1103', customer: 'Elia Santos', phone: '0915-876-0437', email: 'elia@example.com', items: '7894-Standee Flowers', quantity: '4pcs', delivery: 'October 10, 4:00pm', status: 'Delivered', driver: 'Theodore Sanchez' },
    ];
    let staffData = [
        { id: 0, name: 'Kassandra Olvis', email: 'kasu@gmail.com', phone: '09451235847', role: 'Admin', status: 'Active' },
        { id: 1, name: 'Cyla Longos', email: 'cyla@gmail.com', phone: '09451235847', role: 'Manager', status: 'Active' },
        { id: 2, name: 'Veronica Dela Siete', email: 'ver@gmail.com', phone: '09451235847', role: 'Florist', status: 'Active' },
        { id: 3, name: 'Theodore Sanchez', email: 'theo@gmail.com', phone: '09451235847', role: 'Driver', status: 'Active' },
        { id: 4, name: 'Michael Chen', email: 'michael@gmail.com', phone: '09123456789', role: 'Driver', status: 'Active' },
        { id: 5, name: 'Philip Reyes', email: 'philip@gmail.com', phone: '09987654321', role: 'Driver', status: 'Suspended' },
    ];
    let itemsData = [
        { id: 0, name: 'Basket Bouquet', code: '1234', quantity: 12, type: 'item' },
        { id: 1, name: 'Wedding Bouquet', code: '5345', quantity: 8, type: 'item' },
        { id: 2, name: 'Standee Flowers', code: '5345', quantity: 5, type: 'item' },
        { id: 3, name: 'Sympathy Wreath', code: '5345', quantity: 15, type: 'item' },
        { id: 4, name: 'Red Roses', code: null, quantity: 20, type: 'flower' },
        { id: 5, name: 'White Lilies', code: null, quantity: 4, type: 'flower' },
        { id: 6, name: 'Sunflowers', code: null, quantity: 18, type: 'flower' },
        { id: 7, name: 'Tulips', code: null, quantity: 3, type: 'flower' },
    ];
    let productsData = [
        { id: 0, name: 'Rose Bouquet', description: 'A beautiful arrangement of red roses.', price: 1250, image: 'https://images.unsplash.com/photo-1518701057944-554c5373a2a8?q=80&w=2070' },
        { id: 1, name: 'Basket Bouquet', description: 'Mixed flowers in a decorative basket.', price: 2300, image: 'https://images.unsplash.com/photo-1518531933037-91b2f5f229cc?q=80&w=2726' },
        // === IMAGE UPDATE ===
        { id: 2, name: 'Wedding Bouquet', description: 'Elegant bouquet for weddings.', price: 3480, image: 'https://i.pinimg.com/736x/b7/61/ce/b761ce34d3503f1673519d6a78216335.jpg' },
        // === END UPDATE ===
    ];


    // --- DOM ELEMENTS ---
    const navItems = document.querySelectorAll('.nav-item[data-target]');
    const views = document.querySelectorAll('.view');
    const periodOptions = document.querySelectorAll('.period-option');
    const modalContainer = document.getElementById('modal-container');
    const toast = document.getElementById('toast');

    // --- TEMPLATES ---
    const orderRowTemplate = document.getElementById('order-row-template')?.content;
    const deliveryRowTemplate = document.getElementById('delivery-row-template')?.content;
    const staffRowTemplate = document.getElementById('staff-row-template')?.content;
    const inventoryItemTemplate = document.getElementById('inventory-item-template')?.content;
    const productRowTemplate = document.getElementById('product-row-template')?.content;
    const orderDetailsTemplate = document.getElementById('order-details-template')?.content;
    
    // --- NAVIGATION ---
    function navigateToView(targetId) {
        if (!targetId) return;
        navItems.forEach(item => {
            item.classList.toggle('active', item.dataset.target === targetId);
        });
        views.forEach(view => {
            view.classList.toggle('active-view', view.id === targetId);
        });
    }

    navItems.forEach(item => item.addEventListener('click', function(e) {
        e.preventDefault();
        navigateToView(this.dataset.target);
    }));

    periodOptions.forEach(option => option.addEventListener('click', function() {
        periodOptions.forEach(opt => opt.classList.remove('selected'));
        this.classList.add('selected');
    }));

    // --- UTILITY FUNCTIONS ---
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }

    function showModal(modalId, title) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const modalTitle = modal.querySelector('.modal-title');
            if (modalTitle && title) modalTitle.textContent = title;
            modal.style.display = 'flex';
        }
    }

    function hideAllModals() {
        modalContainer.querySelectorAll('.modal-overlay').forEach(modal => modal.style.display = 'none');
    }
    
    // --- MODAL & FORM HANDLING ---
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-modal-target]')) {
            showModal(e.target.dataset.modalTarget, e.target.dataset.modalTitle);
        }
    });
    modalContainer.addEventListener('click', function(event) {
        if (event.target.matches('[data-close-modal]') || event.target.closest('[data-close-modal]')) hideAllModals();
        if (event.target.matches('.modal-overlay')) hideAllModals();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") hideAllModals();
    });


    // --- RENDER FUNCTIONS ---
    const renderOrdersTable = () => {
        const tableBody = document.getElementById('orders-table-body');
        tableBody.innerHTML = '';
        if (!orderRowTemplate) return;

        ordersData.forEach((order, index) => {
            const row = orderRowTemplate.cloneNode(true);
            const statusClass = `status-${order.status.toLowerCase().replace(' ', '-')}`;
            
            row.querySelector('tr').dataset.index = index;
            row.querySelector('[data-field="id"]').textContent = order.id;
            row.querySelector('[data-field="customer"]').prepend(order.customer);
            row.querySelector('[data-field="phone"]').textContent = order.phone;
            row.querySelector('[data-field="items"]').textContent = order.items;
            row.querySelector('[data-field="quantity"]').textContent = order.quantity;
            row.querySelector('[data-field="delivery"]').textContent = order.delivery;
            
            const statusSpan = row.querySelector('[data-field="status"]');
            statusSpan.textContent = order.status;
            statusSpan.className = `status ${statusClass}`;
            
            tableBody.appendChild(row);
        });
    };

    const renderDeliveryTable = () => {
        const tableBody = document.getElementById('delivery-table-body');
        const drivers = staffData.filter(s => s.role === 'Driver');
        tableBody.innerHTML = '';
        if (!deliveryRowTemplate) return;

        ordersData.forEach((order, index) => {
            const row = deliveryRowTemplate.cloneNode(true);
            const isAssigned = !!order.driver;
            
            row.querySelector('tr').dataset.index = index;
            row.querySelector('[data-field="id"]').textContent = order.id;
            row.querySelector('[data-field="customer"]').prepend(order.customer);
            row.querySelector('[data-field="phone"]').textContent = order.phone;
            row.querySelector('[data-field="items"]').textContent = order.items;
            row.querySelector('[data-field="delivery"]').textContent = order.delivery;

            const statusSpan = row.querySelector('[data-field="status"]');
            statusSpan.textContent = isAssigned ? 'Ready for Delivery' : 'Pending';
            statusSpan.className = `status status-${isAssigned ? 'ready' : 'pending'}`;

            const driverSelect = row.querySelector('[data-field="driver-select"]');
            drivers.forEach(d => {
                const option = document.createElement('option');
                option.value = d.name;
                option.textContent = d.name;
                if (order.driver === d.name) {
                    option.selected = true;
                }
                driverSelect.appendChild(option);
            });
            
            if (isAssigned) {
                driverSelect.disabled = true;
                row.querySelector('.assign-driver-btn').disabled = true;
            }
            
            tableBody.appendChild(row);
        });
    };

    const renderStaffTable = () => {
        const staffTableBody = document.getElementById('staff-table-body');
        staffTableBody.innerHTML = '';
        if (!staffRowTemplate) return;

        staffData.forEach((staff, index) => {
            const row = staffRowTemplate.cloneNode(true);
            const roleClass = `status-${staff.role.toLowerCase()}`;
            const statusClass = staff.status === 'Active' ? 'status-approved' : 'status-suspended';
            const toggleButtonText = staff.status === 'Active' ? 'Suspend' : 'Activate';

            row.querySelector('tr').dataset.index = index;
            row.querySelector('[data-field="name"]').textContent = staff.name;
            row.querySelector('[data-field="email"]').textContent = staff.email;
            row.querySelector('[data-field="phone"]').textContent = staff.phone;
            
            const roleSpan = row.querySelector('[data-field="role"]');
            roleSpan.textContent = staff.role;
            roleSpan.className = `status ${roleClass}`;
            
            const statusSpan = row.querySelector('[data-field="status"]');
            statusSpan.textContent = staff.status;
            statusSpan.className = `status ${statusClass}`;
            
            row.querySelector('[data-field="toggle-status"]').textContent = toggleButtonText;
            
            staffTableBody.appendChild(row);
        });
    };

    const renderInventory = () => {
        const itemsList = document.getElementById('items-inventory-list');
        const flowersList = document.getElementById('flowers-inventory-list');
        itemsList.innerHTML = '';
        flowersList.innerHTML = '';
        if (!inventoryItemTemplate) return;

        itemsData.forEach((item, index) => {
            const list = item.type === 'item' ? itemsList : flowersList;
            const itemEl = inventoryItemTemplate.cloneNode(true);

            itemEl.querySelector('.inventory-item').dataset.index = index;
            
            const codeEl = itemEl.querySelector('[data-field="code"]');
            if (item.code) {
                codeEl.textContent = `Code: ${item.code} - `;
            } else {
                codeEl.remove();
            }
            
            itemEl.querySelector('[data-field="name"]').textContent = item.name;
            itemEl.querySelector('[data-field="quantity"]').textContent = item.quantity;
            
            const stockAlertEl = itemEl.querySelector('[data-field="stock-alert"]');
            if (item.quantity <= 5) {
                stockAlertEl.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alert!`;
            } else {
                stockAlertEl.remove();
            }

            list.appendChild(itemEl);
        });
    };

    const renderProductsTable = () => {
        const tableBody = document.getElementById('products-table-body');
        tableBody.innerHTML = '';
        if (!productRowTemplate) return;

        productsData.forEach((product, index) => {
            const row = productRowTemplate.cloneNode(true);
            row.querySelector('tr').dataset.index = index;
            
            const imgEl = row.querySelector('[data-field="image"]');
            if (product.image) {
                imgEl.src = product.image;
                imgEl.alt = product.name;
            } else {
                imgEl.parentElement.textContent = 'No Image'; // Replace td content if no image
            }

            row.querySelector('[data-field="name"]').textContent = product.name;
            row.querySelector('[data-field="description"]').textContent = product.description;
            row.querySelector('[data-field="price"]').textContent = `₱${product.price.toFixed(2)}`;
            
            tableBody.appendChild(row);
        });
    };

    const renderNotificationOrderSelect = () => {
        const selectElement = document.getElementById('select-order');
        selectElement.innerHTML = '<option value="">Choose an order</option>';
        ordersData.forEach(order => {
            const option = document.createElement('option');
            option.value = order.id;
            option.textContent = `#${order.id} - ${order.customer}`;
            selectElement.appendChild(option);
        });
    };
    
    const checkStockAlert = () => {
        const lowStock = itemsData.some(item => item.quantity <= 5);
        const alertBtn = document.getElementById('stock-alert-btn');
        alertBtn.style.display = lowStock ? 'inline-flex' : 'none';
    };


    // --- EVENT LISTENERS FOR DYNAMIC CONTENT ---

    // Dashboard
    document.getElementById('stock-alert-btn').addEventListener('click', (e) => {
        e.preventDefault();
        navigateToView('inventory-view');
    });

    // Orders Page
    document.getElementById('orders-view').addEventListener('click', function(e){
        const target = e.target;
        const row = target.closest('tr');

        if (target.id === 'new-order-btn') {
            e.preventDefault();
            document.getElementById('order-form').reset();
            document.querySelector('#order-form input[name="orderId"]').value = '';
            showModal('order-form-modal', 'Add New Order');
            return;
        }
        if (!row) return;


        if(target.closest('.view-order-btn')) { 
            e.preventDefault();
            if (!orderDetailsTemplate) return;

            const index = row.dataset.index;
            const order = ordersData[index];
            const content = orderDetailsTemplate.cloneNode(true);

            // Populate template
            content.querySelector('[data-field="id"]').textContent = `#${order.id}`;
            content.querySelector('[data-field="delivery"]').textContent = order.delivery;
            content.querySelector('[data-field="customer"]').textContent = order.customer;
            content.querySelector('[data-field="email"]').textContent = order.email || 'N/A';
            content.querySelector('[data-field="phone"]').textContent = order.phone;
            content.querySelector('[data-field="quantity"]').textContent = order.quantity;
            content.querySelector('[data-field="items"]').textContent = order.items;
            // Note: Total is static in the template as per mock data

            const modalContentEl = document.getElementById('order-details-content');
            modalContentEl.innerHTML = ''; // Clear previous
            modalContentEl.appendChild(content);

            showModal('order-details-modal'); 
        }
        else if(target.closest('.update-order-status-btn')) {
            e.preventDefault();
            const index = row.dataset.index;
            const statuses = ['Pending', 'Being Made', 'Delivered'];
            const currentStatus = ordersData[index].status;
            const nextStatus = statuses[(statuses.indexOf(currentStatus) + 1) % statuses.length];
            ordersData[index].status = nextStatus;
            renderOrdersTable();
            showToast(`Order #${ordersData[index].id} status updated to "${nextStatus}"`);
        }
        else if(target.closest('.assign-driver-btn')) {
            e.preventDefault();
            const index = row.dataset.index;
            const select = row.querySelector('.driver-select');
            const driverName = select.value;
            if(driverName) {
                ordersData[index].driver = driverName;
                renderDeliveryTable();
                showToast(`Driver ${driverName} assigned to order #${ordersData[index].id}`);
            } else {
                showToast('Please select a driver first.');
            }
        }
    });
    
    // Inventory Page
    document.getElementById('inventory-view').addEventListener('click', function(e){
        const target = e.target;
        if(target.matches('[data-modal-target]')) {
            e.preventDefault();
            const form = document.getElementById('item-form');
            form.reset();
            form.querySelector('input[name="itemId"]').value = '';
            form.querySelector('input[name="itemType"]').value = target.dataset.modalType;
            showModal(target.dataset.modalTarget, 'Add New Item');
        }
        else if(target.closest('.update-item-btn')) {
            e.preventDefault();
            const itemEl = target.closest('.inventory-item');
            const index = itemEl.dataset.index;
            const item = itemsData[index];
            const form = document.getElementById('item-form');
            form.querySelector('input[name="itemId"]').value = index;
            form.querySelector('input[name="itemType"]').value = item.type;
            form.querySelector('input[name="name"]').value = item.name;
            form.querySelector('input[name="code"]').value = item.code || '';
            form.querySelector('input[name="quantity"]').value = item.quantity;
            showModal('item-form-modal', 'Update Item');
        }
        else if(target.closest('.remove-btn')) {
            e.preventDefault();
            const itemEl = target.closest('.inventory-item');
            const index = itemEl.dataset.index;
            const item = itemsData[index];

            const confirmModalText = document.getElementById('confirm-modal-text');
            confirmModalText.textContent = `Do you really want to remove "${item.name}"? This action cannot be undone.`;
            const confirmBtn = document.getElementById('confirm-modal-btn');
            confirmBtn.style.backgroundColor = '#D32F2F';
            confirmBtn.textContent = 'Confirm';
            showModal('confirm-modal');
            
            confirmBtn.onclick = () => {
                itemsData.splice(index, 1);
                renderInventory(); // Re-render after deletion
                checkStockAlert();
                hideAllModals();
                showToast('Item removed successfully!');
            };
        }
    });

    // Products Page
    document.getElementById('products-view').addEventListener('click', function(e) {
        const target = e.target;
        const row = target.closest('tr');

        if (target.id === 'new-product-btn') {
            e.preventDefault();
            const form = document.getElementById('product-form');
            form.reset();
            form.querySelector('input[name="productId"]').value = '';
            document.getElementById('image-preview').style.display = 'none';
            document.getElementById('image-preview').src = '';
            showModal('product-form-modal', 'Add New Product');
            return;
        }

        if (!row) return;

        if (target.closest('.edit-product-btn')) {
            e.preventDefault();
            const index = row.dataset.index;
            const product = productsData[index];
            const form = document.getElementById('product-form');
            form.querySelector('input[name="productId"]').value = index;
            form.querySelector('input[name="name"]').value = product.name;
            form.querySelector('textarea[name="description"]').value = product.description;
            form.querySelector('input[name="price"]').value = product.price;
            
            const preview = document.getElementById('image-preview');
            if (product.image) {
                preview.src = product.image;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
            showModal('product-form-modal', 'Edit Product');
        } else if (target.closest('.remove-product-btn')) {
            e.preventDefault();
            const index = row.dataset.index;
            const product = productsData[index];

            const confirmModalText = document.getElementById('confirm-modal-text');
            confirmModalText.textContent = `Do you really want to remove "${product.name}"? This action cannot be undone.`;
            const confirmBtn = document.getElementById('confirm-modal-btn');
            confirmBtn.style.backgroundColor = '#D32F2F';
            confirmBtn.textContent = 'Confirm';
            showModal('confirm-modal');
            
            confirmBtn.onclick = () => {
                productsData.splice(index, 1);
                renderProductsTable(); // Re-render after deletion
                hideAllModals();
                showToast('Product removed successfully!');
            };
        }
    });

    // Owners Page
    document.getElementById('owners-view').addEventListener('click', function(e){
         if(e.target.id === 'add-staff-btn') {
            e.preventDefault(); 
            const form = document.getElementById('staff-form');
            form.reset();
            form.querySelector('input[name="staffId"]').value = '';
            showModal('staff-form-modal', 'Add New Staff');
        }
         else if(e.target.closest('.edit-staff-btn')) {
            e.preventDefault(); 
            const row = e.target.closest('tr');
            const index = row.dataset.index;
            const staff = staffData[index];
            const form = document.getElementById('staff-form');
            form.querySelector('input[name="staffId"]').value = index;
            form.querySelector('input[name="name"]').value = staff.name;
            form.querySelector('input[name="email"]').value = staff.email;
            form.querySelector('input[name="phone"]').value = staff.phone;
            form.querySelector('select[name="role"]').value = staff.role;
            showModal('staff-form-modal', 'Edit Staff Information');
        }
         else if (e.target.closest('.toggle-status-btn')) {
            e.preventDefault();
            const row = e.target.closest('tr');
            const index = row.dataset.index;
            const staff = staffData[index];
            staff.status = staff.status === 'Active' ? 'Suspended' : 'Active';
            renderStaffTable();
            showToast(`${staff.name} has been ${staff.status.toLowerCase()}.`);
        }
    });

    // Logout
    // Logout
const handleLogout = () => {
    const confirmModalText = document.getElementById('confirm-modal-text');
    if (confirmModalText) confirmModalText.textContent = 'Are you sure you want to log out?';

    const confirmBtn = document.getElementById('confirm-modal-btn');
    if (confirmBtn) {
        confirmBtn.style.backgroundColor = '#A38D8C'; // This matches your vendor CSS
        confirmBtn.textContent = 'Log Out';

        // THIS IS THE NEW LOGIC
        confirmBtn.onclick = () => {
            hideAllModals();
            showToast('Logging out...');

            // Get CSRF token from the meta tag
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Create a dynamic form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout'; // This is the route Breeze created

            // Add the CSRF token as an input
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = token;
            form.appendChild(tokenInput);

            // Add form to page, submit it, then remove it
            document.body.appendChild(form);
            form.submit();
        };
        // END OF NEW LOGIC
    }
    showModal('confirm-modal');
};

// Add null checks for both logout buttons
const logoutBtn = document.getElementById('logout-btn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', handleLogout);
}

const profileLogoutBtn = document.getElementById('profile-logout-btn');
if (profileLogoutBtn) {
    profileLogoutBtn.addEventListener('click', handleLogout);
}
    // --- FORM SUBMISSION HANDLERS ---
    document.getElementById('staff-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const staffId = formData.get('staffId');
        const staffMember = {
            name: formData.get('name'), email: formData.get('email'),
            phone: formData.get('phone'), role: formData.get('role'),
            status: staffId ? staffData[staffId].status : 'Active'
        };
        
        if (staffId) { // Update
            staffData[staffId] = { ...staffData[staffId], ...staffMember };
             showToast('Staff updated successfully!');
        } else { // Add
            staffMember.id = staffData.length;
            staffData.push(staffMember);
            showToast('Staff added successfully!');
        }
        renderStaffTable();
        hideAllModals();
        this.reset();
    });

    document.getElementById('item-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const itemId = formData.get('itemId');
        const item = {
            name: formData.get('name'), code: formData.get('code') || null,
            quantity: parseInt(formData.get('quantity')), type: formData.get('itemType')
        };
        
        if (itemId) { // Update
            itemsData[itemId] = { ...itemsData[itemId], ...item };
            showToast('Item updated successfully!');
        } else { // Add
            item.id = itemsData.length;
            itemsData.push(item);
            showToast('Item added successfully!');
        }
        renderInventory();
        checkStockAlert();
        hideAllModals();
        this.reset();
    });

    document.getElementById('product-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const productId = formData.get('productId');
        const file = formData.get('image');
        const product = {
            name: formData.get('name'),
            description: formData.get('description'),
            price: parseFloat(formData.get('price')),
            image: '' // Placeholder
        };

        const handleImage = (callback) => {
            if (file && file.name) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    product.image = event.target.result;
                    callback();
                };
                reader.readAsDataURL(file);
            } else {
                // Keep existing image if editing and no new file
                if (productId && productsData[productId]) {
                    product.image = productsData[productId].image;
                }
                callback();
            }
        };

        handleImage(() => {
            if (productId) { // Update
                productsData[productId] = { ...productsData[productId], ...product };
                showToast('Product updated successfully!');
            } else { // Add
                product.id = productsData.length;
                productsData.push(product);
                showToast('Product added successfully! It is now available in the customer dashboard.');
            }
            renderProductsTable();
            hideAllModals();
            this.reset();
            document.getElementById('image-preview').style.display = 'none';
        });
    });

    // Image Preview on File Select
    document.querySelector('input[name="image"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('image-preview');
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
             preview.src = '';
             preview.style.display = 'none';
        }
    });
    
    document.getElementById('notification-form').addEventListener('submit', (e) => { e.preventDefault(); showToast('Notification sent!'); e.target.reset(); });
    document.getElementById('announcement-form').addEventListener('submit', (e) => { e.preventDefault(); showToast('Announcement posted!'); e.target.reset(); });
    document.getElementById('gmail-form').addEventListener('submit', (e) => { e.preventDefault(); showToast('Connecting to Gmail...'); });
    document.getElementById('generate-pdf-btn').addEventListener('click', () => showToast('Generating PDF...'));
    document.getElementById('download-pdf-btn').addEventListener('click', () => showToast('Downloading PDF...'));
    
    // --- INITIAL RENDER ---
    renderOrdersTable();
    renderDeliveryTable();
    renderStaffTable();
    renderInventory();
    renderProductsTable();
    renderNotificationOrderSelect();
    checkStockAlert();
});