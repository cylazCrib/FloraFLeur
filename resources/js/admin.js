document.addEventListener('DOMContentLoaded', function() {
    // --- MOCK DATA ---
    let registrations = [
        { 
            id: 1, 
            name: 'Petal Paradise', 
            owner: 'Maria Lopez', 
            date: 'Oct 14, 2025', 
            status: 'Pending', 
            permitUrl: 'https://placehold.co/800x1100/eee/333?text=Business+Permit',
            email: 'maria.lopez@example.com',
            phone: '0915-457-8943',
            address: '123 Flower Street, Surigao City',
            category: 'Florist',
            description: 'Specializing in custom floral arrangements for all occasions.'
        },
        { 
            id: 2, 
            name: 'Bloom & Blossom', 
            owner: 'John Rivera', 
            date: 'Oct 10, 2025', 
            status: 'Pending', 
            permitUrl: 'https://placehold.co/800x1100/eee/333?text=Business+Permit',
            email: 'john.rivera@example.com',
            phone: '0915-876-0437',
            address: '456 Garden Avenue, Butuan City',
            category: 'Florist',
            description: 'Premium flower shop with imported flowers and unique arrangements.'
        }
    ];
    
    let shops = [
        { 
            id: 3, 
            name: 'The Green Stem', 
            owner: 'Elia Santos', 
            location: 'Butuan City', 
            status: 'Approved', 
            products: 350, 
            permitUrl: 'https://placehold.co/800x1100/eee/333?text=Business+Permit',
            email: 'elia.santos@example.com',
            phone: '0915-876-0437',
            address: '789 Plant Boulevard, Butuan City',
            category: 'Florist',
            description: 'Eco-friendly florist specializing in sustainable floral arrangements.',
            registrationDate: 'Aug 15, 2025'
        },
        { 
            id: 4, 
            name: 'The Flower Patch', 
            owner: 'Theodore Sanchez', 
            location: 'Cagayan de Oro', 
            status: 'Suspended', 
            products: 404, 
            permitUrl: 'https://placehold.co/800x1100/eee/333?text=Business+Permit',
            email: 'theodore.sanchez@example.com',
            phone: '0945-123-5847',
            address: '321 Blossom Road, Cagayan de Oro',
            category: 'Florist',
            description: 'Family-owned flower shop with over 20 years of experience.',
            registrationDate: 'Jul 22, 2025'
        },
        { 
            id: 5, 
            name: 'Floral Elegance', 
            owner: 'Catherine Reyes', 
            location: 'Davao City', 
            status: 'Approved', 
            products: 450, 
            permitUrl: 'https://placehold.co/800x1100/eee/333?text=Business+Permit',
            email: 'catherine.reyes@example.com',
            phone: '0917-654-3210',
            address: '654 Petal Lane, Davao City',
            category: 'Florist',
            description: 'Luxury floral arrangements for weddings and special events.',
            registrationDate: 'Sep 5, 2025'
        }
    ];

    // --- DOM ELEMENTS ---
    const navItems = document.querySelectorAll('.nav-item[data-target]');
    const views = document.querySelectorAll('.view');
    const modalContainer = document.getElementById('modal-container');
    const toast = document.getElementById('toast');

    // --- TEMPLATES ---
    const registrationTemplate = document.getElementById('registration-row-template')?.content;
    const shopTemplate = document.getElementById('shop-row-template')?.content;
    const ownerTemplate = document.getElementById('owner-row-template')?.content;
    const detailsModalTemplate = document.getElementById('details-modal-template')?.content;
    const activityModalTemplate = document.getElementById('activity-modal-template')?.content;
    
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

    // --- UTILITY FUNCTIONS ---
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }

    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function hideAllModals() {
        modalContainer.querySelectorAll('.modal-overlay').forEach(modal => modal.style.display = 'none');
    }
    
    // --- MODAL & FORM HANDLING ---
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-modal-target]')) {
            showModal(e.target.dataset.modalTarget);
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
    function renderDashboardCards() {
        // Update total shops
        const totalShopsCard = document.getElementById('total-shops-card');
        if (totalShopsCard) {
            totalShopsCard.querySelector('.card-main-value').textContent = shops.filter(s => s.status === 'Approved').length;
        }
        
        // Update new entries
        const newEntriesCard = document.getElementById('new-entries-card');
        if (newEntriesCard) {
            newEntriesCard.querySelector('.card-main-value').textContent = registrations.length;
        }

        // Update total products
        const totalProductsCard = document.getElementById('total-products-card');
        if (totalProductsCard) {
            const totalProducts = shops.reduce((sum, shop) => sum + (shop.status === 'Approved' ? shop.products : 0), 0);
            totalProductsCard.querySelector('.card-main-value').textContent = totalProducts.toLocaleString();
        }
    }

    function renderRegistrations() {
        const tbody = document.getElementById('registrations-table-body');
        tbody.innerHTML = ''; // Clear existing
        
        if (!registrationTemplate) {
            console.error('Registration template not found');
            return;
        }

        if (registrations.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No new registrations.</td></tr>';
            return;
        }

        registrations.forEach((reg, index) => {
            const row = registrationTemplate.cloneNode(true);
            
            // Find elements by data-field and populate
            row.querySelector('[data-field="name"]').textContent = reg.name;
            row.querySelector('[data-field="owner"]').textContent = reg.owner;
            row.querySelector('[data-field="date"]').textContent = reg.date;
            
            const statusSpan = row.querySelector('[data-field="status"]');
            statusSpan.textContent = 'Pending';
            statusSpan.className = 'status status-pending';

            // Set dataset attributes for buttons
            row.querySelector('[data-action="details"]').dataset.index = index;
            row.querySelector('[data-action="approve"]').dataset.index = index;
            row.querySelector('[data-action="reject"]').dataset.index = index;
            
            tbody.appendChild(row);
        });
    }

    function renderShops() {
        const tbody = document.getElementById('shops-table-body');
        tbody.innerHTML = '';
        
        if (!shopTemplate) {
            console.error('Shop template not found');
            return;
        }

        shops.forEach((shop, index) => {
            const row = shopTemplate.cloneNode(true);
            
            const statusClass = shop.status === 'Approved' ? 'status-approved' : 'status-suspended';
            const toggleAction = shop.status === 'Approved' ? 'Suspend' : 'Activate';
            const toggleColor = shop.status === 'Approved' ? 'yellow' : 'green';

            row.querySelector('[data-field="name"]').textContent = shop.name;
            row.querySelector('[data-field="owner"]').textContent = shop.owner;
            row.querySelector('[data-field="location"]').textContent = shop.location;

            const statusSpan = row.querySelector('[data-field="status"]');
            statusSpan.textContent = shop.status;
            statusSpan.className = `status ${statusClass}`;
            
            const toggleButton = row.querySelector('[data-action="toggleStatus"]');
            toggleButton.textContent = toggleAction;
            toggleButton.className = `action-button ${toggleColor}`;
            toggleButton.dataset.index = index;

            row.querySelector('[data-action="details"]').dataset.index = index;

            tbody.appendChild(row);
        });
    }
    
    function renderOwners() {
        const tbody = document.getElementById('owners-table-body');
        tbody.innerHTML = '';
        
        if (!ownerTemplate) {
            console.error('Owner template not found');
            return;
        }

         shops.forEach((shop, index) => {
            const row = ownerTemplate.cloneNode(true);
            
            row.querySelector('[data-field="owner"]').textContent = shop.owner;
            row.querySelector('[data-field="shopName"]').textContent = shop.name;
            row.querySelector('[data-action="viewActivity"]').dataset.index = index;
            
            tbody.appendChild(row);
         });
    }

    function renderAll() {
        renderDashboardCards();
        renderRegistrations();
        renderShops();
        renderOwners();
    }

    // --- EVENT HANDLING ---
    document.addEventListener('click', e => {
        const action = e.target.dataset.action;
        const index = e.target.dataset.index;
        if (!action) return;

        if (action === 'approve') {
            const reg = registrations.splice(index, 1)[0];
            shops.push({ 
                ...reg, 
                id: shops.length + 1, 
                location: reg.address.split(', ')[1] || 'Unknown', 
                products: Math.floor(Math.random() * 500) + 100, 
                status: 'Approved',
                registrationDate: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            });
            renderAll();
            showToast(`${reg.name} has been approved.`);
        }
        if (action === 'reject') {
            const reg = registrations[index];
            document.getElementById('confirm-title').textContent = `Reject ${reg.name}?`;
            document.getElementById('confirm-text').textContent = `Are you sure you want to reject this registration?`;
            const confirmBtn = document.getElementById('confirm-btn');
            confirmBtn.className = 'action-button red';
            confirmBtn.textContent = 'Reject';
            showModal('confirm-modal');
            confirmBtn.onclick = () => {
                registrations.splice(index, 1);
                renderAll();
                hideAllModals();
                showToast(`${reg.name} has been rejected.`);
            };
        }
        if (action === 'details') {
            if (!detailsModalTemplate) {
                console.error('Details modal template not found');
                return;
            }
            const type = e.target.dataset.type;
            const item = type === 'shop' ? shops[index] : registrations[index];
            const content = detailsModalTemplate.cloneNode(true);
            
            // Populate cloned template
            content.querySelector('[data-field="name"]').textContent = item.name;
            content.querySelector('[data-field="owner"]').textContent = item.owner;
            content.querySelector('[data-field="date"]').textContent = item.date || item.registrationDate || 'N/A';
            content.querySelector('[data-field="location"]').textContent = item.location || item.address || 'N/A';
            content.querySelector('[data-field="status"]').textContent = item.status;
            content.querySelector('[data-field="email"]').textContent = item.email || 'N/A';
            content.querySelector('[data-field="phone"]').textContent = item.phone || 'N/A';
            content.querySelector('[data-field="category"]').textContent = item.category || 'N/A';
            content.querySelector('[data-field="products"]').textContent = item.products || 'N/A';
            content.querySelector('[data-field="description"]').textContent = item.description || 'No description provided.';
            content.querySelector('[data-action="viewPermit"]').dataset.url = item.permitUrl;

            // Inject into modal
            const modalContentEl = document.getElementById('details-modal-content');
            modalContentEl.innerHTML = ''; // Clear previous
            modalContentEl.appendChild(content);
            
            showModal('details-modal');
        }
         if (action === 'viewActivity') {
            if (!activityModalTemplate) {
                console.error('Activity modal template not found');
                return;
            }
            const shop = shops[index];
            const content = activityModalTemplate.cloneNode(true);
            
            // Populate cloned template
            content.querySelector('[data-field="name"]').textContent = `Activity for ${shop.name}`;
            content.querySelector('[data-field="products"]').textContent = shop.products;
            content.querySelector('[data-field="status"]').textContent = shop.status;
            content.querySelector('[data-field="date"]').textContent = shop.registrationDate;
            content.querySelector('[data-field="location"]').textContent = shop.location;
            
            // Note: Recent Orders and Monthly Sales are static in the template, so no need to populate them.
            
            // Inject into modal
            const modalContentEl = document.getElementById('activity-modal-content');
            modalContentEl.innerHTML = ''; // Clear previous
            modalContentEl.appendChild(content);

            showModal('activity-modal');
        }
        if (action === 'viewPermit') {
            document.getElementById('permit-image').src = e.target.dataset.url;
            showModal('permit-modal');
        }
        if (action === 'toggleStatus') {
            const shop = shops[index];
            shop.status = shop.status === 'Approved' ? 'Suspended' : 'Approved';
            renderAll();
            showToast(`${shop.name} has been ${shop.status.toLowerCase()}.`);
        }
    });

    // Logout
    const handleLogout = () => {
        document.getElementById('confirm-title').textContent = `Log Out`;
        document.getElementById('confirm-text').textContent = `Are you sure you want to log out?`;
        const confirmBtn = document.getElementById('confirm-btn');
        confirmBtn.className = 'action-button red';
        confirmBtn.textContent = 'Log Out';
        showModal('confirm-modal');
        confirmBtn.onclick = () => {
            hideAllModals();
            showToast(`Logging out...`);
            // Add redirect to landing page
            setTimeout(() => {
                window.location.href = '../public/landingpage.html';
            }, 1000);
        };
    };
    document.getElementById('logout-btn').addEventListener('click', handleLogout);

    // Form Submissions
    document.getElementById('owner-notification-form').addEventListener('submit', e => { 
        e.preventDefault(); 
        showToast("Notification sent to all owners."); 
        e.target.reset(); 
    });
    
    document.getElementById('gmail-form').addEventListener('submit', e => { 
        e.preventDefault(); 
        showToast("Gmail account connected."); 
        e.target.reset(); 
    });
    
    document.getElementById('password-form').addEventListener('submit', e => { 
        e.preventDefault(); 
        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        if (newPassword !== confirmPassword) {
            showToast("New passwords do not match.");
            return;
        }
        
        showToast("Password changed successfully."); 
        e.target.reset(); 
    });

    // Chart.js
    const ctxEl = document.getElementById('registrationsChart');
    if (ctxEl) {
        const ctx = ctxEl.getContext('2d');
        new Chart(ctx, { 
            type: 'line', 
            data: { 
                labels: ['May', 'June', 'July', 'August', 'September', 'October'], 
                datasets: [{ 
                    label: 'New Shop Registrations', 
                    data: [1, 3, 2, 5, 4, 1], 
                    backgroundColor: 'rgba(163, 141, 140, 0.1)', 
                    borderColor: 'rgba(163, 141, 140, 0.8)', 
                    borderWidth: 2, 
                    tension: 0.4, 
                    fill: true 
                }] 
            }, 
            options: { 
                scales: { 
                    y: { beginAtZero: true, ticks: { color: 'var(--text-secondary)' }, grid: { color: 'var(--border-color)'} }, 
                    x: { ticks: { color: 'var(--text-secondary)' }, grid: { color: 'var(--border-color)'} }
                }, 
                plugins: { legend: { labels: { color: 'var(--text-secondary)' } } } 
            } 
        });
    }

    // Initial Render
    renderAll();
});