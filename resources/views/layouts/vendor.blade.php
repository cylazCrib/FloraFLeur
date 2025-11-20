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
                    <li><a class="nav-item" data-target="gmail-view"><i class="fa-regular fa-envelope"></i> Gmail</a></li>
                    <li><a class="nav-item" data-target="settings-view"><i class="fa-solid fa-gear"></i> Settings</a></li>
                    <li><a class="nav-item" id="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></li>
                </ul>
            </div>
        </aside>

        <div class="main-content">
           @yield('content')
        
            <main id="products-view" class="view">
                <header class="main-header">
                    <h1>MANAGE PRODUCTS</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal" data-modal-title="Search"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal" data-modal-title="Notifications"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal" data-modal-title="My Profile"></i>
                    </div>
                </header>
                <section class="content-container">
                    <div class="content-header">
                        <h2>All Products</h2>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="search-wrapper"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search by product name or description..."></div>
                            <button class="action-button" id="new-product-btn">+ Add New Product</button>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Image</th><th>Name</th><th>Description</th><th>Price</th><th>Action</th></tr></thead>
                            <tbody id="products-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <main id="owners-view" class="view">
                <header class="main-header">
                    <h1>MANAGE OWNERS & NOTIFICATIONS</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal" data-modal-title="Search"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal" data-modal-title="Notifications"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal" data-modal-title="My Profile"></i>
                    </div>
                </header>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header">
                        <h2>Shop Administrators & Staff</h2>
                        <button class="action-button" id="add-staff-btn">+ Add Staff</button>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody id="staff-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header"><h2>Customer Updates & Notifications</h2></div>
                    <form class="form-grid" id="notification-form">
                        <div class="form-group"><label for="select-order">Select Order</label>
                            <select id="select-order" required>
                            <option value="">Choose an order</option>
                            </select>
                        </div>
                        <div class="form-group"><label for="notif-type">Notification Type</label><input type="text" id="notif-type" value="Order Status Update"></div>
                        <div class="form-group full-width">
                            <label>Send to:</label>
                            <div class="send-to-group">
                                <label><input type="checkbox" checked> <i class="fa-regular fa-envelope"></i> Email</label>
                                <label><input type="checkbox" checked> <i class="fa-solid fa-comment-sms"></i> SMS</label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label for="message">Message</label>
                            <textarea id="message" rows="4">Your order FF-1101 is now being prepared and will be delivered on Oct 10, 2:00 PM. Thank you for choosing Flora Fleur!</textarea>
                        </div>
                        <button type="submit" class="action-button">Send Notification</button>
                    </form>
                </section>
                <section class="content-container">
                    <div class="content-header"><h2>Recent Notifications Sent</h2></div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Date & Time</th><th>Customer</th><th>Order ID</th><th>Type</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr><td>Oct 9, 3:45 PM</td><td>Anna Reyes</td><td>#FF-1246</td><td>Delivery Update</td><td><span class="status-sent"><i class="fa-solid fa-check"></i> Sent</span></td></tr>
                                <tr><td>Oct 9, 3:30 PM</td><td>Juan Dela Cruz</td><td>#FF-1247</td><td>Status Update</td><td><span class="status-sent"><i class="fa-solid fa-check"></i> Sent</span></td></tr>
                                <tr><td>Oct 9, 2:10 PM</td><td>Kent Clark</td><td>#FF-1254</td><td>Status Update</td><td><span class="status-sent"><i class="fa-solid fa-check"></i> Sent</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
            
            <main id="gmail-view" class="view">
                <header class="main-header">
                    <h1>Gmail Integration</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal" data-modal-title="Search"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal" data-modal-title="Notifications"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal" data-modal-title="My Profile"></i>
                    </div>
                </header>
                <section class="content-container form-container">
                    <div class="content-header" style="margin-bottom: 0.5rem;">
                        <h2>Email Configuration</h2>
                    </div>
                    <p>Connect your gmail account to send automated notifications and updates to customers.</p>
                    <form class="styled-form" style="max-width: 500px;" id="gmail-form">
                        <div class="form-group">
                            <label for="gmail-email">Gmail</label>
                            <input type="email" id="gmail-email" placeholder="your-email@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label for="app-password">App password</label>
                            <input type="password" id="app-password" placeholder="Enter gmail password" required>
                        </div>
                        <button type="submit" class="action-button" style="width: fit-content;">Connect Gmail</button>
                    </form>
                </section>
            </main>

            <main id="settings-view" class="view">
                <header class="main-header">
                    <h1>Settings</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal" data-modal-title="Search"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal" data-modal-title="Notifications"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal" data-modal-title="My Profile"></i>
                    </div>
                </header>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header"><h2>Reports & Analytics</h2></div>
                    <form class="form-grid">
                        <div class="form-group">
                            <label for="report-type">Report Type</label>
                            <input type="text" id="report-type" value="Sales Summary">
                        </div>
                        <div class="form-group">
                            <label>Period</label>
                            <div class="period-selector">
                                <div class="period-option selected">Today</div>
                                <div class="period-option">This week</div>
                                <div class="period-option">This month</div>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <div class="form-actions">
                                <button type="button" class="action-button" id="generate-pdf-btn">Generate PDF</button>
                                <button type="button" class="action-button outline" id="download-pdf-btn">Download PDF</button>
                            </div>
                        </div>
                    </form>
                </section>
                <section class="content-container">
                    <div class="content-header"><h2>System Announcements</h2></div>
                    <form class="styled-form" id="announcement-form">
                        <div class="form-group">
                            <label for="announcement-title">Announcement Title</label>
                            <input type="text" id="announcement-title" value="Valentine's Day Promo" required>
                        </div>
                        <div class="form-group">
                            <label for="announcement-message">Message</label>
                            <textarea id="announcement-message" rows="3" required>Valentine's Day Promo</textarea>
                        </div>
                        <div class="form-group">
                            <label for="target-audience">Target Audience</label>
                            <select id="target-audience">
                                <option>All Florists</option>
                                <option>All Drivers</option>
                                <option>Everyone</option>
                            </select>
                        </div>
                        <button type="submit" class="action-button" style="width: fit-content;">Post Announcement</button>
                    </form>
                </section>
            </main>
        </div>

        <div id="modal-container">
            <div class="modal-overlay" id="order-details-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="modal-back-btn" data-close-modal><i class="fa-solid fa-arrow-left"></i></button>
                        <h2 class="modal-title">Customer Details</h2>
                    </div>
                    <div id="order-details-content">
                        </div>
                </div>
            </div>

            <div class="modal-overlay" id="order-form-modal">
                <div class="modal-content">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 2rem;">Order Information</h2>
                    <form class="styled-form" id="order-form">
                        <input type="hidden" name="orderId">
                        <div class="form-group"><label>Customer Name</label><input type="text" name="customer" required></div>
                        <div class="form-group"><label>Phone</label><input type="tel" name="phone" required></div>
                        <div class="form-group"><label>Items</label><input type="text" name="items" required></div>
                        <div class="form-group"><label>Quantity</label><input type="text" name="quantity" required></div>
                        <div class="form-group"><label>Delivery Date</label><input type="text" name="delivery" required></div>
                        <button type="submit" class="action-button">Save Order</button>
                    </form>
                </div>
            </div>
            
            <div class="modal-overlay" id="item-form-modal">
                <div class="modal-content">
                     <button class="modal-close-btn" data-close-modal> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 2rem;">Inventory Item</h2>
                    <form class="styled-form" id="item-form">
                        <input type="hidden" name="itemId">
                        <input type="hidden" name="itemType">
                        <div class="form-group"><label>Item Name</label><input name="name" type="text" required></div>
                        <div class="form-group"><label>Item Code</label><input name="code" type="text"></div>
                        <div class="form-group"><label>Quantity</label><input name="quantity" type="number" required></div>
                        <button type="submit" class="action-button">Save Item</button>
                    </form>
                </div>
            </div>

            <div class="modal-overlay" id="staff-form-modal">
                <div class="modal-content">
                     <button class="modal-close-btn" data-close-modal> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 2rem;">Staff Information</h2>
                    <form class="styled-form" id="staff-form">
                        <input type="hidden" name="staffId">
                        <div class="form-group"><label>Name</label><input name="name" type="text" required></div>
                        <div class="form-group"><label>Email</label><input name="email" type="email" required></div>
                        <div class="form-group"><label>Phone</label><input name="phone" type="tel" required></div>
                        <div class="form-group"><label>Role</label><select name="role"><option>Admin</option><option>Manager</option><option>Florist</option><option>Driver</option></select></div>
                        <button type="submit" class="action-button">Save Staff</button>
                    </form>
                </div>
            </div>

            <div class="modal-overlay" id="product-form-modal">
                <div class="modal-content">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 2rem;">Product Information</h2>
                    <form class="styled-form" id="product-form">
                        <input type="hidden" name="productId">
                        <div class="form-group"><label>Name</label><input name="name" type="text" required></div>
                        <div class="form-group"><label>Description</label><textarea name="description" rows="3" required></textarea></div>
                        <div class="form-group"><label>Price (₱)</label><input name="price" type="number" step="0.01" required></div>
                        <div class="form-group"><label>Image</label><input name="image" type="file" accept="image/*"></div>
                        <img id="image-preview" class="image-preview" alt="Image Preview">
                        <button type="submit" class="action-button">Save Product</button>
                    </form>
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

            <div class="modal-overlay" id="search-modal">
                <div class="modal-content">
                     <button class="modal-close-btn" data-close-modal> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 1.5rem;">Search</h2>
                    <div class="search-wrapper" style="width: 100%;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search for orders, items, staff..." style="width: 100%;"></div>
                </div>
            </div>
            <div class="modal-overlay" id="notifications-modal">
                <div class="modal-content">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 1.5rem;">Notifications</h2>
                    <div class="notification-item">
                        <i class="fa-solid fa-receipt notification-icon"></i>
                        <div class="notification-content">
                            <p>New order #FF-1104 received</p>
                            <span>2 minutes ago</span>
                        </div>
                    </div>
                     <div class="notification-item">
                        <i class="fa-solid fa-triangle-exclamation notification-icon"></i>
                        <div class="notification-content">
                            <p>Low stock warning for White Lilies</p>
                            <span>1 hour ago</span>
                        </div>
                    </div>
                     <div class="notification-item">
                        <i class="fa-solid fa-truck notification-icon"></i>
                        <div class="notification-content">
                            <p>Order #FF-1103 has been delivered</p>
                            <span>3 hours ago</span>
                        </div>
                    </div>
                </div>
            </div>
             <div class="modal-overlay" id="profile-modal">
                <div class="modal-content" style="text-align: center;">
                    <button class="modal-close-btn" data-close-modal> &times; </button>
                    <i class="fa-solid fa-user-circle" style="font-size: 4rem; color: var(--active-nav-bg); margin-bottom: 1rem;"></i>
                    <h2 class="modal-title" style="margin-bottom: 0.5rem;">Kassandra Olvis</h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;">kasu@gmail.com</p>
                    <button class="action-button" id="profile-logout-btn" style="width: 100%;">Log Out</button>
                </div>
            </div>
        </div>

        <div id="toast"></div>

    </div>
    
    <template id="order-row-template">
        <tr>
            <td data-field="id"></td>
            <td data-field="customer"><small data-field="phone"></small></td>
            <td data-field="items"></td>
            <td data-field="quantity"></td>
            <td data-field="delivery"></td>
            <td><span class="status" data-field="status"></span></td>
            <td>
                <button class="table-action-btn update-order-status-btn">Update</button>
                <a href="#" class="table-action-btn view-link view-order-btn">View</a>
            </td>
        </tr>
    </template>

    <template id="delivery-row-template">
        <tr>
            <td data-field="id"></td>
            <td data-field="customer"><small data-field="phone"></small></td>
            <td data-field="items"></td>
            <td data-field="delivery"></td>
            <td><span class="status" data-field="status"></span></td>
            <td>
                <div class="form-group" style="margin-bottom: 0;">
                    <select class="driver-select" data-field="driver-select">
                        <option value="">Select Rider</option>
                        </select>
                </div>
            </td>
            <td>
                <button class="table-action-btn assign-driver-btn">Assign</button> 
                <a href="https://www.google.com/maps/search/?api=1&query=Surigao+City" target="_blank" class="table-action-btn view-link">View on Google Maps</a>
            </td>
        </tr>
    </template>

    <template id="staff-row-template">
        <tr>
            <td data-field="name"></td>
            <td data-field="email"></td>
            <td data-field="phone"></td>
            <td><span class="status" data-field="role"></span></td>
            <td><span class="status" data-field="status"></span></td>
            <td>
                <button class="table-action-btn edit-staff-btn">Edit</button>
                <button class="table-action-btn toggle-status-btn" data-field="toggle-status"></button>
            </td>
        </tr>
    </template>

    <template id="inventory-item-template">
        <div class="inventory-item">
            <div class="inventory-item-details">
                <div class="inventory-item-name"><small data-field="code"></small><span data-field="name"></span></div>
                <div class="inventory-item-stock"><span data-field="quantity"></span> pcs remaining <span data-field="stock-alert" class="low-stock-alert"></span></div>
            </div>
            <div class="inventory-item-actions">
                <button class="action-button update-item-btn">Update</button>
                <button class="action-button remove-btn">Remove</button>
            </div>
        </div>
    </template>

    <template id="product-row-template">
        <tr>
            <td><img class="product-image" data-field="image" alt="Product Image"></td>
            <td data-field="name"></td>
            <td data-field="description"></td>
            <td data-field="price"></td>
            <td>
                <button class="table-action-btn edit-product-btn">Edit</button>
                <button class="table-action-btn remove-product-btn">Remove</button>
            </td>
        </tr>
    </template>

    <template id="order-details-template">
        <div class="customer-details">
            <h3 data-field="id"></h3>
            <p data-field="delivery"></p>
            <h3 data-field="customer"></h3>
            <p><span data-field="email"></span><br><span data-field="phone"></span></p>
        </div>
        <div class="order-summary">
            <div class="order-summary-item"><span>Quantity</span><strong data-field="quantity"></strong></div>
            <div class="order-summary-item"><span>Items</span><strong data-field="items"></strong></div>
            <div class="order-summary-item"><span>Total</span><strong>P2300</strong></div> </div>
    </template>


   
</body>
</html>