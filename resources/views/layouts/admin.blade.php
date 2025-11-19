<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flora Fleur - Admin Dashboard</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

</head>
<body class="admin-vendor-layout"> 
    <div class="dashboard-container"> 
        
        <aside class="sidebar">
            <div class="logo">
                <span>FLORA</span>
                <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="logo-icon">
                <span>FLEUR</span>
            </div>
           <nav class="navigation">
    <ul>
        <li>
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
               <i class="fa-solid fa-table-cells-large"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.registrations.index') }}" 
               class="nav-item {{ request()->routeIs('admin.registrations.index') ? 'active' : '' }}">
               <i class="fa-solid fa-file-signature"></i> Manage Registrations
            </a>
        </li>
        <li>
            <a href="{{ route('admin.vendors.index') }}"
               class="nav-item {{ request()->routeIs('admin.vendors.index') ? 'active' : '' }}">
               <i class="fa-solid fa-store"></i> Manage Shop/Vendors
            </a>
        </li>
        <li>
            <a href="{{ route('admin.owners.index') }}"
               class="nav-item {{ request()->routeIs('admin.owners.index') ? 'active' : '' }}">
               <i class="fa-solid fa-user-group"></i> Owners & Notifs
            </a>
        </li>
    </ul>
</nav>
            <div class="sidebar-bottom">
    <p>Integration</p>
    <ul>
        <li>
            <a href="{{ route('admin.gmail.index') }}"
               class="nav-item {{ request()->routeIs('admin.gmail.index') ? 'active' : '' }}">
               <i class="fa-regular fa-envelope"></i> Gmail
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings.index') }}"
               class="nav-item {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
               <i class="fa-solid fa-gear"></i> Settings
            </a>
        </li>
        <li>
            <a class="nav-item" id="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Log Out
            </a>
        </li>
    </ul>
