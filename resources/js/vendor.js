document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const app = {
        init() {
            this.listeners();
            // Ensure Dashboard is active by default if no other view is active
            if (!document.querySelector('.view.active-view')) {
                const dashboard = document.getElementById('dashboard-view');
                if (dashboard) dashboard.classList.add('active-view');
            }
        },

        listeners() {
            // --- GLOBAL CLICK LISTENER ---
            document.addEventListener('click', e => {
                const t = e.target;

                // 1. NAVIGATION (Sidebar)
                if (t.closest('.nav-item') && t.closest('.nav-item').id !== 'logout-btn') {
                    e.preventDefault();
                    const target = t.closest('.nav-item').dataset.target;
                    if (target) {
                        document.querySelectorAll('.view').forEach(v => v.classList.remove('active-view'));
                        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                        const view = document.getElementById(target);
                        if(view) view.classList.add('active-view');
                        t.closest('.nav-item').classList.add('active');
                    }
                }

                // 2. OPEN MODALS (Add Buttons)
                if (t.closest('#new-order-btn')) this.openModal('order-form-modal');
                
                if (t.closest('#new-product-btn')) {
                    this.resetForm('product-form');
                    this.openModal('product-form-modal');
                }
                
                if (t.closest('#new-staff-btn')) {
                    this.resetForm('staff-form');
                    this.openModal('staff-form-modal');
                }

                if (t.closest('#new-item-btn')) {
                    this.resetForm('inventory-form');
                    const typeInput = document.getElementById('inv-type');
                    if(typeInput) typeInput.value = 'item';
                    
                    const title = document.getElementById('inv-modal-title');
                    if(title) title.innerText = 'Add Item';
                    
                    this.openModal('inventory-form-modal');
                }
                
                if (t.closest('#new-flower-btn')) {
                    this.resetForm('inventory-form');
                    const typeInput = document.getElementById('inv-type');
                    if(typeInput) typeInput.value = 'flower';
                    
                    const title = document.getElementById('inv-modal-title');
                    if(title) title.innerText = 'Add Flower';
                    
                    this.openModal('inventory-form-modal');
                }

                // 3. EDIT ACTIONS (Populate Forms)
                
                // --- PRODUCT EDIT ---
                if (t.closest('.edit-product-btn')) {
                    const row = t.closest('tr');
                    const d = row.dataset;

                    // Populate Fields
                    if(document.getElementById('product_id')) document.getElementById('product_id').value = d.id;
                    if(document.getElementById('p_name')) document.getElementById('p_name').value = d.name;
                    if(document.getElementById('p_description')) document.getElementById('p_description').value = d.description;
                    if(document.getElementById('p_price')) document.getElementById('p_price').value = d.price;
                    if(document.getElementById('p_category')) document.getElementById('p_category').value = d.category;

                    // Populate Occasion
                    const occInput = document.getElementById('p_occasion');
                    if (occInput && d.occasion) {
                        occInput.value = d.occasion;
                    }

                    // Update Modal Title (Safely)
                    const title = document.getElementById('product-modal-title');
                    if(title) title.innerText = "Edit Product";
                    
                    this.openModal('product-form-modal');
                }

                // --- INVENTORY EDIT ---
                if (t.closest('.update-item-btn')) {
                    const d = t.closest('.update-item-btn').dataset;
                    if(document.getElementById('item_id')) document.getElementById('item_id').value = d.id;
                    if(document.getElementById('inv_name')) document.getElementById('inv_name').value = d.name;
                    if(document.getElementById('inv_code')) document.getElementById('inv_code').value = d.code;
                    if(document.getElementById('inv_qty')) document.getElementById('inv_qty').value = d.quantity;
                    if(document.getElementById('inv-type')) document.getElementById('inv-type').value = d.type;
                    
                    const title = document.getElementById('inv-modal-title');
                    if(title) title.innerText = 'Edit ' + d.type;
                    
                    this.openModal('inventory-form-modal');
                }

                // --- STAFF EDIT ---
                if (t.closest('.edit-staff-btn')) {
                    const d = t.closest('.edit-staff-btn').dataset;
                    if(document.getElementById('staff_id')) document.getElementById('staff_id').value = d.id;
                    if(document.getElementById('s_name')) document.getElementById('s_name').value = d.name;
                    if(document.getElementById('s_email')) document.getElementById('s_email').value = d.email;
                    if(document.getElementById('s_phone')) document.getElementById('s_phone').value = d.phone;
                    if(document.getElementById('s_role')) document.getElementById('s_role').value = d.role;
                    this.openModal('staff-form-modal');
                }

                // 4. CLOSE MODALS
                if (t.matches('[data-close-modal]') || t.closest('.modal-close-btn')) {
                    document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
                }

                // 5. DELETE ACTIONS
                if (t.closest('.remove-product-btn')) {
                    const row = t.closest('tr');
                    if(row && row.dataset.deleteUrl) this.deleteItem(row.dataset.deleteUrl);
                }

                if (t.closest('.del-inv')) this.deleteItem('/vendor/inventory/' + t.closest('.del-inv').dataset.id);
                if (t.closest('.del-staff')) this.deleteItem('/vendor/staff/' + t.closest('.del-staff').dataset.id);


                // 6. ORDER STATUS & DRIVER ACTIONS
                if (t.closest('.view-order-btn')) this.viewDetails(t.closest('.view-order-btn').dataset);

                if (t.closest('.update-status-btn')) {
                    const btn = t.closest('.update-status-btn');
                    const select = document.getElementById(`status-${btn.dataset.id}`);
                    if(select) this.updateStatus(btn.dataset.id, select.value);
                }

                if (t.closest('.assign-driver-btn')) {
                    const btn = t.closest('.assign-driver-btn');
                    this.assignDriver(btn.dataset.id);
                }
                
                // 7. REQUEST ACTIONS
                if (t.closest('.update-req-btn')) {
                    const btn = t.closest('.update-req-btn');
                    const id = btn.dataset.id;
                    const statusInput = document.getElementById(`req-status-${id}`);
                    
                    if (statusInput && confirm(`Update request status to "${statusInput.value}"?`)) {
                        fetch(`/vendor/requests/${id}/status`, {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify({ status: statusInput.value })
                        }).then(() => window.location.reload());
                    }
                }
            });

            // --- FORM SUBMISSIONS ---
            
            const post = (url, body) => {
                fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: body })
                    .then(res => res.json())
                    .then(data => { alert(data.message); window.location.reload(); })
                    .catch(err => { console.error(err); alert('Operation failed.'); });
            };

            // Product Form
            const prodForm = document.getElementById('product-form');
            if (prodForm) {
                prodForm.addEventListener('submit', e => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const id = document.getElementById('product_id').value;
                    const url = id ? `/vendor/products/${id}` : '/vendor/products';
                    if (id) formData.append('_method', 'PUT');

                    post(url, formData);
                });
            }

            // Other Forms
            const invForm = document.getElementById('inventory-form');
            if (invForm) invForm.addEventListener('submit', e => { e.preventDefault(); post('/vendor/inventory', new FormData(e.target)); });

            const staffForm = document.getElementById('staff-form');
            if (staffForm) staffForm.addEventListener('submit', e => { e.preventDefault(); post('/vendor/staff', new FormData(e.target)); });

            const orderForm = document.getElementById('order-form');
            if (orderForm) orderForm.addEventListener('submit', e => { e.preventDefault(); post('/vendor/orders/manual', new FormData(e.target)); });

            const gmailForm = document.getElementById('gmail-form');
            if(gmailForm) gmailForm.addEventListener('submit', e => { e.preventDefault(); alert('Connected'); });
            
            const announceForm = document.getElementById('announcement-form');
            if(announceForm) announceForm.addEventListener('submit', e => { e.preventDefault(); alert('Posted'); });
        },

        // --- HELPER FUNCTIONS ---

        openModal(id) {
            const modal = document.getElementById(id);
            if(modal) modal.style.display = 'flex';
        },

        resetForm(id) {
            const f = document.getElementById(id);
            if (f) {
                f.reset();
                const h = f.querySelector('input[type=hidden]:not([name=type])');
                if (h) h.value = ''; 
                
                // [FIXED] Safety check added here to prevent "null" error
                if (id === 'product-form') {
                    const title = document.getElementById('product-modal-title');
                    if (title) title.innerText = "Add New Product";
                }
            }
        },

        viewDetails(data) {
            const content = document.getElementById('order-details-content');
            if(content) {
                content.innerHTML = `
                    <h3 style="font-size:1.2rem; font-weight:bold; margin-bottom:10px;">Order Details</h3>
                    <p><strong>Customer:</strong> ${data.customer}</p>
                    <p><strong>Phone:</strong> ${data.phone}</p>
                    <p><strong>Address:</strong> ${data.address}</p>
                    <hr style="margin:10px 0;">
                    <p><strong>Items:</strong> ${data.items}</p>
                    <p><strong>Total:</strong> ₱${data.total}</p>
                    <p><strong>Status:</strong> ${data.status}</p>
                `;
                this.openModal('order-details-modal');
            }
        },

        async updateStatus(id, newStatus) {
            if (!confirm(`Update status to "${newStatus}"?`)) return;
            await fetch(`/vendor/orders/${id}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ status: newStatus })
            });
            window.location.reload();
        },

        async assignDriver(id) {
            const drvInput = document.getElementById(`driver-${id}`);
            if (!drvInput) return;
            const drv = drvInput.value;
            
            if (!drv) return alert('Select driver');
            await fetch(`/vendor/orders/${id}/assign`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ driver_name: drv })
            });
            window.location.reload();
        },

        deleteItem(url) {
            if (url && confirm('Are you sure you want to delete this?')) {
                fetch(url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message || 'Deleted successfully');
                    window.location.reload();
                })
                .catch(err => alert('Deletion failed.'));
            }
        }
    };

    app.init();
});