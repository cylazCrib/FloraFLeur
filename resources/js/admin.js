document.addEventListener('DOMContentLoaded', function() {
    // --- DOM ELEMENTS ---
    const navItems = document.querySelectorAll('.nav-item[data-target]');
    const views = document.querySelectorAll('.view');
    const modalContainer = document.getElementById('modal-container');
    const toast = document.getElementById('toast');

    // --- TEMPLATES ---
    const detailsModalTemplate = document.getElementById('details-modal-template')?.content;
    const activityModalTemplate = document.getElementById('activity-modal-template')?.content;
    
    // --- NAVIGATION ---
    // This is for the JS-based navigation (which we'll replace with routes)
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
        // Don't prevent default if it's a real link (href starts with '/')
        if (this.dataset.target) {
            e.preventDefault();
            navigateToView(this.dataset.target);
        }
    }));

    // --- UTILITY FUNCTIONS ---
    function showToast(message) {
        if (toast) {
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => { toast.classList.remove('show'); }, 3000);
        }
    }

    function showModal(modalId) {
        if (modalContainer) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
            }
        }
    }

    function hideAllModals() {
        if (modalContainer) {
            modalContainer.querySelectorAll('.modal-overlay').forEach(modal => modal.style.display = 'none');
        }
    }
    
    // --- MODAL & FORM HANDLING ---
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-modal-target]')) {
            showModal(e.target.dataset.modalTarget);
        }
    });

    if (modalContainer) {
        modalContainer.addEventListener('click', function(event) {
            if (event.target.matches('[data-close-modal]') || event.target.closest('[data-close-modal]')) hideAllModals();
            if (event.target.matches('.modal-overlay')) hideAllModals();
        });
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") hideAllModals();
    });

    // --- CLICK EVENT LISTENER FOR MODALS ---
    document.addEventListener('click', e => {
        const action = e.target.dataset.action;
        if (!action) return;

        // *** THIS IS OUR NEW "VIEW DETAILS" FIX ***
        if (action === 'details') {
            if (!detailsModalTemplate) {
                console.error('Details modal template not found');
                return;
            }
            
            const row = e.target.closest('tr');
            if (!row) {
                console.error('Could not find parent table row.');
                return;
            }

            const data = row.dataset;
            const content = detailsModalTemplate.cloneNode(true);
            
            content.querySelector('[data-field="name"]').textContent = data.name;
            content.querySelector('[data-field="owner"]').textContent = data.owner;
            content.querySelector('[data-field="date"]').textContent = data.date;
            content.querySelector('[data-field="location"]').textContent = data.location;
            content.querySelector('[data-field="status"]').textContent = data.status;
            content.querySelector('[data-field="email"]').textContent = data.email;
            content.querySelector('[data-field="phone"]').textContent = data.phone;
            content.querySelector('[data-field="category"]').textContent = data.category;
            content.querySelector('[data-field="products"]').textContent = data.products;
            content.querySelector('[data-field="description"]').textContent = data.description;
            
            const permitButton = content.querySelector('[data-action="viewPermit"]');
            if (permitButton) {
                permitButton.dataset.url = data.permitUrl;
            }

            const modalContentEl = document.getElementById('details-modal-content');
            modalContentEl.innerHTML = ''; // Clear previous
            modalContentEl.appendChild(content);
            
            showModal('details-modal');
        }
        // *** END OF "VIEW DETAILS" FIX ***

        if (action === 'viewPermit') {
            const permitImage = document.getElementById('permit-image');
            if (permitImage) {
                permitImage.src = e.target.dataset.url;
                showModal('permit-modal');
            }
        }
    });

    // --- LOGOUT (WITH NULL CHECK) ---
 // --- LOGOUT (WITH NULL CHECK) ---
const handleLogout = () => {
    const confirmTitle = document.getElementById('confirm-title');
    const confirmText = document.getElementById('confirm-text');
    const confirmBtn = document.getElementById('confirm-btn');

    if (confirmTitle) confirmTitle.textContent = `Log Out`;
    if (confirmText) confirmText.textContent = `Are you sure you want to log out?`;

    if (confirmBtn) {
        confirmBtn.className = 'action-button red';
        confirmBtn.textContent = 'Log Out';

        // THIS IS THE NEW LOGIC
        confirmBtn.onclick = () => {
            hideAllModals();
            showToast(`Logging out...`);

            // Get CSRF token from the meta tag we just added
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

        showModal('confirm-modal');
    }
};

// This part at the bottom finds the button and attaches the function
const logoutButton = document.getElementById('logout-btn');
if (logoutButton) {
    logoutButton.addEventListener('click', handleLogout);
}
    // --- FORM SUBMISSIONS (WITH NULL CHECKS) ---
    
    // This was the cause of your new error at line 376
    const ownerForm = document.getElementById('owner-notification-form');
    if (ownerForm) {
        ownerForm.addEventListener('submit', e => { 
            e.preventDefault(); 
            showToast("Notification sent to all owners."); 
            e.target.reset(); 
        });
    }
    
    const gmailForm = document.getElementById('gmail-form');
    if (gmailForm) {
        gmailForm.addEventListener('submit', e => { 
            e.preventDefault(); 
            showToast("Gmail account connected."); 
            e.target.reset(); 
        });
    }
    
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', e => { 
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
    }

    // --- CHART.JS (ALREADY HAS NULL CHECK) ---
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

    // We no longer call renderAll() because Laravel handles rendering.
});