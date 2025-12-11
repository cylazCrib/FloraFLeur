@extends('layouts.admin')

@section('content')

<div class="admin-container">
    
    <header class="admin-header">
        <h1>Owners & Notifications</h1>
        <p class="subtitle">Manage shop owners and monitor their recent activities.</p>
    </header>

    @if($notifications->count() > 0)
    <section class="content-section" style="margin-bottom: 2rem; background: #fff0f0; border: 1px solid #ffcccc;">
        <div class="section-header" style="border-bottom: 1px solid #ffcccc;">
            <h2 style="color: #d32f2f;"><i class="fa-solid fa-bell"></i> Pending Approvals</h2>
        </div>
        <div class="notification-list">
            @foreach($notifications as $shop)
            <div class="notif-item" style="display:flex; justify-content:space-between; align-items:center; padding:10px; border-bottom:1px solid #eee; background:white; margin-bottom:5px; border-radius:5px;">
                <div>
                    <strong>{{ $shop->name }}</strong> requested to join.
                    <br><small class="text-gray-500">Owner: {{ $shop->user->name }} • {{ $shop->created_at->diffForHumans() }}</small>
                </div>
                <a href="{{ route('admin.registrations.index') }}" class="action-btn small">Review</a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <section class="content-section">
        <div class="section-header">
            <h2>Active Shop Owners</h2>
            <div class="search-box">
                <input type="text" id="owner-search" placeholder="Search owner...">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Shop Logo</th>
                        <th>Shop Name</th>
                        <th>Owner</th>
                        <th>Contact</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="owners-table-body">
                    @forelse($vendors as $shop)
                    <tr>
                        <td>
                            <img src="{{ Storage::url($shop->image) }}" class="table-img" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
                        </td>
                        <td>{{ $shop->name }}</td>
                        <td>{{ $shop->user->name }}</td>
                        <td>{{ $shop->user->email }}</td>
                        <td>{{ $shop->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="action-btn view-activity-btn" data-id="{{ $shop->id }}">
                                <i class="fa-solid fa-chart-line"></i> View Activity
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No active vendors found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>

<div id="activity-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <button class="modal-close-btn" onclick="closeModal()">×</button>
        
        <div class="modal-header" style="border-bottom:1px solid #eee; padding-bottom:1rem; margin-bottom:1rem;">
            <h2 id="modal-shop-name">Shop Name</h2>
            <p id="modal-owner-name" style="color:#666; font-size:0.9rem;">Owner Name</p>
        </div>

        <div id="activity-timeline" class="activity-feed" style="max-height: 400px; overflow-y: auto;">
            <p class="text-center">Loading...</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // Search Logic
        document.getElementById('owner-search').addEventListener('keyup', function() {
            let value = this.value.toLowerCase();
            let rows = document.querySelectorAll('#owners-table-body tr');
            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });

        // Activity Button Logic
        document.querySelectorAll('.view-activity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const shopId = this.dataset.id;
                openActivityModal(shopId);
            });
        });
    });

    function closeModal() {
        document.getElementById('activity-modal').style.display = 'none';
    }

    function openActivityModal(shopId) {
        const modal = document.getElementById('activity-modal');
        const container = document.getElementById('activity-timeline');
        const title = document.getElementById('modal-shop-name');
        const sub = document.getElementById('modal-owner-name');

        modal.style.display = 'flex';
        container.innerHTML = '<p style="text-align:center; padding:20px;">Loading activity...</p>';

        // Fetch Real Data
        fetch(`/admin/owners/${shopId}/activity`)
            .then(res => res.json())
            .then(data => {
                title.innerText = data.shop_name;
                sub.innerText = "Owner: " + data.owner_name;
                
                if (data.activity.length === 0) {
                    container.innerHTML = '<p style="text-align:center; padding:20px; color:#999;">No recent activity found.</p>';
                    return;
                }

                // Render Timeline
                container.innerHTML = data.activity.map(item => {
                    let icon = item.type === 'order' 
                        ? '<i class="fa-solid fa-cart-shopping" style="color: #4CAF50;"></i>' 
                        : '<i class="fa-solid fa-box" style="color: #2196F3;"></i>';
                    
                    return `
                        <div style="display:flex; gap:15px; margin-bottom:15px; border-left:2px solid #eee; padding-left:15px;">
                            <div style="background:#f9f9f9; width:35px; height:35px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                ${icon}
                            </div>
                            <div>
                                <div style="font-weight:600; color:#333;">${item.text}</div>
                                <div style="font-size:0.8rem; color:#888;">
                                    ${item.date} • <span style="font-weight:bold;">${item.status}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<p style="text-align:center; color:red;">Failed to load data.</p>';
            });
    }
</script>

<style>
    /* Minimal CSS for this page */
    .admin-container { padding: 2rem; max-width: 1200px; margin: 0 auto; color: #4A4A3A; }
    .admin-header { margin-bottom: 2rem; }
    .admin-header h1 { font-size: 2rem; margin-bottom: 0.5rem; }
    .content-section { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 2rem; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .search-box { position: relative; }
    .search-box input { padding: 8px 12px 8px 35px; border: 1px solid #ddd; border-radius: 6px; }
    .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999; }
    
    .admin-table { width: 100%; border-collapse: collapse; }
    .admin-table th { text-align: left; padding: 12px; border-bottom: 2px solid #eee; color: #888; font-size: 0.85rem; uppercase; }
    .admin-table td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
    
    .action-btn { background: #4A4A3A; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; transition: 0.2s; }
    .action-btn:hover { background: #86A873; }
    
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; bg: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; background-color: rgba(0,0,0,0.5); }
    .modal-content { background: white; padding: 2rem; border-radius: 12px; width: 90%; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    .modal-close-btn { position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; background: none; border: none; cursor: pointer; }
</style>

@endsection