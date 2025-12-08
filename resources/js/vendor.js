document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const app = {
        init() {
            this.listeners();
            // Ensure Dashboard is active by default if no other view is active
            if (!document.querySelector('.view.active-view')) {
                document.getElementById('dashboard-view').classList.add('active-view');
            }
        },

        listeners() {
            document.addEventListener('click', e => {
                const t = e.target;
                
                // 1. NAVIGATION
                if(t.closest('.nav-item') && t.closest('.nav-item').id !== 'logout-btn') {
                    e.preventDefault();
                    const target = t.closest('.nav-item').dataset.target;
                    if(target) {
                        document.querySelectorAll('.view').forEach(v => v.classList.remove('active-view'));
                        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                        document.getElementById(target).classList.add('active-view');
                        t.closest('.nav-item').classList.add('active');
                    }
                }

                // 2. MODAL OPENERS (Add)
                if(t.id === 'new-order-btn') this.openModal('order-form-modal');
                if(t.id === 'new-product-btn') { this.resetForm('product-form'); this.openModal('product-form-modal'); }
                if(t.id === 'new-staff-btn') { this.resetForm('staff-form'); this.openModal('staff-form-modal'); }
                
                if(t.id === 'new-item-btn') { 
                    this.resetForm('inventory-form'); 
                    document.getElementById('inv-type').value = 'item'; 
                    document.getElementById('inv-modal-title').innerText = 'Add Item';
                    this.openModal('inventory-form-modal'); 
                }
                if(t.id === 'new-flower-btn') { 
                    this.resetForm('inventory-form'); 
                    document.getElementById('inv-type').value = 'flower'; 
                    document.getElementById('inv-modal-title').innerText = 'Add Flower';
                    this.openModal('inventory-form-modal'); 
                }

                // 3. EDIT ACTIONS (Populate Forms from Data Attributes)
                if(t.closest('.edit-product-btn')) {
                    const d = t.closest('.edit-product-btn').dataset;
                    document.getElementById('prod_id').value = d.id;
                    document.getElementById('prod_name').value = d.name;
                    document.getElementById('prod_desc').value = d.desc;
                    document.getElementById('prod_price').value = d.price;
                    document.getElementById('prod_cat').value = d.cat;
                    if(document.getElementById('prod_occ')) document.getElementById('prod_occ').value = d.occ;
                    this.openModal('product-form-modal');
                }
                if(t.closest('.update-item-btn')) {
                    const d = t.closest('.update-item-btn').dataset;
                    document.getElementById('item_id').value = d.id;
                    document.getElementById('inv_name').value = d.name;
                    document.getElementById('inv_code').value = d.code;
                    document.getElementById('inv_qty').value = d.quantity;
                    document.getElementById('inv-type').value = d.type;
                    document.getElementById('inv-modal-title').innerText = 'Edit ' + d.type;
                    this.openModal('inventory-form-modal');
                }
                if(t.closest('.edit-staff-btn')) {
                    const d = t.closest('.edit-staff-btn').dataset;
                    document.getElementById('staff_id').value = d.id;
                    document.getElementById('s_name').value = d.name;
                    document.getElementById('s_email').value = d.email;
                    document.getElementById('s_phone').value = d.phone;
                    document.getElementById('s_role').value = d.role;
                    this.openModal('staff-form-modal');
                }

                // 4. CLOSING MODALS
                if(t.matches('[data-close-modal]') || t.closest('.modal-close-btn')) {
                    document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
                }

                // 5. DELETE ACTIONS
                if(t.closest('.del-inv')) this.deleteItem('/vendor/inventory/'+t.closest('.del-inv').dataset.id);
                if(t.closest('.del-staff')) this.deleteItem('/vendor/staff/'+t.closest('.del-staff').dataset.id);
                if(t.closest('.del-product')) this.deleteItem('/vendor/products/'+t.closest('.del-product').dataset.id);

                // 6. ORDER ACTIONS
                if(t.closest('.view-order-btn')) this.viewDetails(t.closest('.view-order-btn').dataset);
                
                if(t.closest('.update-status-btn')) {
                    const btn = t.closest('.update-status-btn');
                    const select = document.getElementById(`status-${btn.dataset.id}`);
                    this.updateStatus(btn.dataset.id, select.value);
                }

                if(t.closest('.assign-driver-btn')) {
                    const btn = t.closest('.assign-driver-btn');
                    this.assignDriver(btn.dataset.id);
                }
            });

            // 7. FORM SUBMISSIONS
            const post = (url, body) => {
                fetch(url, { method: 'POST', headers: {'X-CSRF-TOKEN': csrfToken}, body: body })
                .then(res => res.json())
                .then(data => { alert(data.message); window.location.reload(); })
                .catch(() => alert('Network Error'));
            };

            document.getElementById('inventory-form').addEventListener('submit', e => { e.preventDefault(); post('/vendor/inventory', new FormData(e.target)); });
            document.getElementById('staff-form').addEventListener('submit', e => { e.preventDefault(); post('/vendor/staff', new FormData(e.target)); });
            document.getElementById('product-form').addEventListener('submit', e => { e.preventDefault(); post('/vendor/products', new FormData(e.target)); });
            document.getElementById('order-form').addEventListener('submit', e => { e.preventDefault(); post('/vendor/orders/manual', new FormData(e.target)); });
            document.getElementById('gmail-form').addEventListener('submit', e => { e.preventDefault(); alert('Connected'); });
            document.getElementById('announcement-form').addEventListener('submit', e => { e.preventDefault(); alert('Posted'); });
        },

        openModal(id) { document.getElementById(id).style.display = 'flex'; },
        
        resetForm(id) { 
            const f = document.getElementById(id);
            if(f) {
                f.reset(); 
                const h = f.querySelector('input[type=hidden]:not([name=type])');
                if(h) h.value = '';
            }
        },

        viewDetails(data) {
            const content = document.getElementById('order-details-content');
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
        },

        async updateStatus(id, newStatus) {
             if(!confirm(`Update status to "${newStatus}"?`)) return;
             await fetch(`/vendor/orders/${id}/status`, { method: 'PATCH', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken}, body: JSON.stringify({status: newStatus}) });
             window.location.reload();
        },
        async assignDriver(id) {
             const drv = document.getElementById(`driver-${id}`).value;
             if(!drv) return alert('Select driver');
             await fetch(`/vendor/orders/${id}/assign`, { method: 'PATCH', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken}, body: JSON.stringify({driver_name: drv}) });
             window.location.reload();
        },
        deleteItem(url) {
            if(confirm('Delete?')) fetch(url, { method: 'DELETE', headers: {'X-CSRF-TOKEN': csrfToken} }).then(() => window.location.reload());
        }
    };
    app.init();
});