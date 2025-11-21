<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flora Fleur - Vendor Dashboard</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    @vite(['resources/css/vendor.css', 'resources/js/vendor.js'])
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
                        <a href="{{ route('vendor.dashboard') }}" 
                           class="nav-item {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-table-cells-large"></i> Dashboard
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('vendor.orders.index') }}" 
                           class="nav-item {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-truck-fast"></i> Orders & Deliveries
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('vendor.inventory.index') }}" 
                           class="nav-item {{ request()->routeIs('vendor.inventory.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-box-archive"></i> Inventory
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('vendor.products.index') }}" 
                           class="nav-item {{ request()->routeIs('vendor.products.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-boxes-stacked"></i> Manage Products
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('vendor.staff.index') }}" 
                           class="nav-item {{ request()->routeIs('vendor.staff.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-group"></i> Manage Staff
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-bottom">
                <p>Integration</p>
                <ul>
                    <li><a class="nav-item" href="{{ route('vendor.gmail.index') }}"><i class="fa-regular fa-envelope"></i> Gmail</a></li>
                    <li><a class="nav-item" href="{{ route('vendor.settings.index') }}"><i class="fa-solid fa-gear"></i> Settings</a></li>
                    <li><a class="nav-item" id="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></li>
                </ul>
            </div>
        </aside>

        <div class="main-content">
           @yield('content')
        </div>

        <div id="modal-container">
            
            <div class="modal-overlay" id="search-modal">
                <div class="modal-content">
                     <button class="modal-close-btn" data-close-modal> × </button>
                    <h2 class="modal-title" style="margin-bottom: 1.5rem;">Search</h2>
                    <div class="search-wrapper" style="width: 100%;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search..." style="width: 100%;"></div>
                </div>
            </div>

            <div class="modal-overlay" id="notifications-modal">
                <div class="modal-content">
                    <button class="modal-close-btn" data-close-modal> × </button>
                    <h2 class="modal-title" style="margin-bottom: 1.5rem;">Notifications</h2>
                    <div class="notification-item">
                        <i class="fa-solid fa-receipt notification-icon"></i>
                        <div class="notification-content">
                            <p>New order received</p>
                            <span>2 minutes ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="profile-modal">
                <div class="modal-content" style="text-align: center;">
                    <button class="modal-close-btn" data-close-modal> × </button>
                    <i class="fa-solid fa-user-circle" style="font-size: 4rem; color: var(--active-nav-bg); margin-bottom: 1rem;"></i>
                    <h2 class="modal-title" style="margin-bottom: 0.5rem;">{{ Auth::user()->name ?? 'Vendor' }}</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">{{ Auth::user()->email ?? '' }}</p>
                    <button class="action-button" id="profile-logout-btn" style="width: 100%;">Log Out</button>
                </div>
            </div>

             <div class="modal-overlay" id="confirm-modal">
                <div class="modal-content" style="max-width: 400px; text-align: center;">
                    <h2 class="modal-title" style="margin-bottom: 1rem;">Are you sure?</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;" id="confirm-modal-text"></p>
                    <div class="form-actions" style="justify-content: center;">
                        <button type="button" class="action-button outline" data-close-modal>Cancel</button>
                        <button type="button" class="action-button" id="confirm-modal-btn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="toast"></div>

    </div>
</body>
</html>