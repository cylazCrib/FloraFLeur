document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // NAVIGATION
    const navItems = document.querySelectorAll('.nav-item[data-target]');
    const views = document.querySelectorAll('.view');

    navItems.forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            navItems.forEach(n => n.classList.remove('active'));
            views.forEach(v => v.classList.remove('active-view'));
            item.classList.add('active');
            const target = document.getElementById(item.dataset.target);
            if (target) target.classList.add('active-view');
        });
    });

    // ACTIONS
    document.addEventListener('click', e => {
        const t = e.target;

        // Close Modal
        if (t.matches('[data-close-modal]') || t.matches('.modal-overlay') || t.closest('.modal-close-btn')) {
            document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
        }

       // View Activity
        if (t.closest('.view-activity-btn')) {
            const id = t.closest('.view-activity-btn').dataset.id;
            const modal = document.getElementById('activity-modal');
            const list = document.getElementById('activity-list');
            const title = document.getElementById('act-shop-name');
            
            list.innerHTML = '<p class="text-center p-4">Loading...</p>';
            modal.style.display = 'flex';

            fetch(`/admin/owners/${id}/activity`)
                .then(res => {
                    if (!res.ok) {
                        // Throw specific error with status code
                        throw new Error(`Status ${res.status}: ${res.statusText}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if(data.error) {
                        list.innerHTML = `<p class="text-center p-4 text-red-500">${data.message}</p>`;
                        return;
                    }
                    title.innerText = data.shop + ' (' + data.owner + ')';
                    
                    if (!data.activity || data.activity.length === 0) {
                        list.innerHTML = '<p class="text-center p-4 text-gray-500">No recent activity.</p>';
                    } else {
                        list.innerHTML = data.activity.map(a => `
                            <div style="padding:10px; border-bottom:1px solid #eee; display:flex; gap:10px;">
                                <div><strong>${a.type ? a.type.toUpperCase() : 'INFO'}</strong></div>
                                <div>${a.text} <br><small style="color:#888">${a.date}</small></div>
                            </div>
                        `).join('');
                    }
                })
                .catch(err => {
                    console.error("Fetch Failed:", err);
                    list.innerHTML = `<p class="text-center p-4 text-red-500">Failed to load: ${err.message}</p>`;
                });
        }
        // View Details
        if (t.closest('.view-details-btn')) {
            const data = JSON.parse(t.closest('.view-details-btn').dataset.json);
            const modal = document.getElementById('details-modal');
            const content = document.getElementById('details-content');
            
            let permitHtml = 'No Permit';
            if(data.permit_url) {
                let src = data.permit_url.startsWith('http') ? data.permit_url : '/storage/' + data.permit_url;
                permitHtml = `<a href="${src}" target="_blank"><img src="${src}" style="width:100%; max-height:200px; object-fit:contain; border:1px solid #ccc;"></a>`;
            }

            content.innerHTML = `
                <div>
                    <p><strong>Shop:</strong> ${data.name}</p>
                    <p><strong>Owner:</strong> ${data.owner}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                </div>
                <div>
                    <p><strong>Address:</strong> ${data.address}</p>
                    <p><strong>Permit:</strong></p>
                    ${permitHtml}
                </div>
            `;
            modal.querySelector('.approve-from-modal').dataset.id = data.id;
            modal.querySelector('.reject-from-modal').dataset.id = data.id;
            modal.style.display = 'flex';
        }

        // Buttons Logic
        if (t.closest('.approve-btn') || t.classList.contains('approve-from-modal')) {
            const id = t.dataset.id || t.closest('[data-id]').dataset.id;
            performAction(`/admin/registrations/${id}/approve`, 'PATCH');
        }
        if (t.closest('.reject-btn') || t.classList.contains('reject-from-modal')) {
            const id = t.dataset.id || t.closest('[data-id]').dataset.id;
            performAction(`/admin/registrations/${id}/reject`, 'DELETE');
        }
        if (t.closest('.toggle-vendor-btn')) {
            const id = t.closest('.toggle-vendor-btn').dataset.id;
            performAction(`/admin/vendors/${id}/toggle`, 'PATCH');
        }
    });

    function performAction(url, method) {
        if (!confirm('Are you sure?')) return;
        fetch(url, {
            method: method,
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => { alert(data.message); window.location.reload(); })
        .catch(() => alert('Network Error'));
    }
});