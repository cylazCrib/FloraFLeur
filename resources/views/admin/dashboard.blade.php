@extends('layouts.admin')

@section('content')

<style>
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* [FIXED] Changed 'bg' to 'background-color' */
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        backdrop-filter: blur(2px);
    }
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .badge-count {
        background: #D32F2F; color: white; padding: 2px 6px; 
        border-radius: 10px; font-size: 10px; margin-left: 5px;
    }
</style>

<div class="dashboard-container"> 
    
    <aside class="sidebar">
        <div class="logo"><span>FLORA</span><img src="{{ asset('images/image_8bd93d.png') }}" class="logo-icon"><span>FLEUR</span></div>
        <nav class="navigation">
            <ul>
                <li><a class="nav-item active" data-target="dashboard-view"><i class="fa-solid fa-table-cells-large"></i> Dashboard</a></li>
                <li>
                    <a class="nav-item" data-target="registrations-view">
                        <i class="fa-solid fa-file-signature"></i> Registrations
                        @if($stats['pending_shops'] > 0) <span class="badge-count">{{ $stats['pending_shops'] }}</span> @endif
                    </a>
                </li>
                <li><a class="nav-item" data-target="vendors-view"><i class="fa-solid fa-store"></i> Vendors List</a></li>
                <li><a class="nav-item" data-target="owners-view"><i class="fa-solid fa-user-group"></i> Owners Activity</a></li>
            </ul>
        </nav>
        <div class="sidebar-bottom">
            <ul><li><form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-item" id="logout-btn" style="width:100%;text-align:left;border:none;background:none;color:#D32F2F;"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button></form></li></ul>
        </div>
    </aside>

    <div class="main-content">
        
        <main id="dashboard-view" class="view active-view">
            <header class="main-header">
                <h1>Hi, Admin!</h1>
                <div class="header-icons-wrapper"> 
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <i class="fa-regular fa-bell"></i>
                    <i class="fa-regular fa-user"></i>
                </div>
            </header>
            
            <section class="summary-cards">
                <div class="card card-sales"><h2>Total Shops</h2><p class="card-main-value">{{ $stats['active_vendors'] }}</p><p class="card-sub-value">Active & Suspended</p></div>
                <div class="card card-orders"><h2>Pending Req.</h2><p class="card-main-value">{{ $stats['pending_shops'] }}</p></div>
                <div class="card card-inventory"><h2>Total Users</h2><p class="card-main-value">{{ $stats['total_users'] }}</p></div>
            </section>

            @if($pendingShops->count() > 0)
            <section class="content-container" style="margin-top: 2rem; border-left: 4px solid #D32F2F;">
                <div class="content-header"><h2 style="color: #D32F2F;">New Registrations Needed Review</h2></div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead><tr><th>Shop</th><th>Owner</th><th>Date</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach($pendingShops as $shop)
                            <tr>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->user->name ?? 'Unknown' }}</td>
                                <td>{{ $shop->created_at->format('M d, Y') }}</td>
                                <td><button class="action-button small" onclick="document.querySelector('[data-target=registrations-view]').click()">Review Now</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @endif
        </main>

        <main id="registrations-view" class="view">
            <header class="main-header"><h1>NEW REGISTRATIONS</h1></header>
            <section class="content-container">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead><tr><th>Shop Name</th><th>Owner</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse($pendingShops as $shop)
                            @php 
                                $shopData = [
                                    'id' => $shop->id, 'name' => $shop->name, 'owner' => $shop->user->name ?? 'Unknown',
                                    'email' => $shop->user->email ?? 'N/A', 'address' => $shop->address ?? 'N/A',
                                    'description' => $shop->description ?? 'N/A', 'permit_url' => $shop->permit_url ?? null
                                ];
                            @endphp
                            <tr>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->user->name ?? 'Unknown' }}</td>
                                <td>{{ $shop->created_at->format('M d, Y') }}</td>
                                <td><span class="status status-pending">Pending</span></td>
                                <td class="flex gap-2">
                                    <button class="table-action-btn view-details-btn" data-json="{{ json_encode($shopData) }}">View Details</button>
                                    <button class="table-action-btn approve-btn" data-id="{{ $shop->id }}" style="color:green;">Approve</button>
                                    <button class="table-action-btn reject-btn" data-id="{{ $shop->id }}" style="color:red;">Reject</button>
                                </td>
                            </tr>
                            @empty <tr><td colspan="5" class="text-center p-4">No pending registrations.</td></tr> @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <main id="vendors-view" class="view">
            <header class="main-header"><h1>ALL SHOPS</h1></header>
            <section class="content-container">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead><tr><th>Shop Name</th><th>Owner</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse($activeShops as $shop)
                            <tr>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->user->name ?? 'Unknown' }}</td>
                                <td>
                                    @if(in_array(strtolower($shop->status), ['active', 'approved']))
                                        <span style="color:green; font-weight:bold;">Approved</span>
                                    @else
                                        <span style="color:orange; font-weight:bold;">Suspended</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="table-action-btn toggle-vendor-btn" data-id="{{ $shop->id }}">
                                        {{ in_array(strtolower($shop->status), ['active', 'approved']) ? 'Suspend' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                            @empty <tr><td colspan="4" class="text-center p-4">No active vendors.</td></tr> @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <main id="owners-view" class="view">
            <header class="main-header"><h1>OWNER ACTIVITY LOGS</h1></header>
            <section class="content-container">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead><tr><th>Owner</th><th>Shop Name</th><th>Joined Date</th><th>Activity</th></tr></thead>
                        <tbody>
                            @forelse($owners as $shop)
                            <tr>
                                <td>{{ $shop->user->name ?? 'Unknown' }}</td>
                                <td>{{ $shop->name }}</td>
                                <td>{{ $shop->created_at->format('M d, Y') }}</td>
                                <td>
                                    <button class="table-action-btn view-activity-btn" data-id="{{ $shop->id }}">
                                        <i class="fa-solid fa-chart-line"></i> View Activity
                                    </button>
                                </td>
                            </tr>
                            @empty <tr><td colspan="4" class="text-center p-4">No owners found.</td></tr> @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <main id="gmail-view" class="view"><header class="main-header"><h1>Gmail Integration</h1></header><section class="content-container"><p>Connect Gmail...</p></section></main>
        <main id="settings-view" class="view"><header class="main-header"><h1>Settings</h1></header><section class="content-container"><p>Admin Settings...</p></section></main>

    </div>
</div>

<div id="activity-modal" class="modal-overlay">
    <div class="modal-content" style="max-width:500px;">
        <button class="modal-close-btn" data-close-modal> × </button>
        <h2 class="modal-title" style="margin-bottom:1rem;">Activity Log</h2>
        <h4 id="act-shop-name" style="color:#86A873; margin-bottom:1rem;"></h4>
        <div id="activity-list" style="max-height:300px; overflow-y:auto; border-top:1px solid #eee; padding-top:10px;"></div>
    </div>
</div>

<div id="details-modal" class="modal-overlay">
    <div class="modal-content" style="max-width:600px;">
        <button class="modal-close-btn" data-close-modal> × </button>
        <h2 class="modal-title">Shop Details</h2>
        <div id="details-content" style="margin-top:1rem; display:grid; grid-template-columns: 1fr 1fr; gap: 20px;"></div>
        <div style="margin-top:1rem; display:flex; gap:10px; justify-content:flex-end; border-top: 1px solid #eee; padding-top: 10px;">
            <button class="action-button green approve-from-modal">Approve</button>
            <button class="action-button red reject-from-modal">Reject</button>
        </div>
    </div>
</div>

@endsection