</div>
        </aside>

        <div class="main-content">
            @yield('content')
        </div> 

        <div id="modal-container">
            <div class="modal-overlay" id="search-modal">
                <div class="modal-content">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 1.5rem;">Search</h2>
                    <div class="search-wrapper" style="width: 100%;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search..." style="width: 100%;"></div>
                </div>
            </div>
            <div class="modal-overlay" id="notifications-modal">
                <div class="modal-content">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 1.5rem;">Notifications</h2>
                    <div class="notification-item">
                        <i class="fa-solid fa-file-alt notification-icon"></i>
                        <div class="notification-content">
                            <p>New registration from Petal Paradise</p>
                            <span>2 hours ago</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-overlay" id="profile-modal">
                <div class="modal-content" style="text-align: center;">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <i class="fa-solid fa-user-circle" style="font-size: 4rem; color: var(--active-nav-bg); margin-bottom: 1rem;"></i>
                    <h2 class="modal-title" style="margin-bottom: 0.5rem;">Admin</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">admin@florafleur.com</p>
                </div>
            </div>
            <div class="modal-overlay" id="details-modal">
                <div class="modal-content" style="max-width: 600px;">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <div id="details-modal-content">
                    </div>
                </div>
            </div>
            <div class="modal-overlay" id="activity-modal">
                <div class="modal-content" style="max-width: 600px;">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <div id="activity-modal-content">
                    </div>
                </div>
            </div>
            <div class="modal-overlay" id="confirm-modal">
                <div class="modal-content" style="max-width: 400px; text-align: center;">
                    <h2 class="modal-title" style="margin-bottom: 1rem;" id="confirm-title">Are you sure?</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;" id="confirm-text"></p>
                    <div class="form-actions" style="justify-content: center;">
                        <button type="button" class="action-button outline" data-close-modal>Cancel</button>
                        <button type="button" class="action-button" id="confirm-btn">Confirm</button>
                    </div>
                </div>
            </div>
            <div class="modal-overlay" id="permit-modal">
                <div class="modal-content" style="max-width: 800px;">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <img id="permit-image" src="" alt="Business Permit" style="width: 100%; height: auto; border-radius: var(--border-radius-md);">
                </div>
            </div>
        </div>

        <div id="toast"></div>

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const toast = document.getElementById('toast');
                    toast.textContent = "{{ session('success') }}";
                    toast.classList.add('show');
                    setTimeout(() => { 
                        toast.classList.remove('show'); 
                    }, 3000);
                });
            </script>
        @endif
    
        <template id="registration-row-template">
            <tr>
                <td data-field="name"></td>
                <td data-field="owner"></td>
                <td data-field="date"></td>
                <td><span class="status" data-field="status"></span></td>
                <td class="text-center">
                    <button data-action="details" data-type="registration" class="action-button blue" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View Details</button>
                    <button data-action="approve" class="action-button green" style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-left: 0.5rem;">Approve</button>
                    <button data-action="reject" class="action-button red" style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-left: 0.5rem;">Reject</button>
                </td>
            </tr>
        </template>
    
        <template id="shop-row-template">
            <tr>
                <td data-field="name"></td>
                <td data-field="owner"></td>
                <td data-field="location"></td>
                <td><span class="status" data-field="status"></span></td>
                <td class="text-center">
                    <button data-action="details" data-type="shop" class="action-button blue" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View Details</button>
                    <button data-action="toggleStatus" class="action-button" style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-left: 0.5rem;"></button>
                </td>
            </tr>
        </template>
    
        <template id="owner-row-template">
            <tr>
                <td data-field="owner"></td>
                <td data-field="shopName"></td>
                <td class="text-center">
                     <button data-action="viewActivity" class="action-button blue" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View Activity</button>
                </td>
            </tr>
        </template>
    
        <template id="details-modal-template">
            <h2 class="modal-title" style="margin-bottom: 1rem; font-size: 1.5rem;" data-field="name"></h2>
            <div class="details-grid">
                <div><p>Owner:</p><strong data-field="owner"></strong></div>
                <div><p>Date Submitted:</p><strong data-field="date"></strong></div>
                <div><p>Location:</p><strong data-field="location"></strong></div>
                <div><p>Status:</p><strong data-field="status"></strong></div>
                <div><p>Email:</p><strong data-field="email"></strong></div>
                <div><p>Phone:</p><strong data-field="phone"></strong></div>
                <div><p>Category:</p><strong data-field="category"></strong></div>
                <div><p>Products:</p><strong data-field="products"></strong></div>
            </div>
            <div style="margin-top: 1rem;">
                <h3 style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-secondary);">Description</h3>
                <p style="color: var(--text-secondary);" data-field="description"></p>
            </div>
            <div style="margin-top: 1rem;">
                <h3 style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-secondary);">Business Permit</h3>
                <button data-action="viewPermit" class="action-button blue" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View Permit</button>
            </div>
        </template>
    
        <template id="activity-modal-template">
            <h2 class="modal-title" style="margin-bottom: 1rem; font-size: 1.5rem;" data-field="name"></h2>
            <div class="details-grid">
                <div><p>Total Products:</p><strong data-field="products"></strong></div>
                <div><p>Shop Status:</p><strong data-field="status"></strong></div>
                <div><p>Registration Date:</p><strong data-field="date"></strong></div>
                <div><p>Location:</p><strong data-field="location"></strong></div>
            </div>
            <div style="margin-top: 1rem;">
                <h3 style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-secondary);">Recent Orders</h3>
                <ul style="color: var(--text-secondary); list-style: disc inside;">
                    <li>Order #FF-1103 - Standee Flowers (Delivered)</li>
                    <li>Order #FF-1098 - Rose Bouquet (Delivered)</li>
                    <li>Order #FF-1120 - Wedding Bouquet (In Progress)</li>
                </ul>
            </div>
            <div style="margin-top: 1rem;">
                <h3 style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-secondary);">Monthly Sales</h3>
                <ul style="color: var(--text-secondary); list-style: disc inside;">
                    <li>September 2025: ₱45,230</li>
                    <li>August 2025: ₱38,750</li>
                    <li>July 2025: ₱42,100</li>
                </ul>
            </div>
        </template>

    </div> </body>
</html>