document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const el = document.getElementById('db-data');
    
    const getJSON = (id) => {
        const e = document.getElementById(id);
        return e ? JSON.parse(e.dataset.json || '[]') : [];
    };

    const app = {
        state: {
            orders: getJSON('db-data') ? JSON.parse(el.dataset.orders || '[]') : [],
            products: getJSON('db-data') ? JSON.parse(el.dataset.products || '[]') : [],
            inventory: getJSON('db-data') ? JSON.parse(el.dataset.inventory || '[]') : [],
            staff: getJSON('db-data') ? JSON.parse(el.dataset.staff || '[]') : [],
            drivers: getJSON('db-data') ? JSON.parse(el.dataset.drivers || '[]') : []
        },

        init() {
            this.renderOrders();
            this.renderDelivery();
            this.renderProducts();
            this.renderInventory();
            this.renderStaff();
            this.listeners();
        },

        // --- RENDERERS ---
        renderOrders() {
            const tbody = document.getElementById('orders-table-body');
            if(!tbody) return;
            if(app.state.orders.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="text-center p-4">No orders.</td></tr>'; return; }
            tbody.innerHTML = app.state.orders.map(o => `
                <tr>
                    <td>#${o.id}</td>
                    <td>${o.customer}<br><small style="color:#888">${o.phone}</small></td>
                    <td>${o.items}</td>
                    <td>₱${parseFloat(o.total).toLocaleString()}</td>
                    <td><span class="status status-${o.status.toLowerCase().replace(' ','-')}">${o.status}</span></td>
                    <td>
                        <button class="table-action-btn view-order-btn" data-id="${o.db_id}">View</button>
                        <button class="table-action-btn update-status-btn" data-id="${o.db_id}" data-current="${o.status}">Update</button>
                    </td>
                </tr>`).join('');
        },
        
        renderDelivery() {
            const tbody = document.getElementById('delivery-table-body');
            if(!tbody) return;
            const active = app.state.orders.filter(o => o.status !== 'Delivered' && o.status !== 'Canceled');
            if(active.length === 0) { tbody.innerHTML = '<tr><td colspan="4" class="text-center p-4">No deliveries.</td></tr>'; return; }
            tbody.innerHTML = active.map(o => {
                let opts = '<option value="">Select</option>';
                app.state.drivers.forEach(d => { opts += `<option value="${d.name}" ${o.driver===d.name?'selected':''}>${d.name}</option>`; });
                const mapLink = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(o.address)}`;
                return `<tr><td>#${o.id}</td><td>${o.address}</td><td><select id="driver-${o.db_id}" class="border p-1">${opts}</select></td><td><button class="table-action-btn assign-driver" data-id="${o.db_id}">Assign</button> <a href="${mapLink}" target="_blank" class="table-action-btn view-link" style="text-decoration:none;border:1px solid #ddd;padding:2px 5px;color:#555;">Map</a></td></tr>`;
            }).join('');
        },

        renderProducts() {
            const tbody = document.getElementById('products-table-body');
            if(!tbody) return;
            tbody.innerHTML = app.state.products.map(p => `<tr><td><img src="${p.image}" width="40"></td><td>${p.name}</td><td>${p.description.substring(0,30)}...</td><td>₱${p.price}</td><td><button class="table-action-btn edit-product" data-id="${p.id}" data-name="${p.name}" data-desc="${p.description}" data-price="${p.price}" data-cat="${p.category}">Edit</button> <button style="color:red" class="table-action-btn del-product" data-id="${p.id}">X</button></td></tr>`).join('');
        },

        renderInventory() {
            const items = app.state.inventory.filter(i => i.type === 'item');
            const flowers = app.state.inventory.filter(i => i.type === 'flower');
            
            const renderCard = (i) => `<div class="inventory-item" style="display:flex; justify-content:space-between; align-items:center; padding:1rem; background:#FCEFF0; border-radius:10px; margin-bottom:0.5rem; border:1px solid #eee;"><div><div style="font-weight:600;">${i.code?i.code+' - ':''}${i.name}</div><div style="font-size:0.85rem; color:#666;">${i.quantity} pcs remaining ${i.quantity<=5?'<span style="color:red;font-weight:bold;">Low Stock!</span>':''}</div></div><div><button class="table-action-btn update-item-btn" data-id="${i.id}" data-name="${i.name}" data-code="${i.code}" data-quantity="${i.quantity}" data-type="${i.type}">Edit</button> <button class="table-action-btn del-inv" data-id="${i.id}" style="color:red;">X</button></div></div>`;

            const iBody = document.getElementById('items-inventory-list');
            const fBody = document.getElementById('flowers-inventory-list');
            if(iBody) iBody.innerHTML = items.length ? items.map(renderCard).join('') : '<p class="text-center p-4 text-gray-500">No items.</p>';
            if(fBody) fBody.innerHTML = flowers.length ? flowers.map(renderCard).join('') : '<p class="text-center p-4 text-gray-500">No flowers.</p>';
        },

        renderStaff() {
            const tbody = document.getElementById('staff-table-body');
            if(!tbody) return;
            tbody.innerHTML = app.state.staff.map(s => `<tr><td>${s.name}</td><td>${s.email}</td><td>${s.role}</td><td><span class="status status-active">Active</span></td><td><button class="table-action-btn edit-staff" data-id="${s.id}" data-name="${s.name}" data-email="${s.email}" data-phone="${s.phone}" data-role="${s.role}">Edit</button> <button class="table-action-btn del-staff" style="color:red" data-id="${s.id}">X</button></td></tr>`).join('');
        },

        // --- DETAILS ---
        viewDetails(id) {
            const o = app.state.orders.find(order => order.db_id == id);
            if(!o) return;
            document.getElementById('order-details-content').innerHTML = `<h3>Order #${o.id}</h3><p><strong>Customer:</strong> ${o.customer}</p><p><strong>Phone:</strong> ${o.phone}</p><p><strong>Address:</strong> ${o.address}</p><hr style="margin:10px 0;"><p><strong>Items:</strong> ${o.items}</p><p><strong>Total:</strong> ₱${parseFloat(o.total).toLocaleString()}</p><p><strong>Status:</strong> ${o.status}</p>`;
            document.getElementById('order-details-modal').style.display = 'flex';
        },

        // --- LISTENERS ---
        listeners() {
            document.addEventListener('click', e => {
                const t = e.target;
                
                // Nav
                if(t.closest('.nav-item')) {
                    const link = t.closest('.nav-item');
                    if(link.id === 'logout-btn') { e.preventDefault(); document.getElementById('logout-form')?.submit(); return; }
                    if(link.dataset.target) {
                        e.preventDefault();
                        document.querySelectorAll('.view').forEach(v => v.classList.remove('active-view'));
                        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                        document.getElementById(link.dataset.target).classList.add('active-view');
                        link.classList.add('active');
                    }
                }

                // Modals
                if(t.id === 'new-order-btn') this.openModal('order-form-modal');
                if(t.id === 'new-product-btn') { this.resetForm('product-form'); this.openModal('product-form-modal'); }
                if(t.id === 'new-item-btn') { this.resetForm('inventory-form'); document.getElementById('inv-type').value='item'; document.getElementById('inv-modal-title').innerText='Add Item'; this.openModal('inventory-form-modal'); }
                if(t.id === 'new-flower-btn') { this.resetForm('inventory-form'); document.getElementById('inv-type').value='flower'; document.getElementById('inv-modal-title').innerText='Add Flower'; this.openModal('inventory-form-modal'); }
                if(t.id === 'new-staff-btn') { this.resetForm('staff-form'); this.openModal('staff-form-modal'); }

                if(t.matches('[data-close-modal]')) {
                    document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
                }

                // Edit
                if(t.classList.contains('edit-product')) {
                    const d = t.dataset;
                    document.getElementById('prod_id').value = d.id;
                    document.getElementById('prod_name').value = d.name;
                    document.getElementById('prod_desc').value = d.desc;
                    document.getElementById('prod_price').value = d.price;
                    document.getElementById('prod_cat').value = d.cat;
                    this.openModal('product-form-modal');
                }
                if(t.classList.contains('update-item-btn')) {
                    const d = t.dataset;
                    document.getElementById('item_id').value = d.id;
                    document.getElementById('inv_name').value = d.name;
                    document.getElementById('inv_code').value = d.code;
                    document.getElementById('inv_qty').value = d.quantity;
                    document.getElementById('inv-type').value = d.type;
                    document.getElementById('inv-modal-title').innerText = 'Edit ' + d.type;
                    this.openModal('inventory-form-modal');
                }
                if(t.classList.contains('edit-staff')) {
                    const d = t.dataset;
                    document.getElementById('staff_id').value = d.id;
                    document.getElementById('s_name').value = d.name;
                    document.getElementById('s_email').value = d.email;
                    document.getElementById('s_phone').value = d.phone;
                    document.getElementById('s_role').value = d.role;
                    this.openModal('staff-form-modal');
                }

                // Delete
                if(t.classList.contains('del-inv')) this.deleteItem('/vendor/inventory/'+t.dataset.id);
                if(t.classList.contains('del-staff')) this.deleteItem('/vendor/staff/'+t.dataset.id);
                if(t.classList.contains('del-product')) this.deleteItem('/vendor/products/'+t.dataset.id);
                
                // Status/Assign
                 if(t.classList.contains('update-status-btn')) this.updateStatus(t.dataset.id, t.dataset.current);
                 if(t.classList.contains('assign-driver')) this.assignDriver(t.dataset.id);
                 if(t.classList.contains('view-order-btn')) this.viewDetails(t.dataset.id);
            });

            // Forms
            const post = (url, body) => fetch(url, { method: 'POST', headers: {'X-CSRF-TOKEN': csrfToken}, body: body }).then(res => { if(res.ok) window.location.reload(); else alert('Error'); });
            
            document.getElementById('inventory-form').addEventListener('submit', e => { e.preventDefault(); post('/vendor/inventory', new FormData(e.target)); });
            document.getElementById('staff-form').addEventListener('submit', e => { e.preventDefault(); post('/vendor/staff', new FormData(e.target)); });
            document.getElementById('product-form').addEventListener('submit', e => { e.preventDefault(); post('/vendor/products', new FormData(e.target)); });
            document.getElementById('order-form').addEventListener('submit', e => { e.preventDefault(); post('/vendor/orders/manual', new FormData(e.target)); });
        },

        openModal(id) { document.getElementById(id).style.display = 'flex'; },
        
        resetForm(id) { 
            document.getElementById(id).reset(); 
            const h = document.getElementById(id).querySelector('input[type=hidden]:not([name=type])');
            if(h) h.value = '';
        },
        
        async updateStatus(id, current) {
             const next = current === 'Pending' ? 'Approved' : 'Delivered';
             if(!confirm(`Update to ${next}?`)) return;
             await fetch(`/vendor/orders/${id}/status`, { method: 'PATCH', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken}, body: JSON.stringify({status: next}) });
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