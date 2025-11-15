@extends('layouts.vendor')

    @section('content')
 <main id="dashboard-view" class="view active-view">
                <header class="main-header">
                    <h1>G'DAY, PETAL PARADISE SHOP!</h1>
                    <div class="header-icons-wrapper"> 
                        <a class="nav-item" href="{{ route('landing') }}" title="Back to Landing Page"><i class="fa-solid fa-arrow-left"></i></a>
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal" data-modal-title="Search"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal" data-modal-title="Notifications"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal" data-modal-title="My Profile"></i>
                    </div>
                </header>
                <section class="summary-cards">
                    <div class="card card-sales"><h2>Total Sales</h2><p class="card-main-value">₱ 214,560</p><p class="card-sub-value">Last 30 days</p></div>
                    <div class="card card-orders"><h2>Orders</h2><p class="card-main-value">214</p><p class="card-sub-value">Pending: 12 • Delivered: 265</p></div>
                    <div class="card card-inventory"><h2>Inventory</h2><p class="card-main-value">96 items</p><p class="card-sub-value">Low Stock: 7</p></div>
                </section>
                <section class="content-container">
                    <div class="content-header">
                        <h2>Recent Orders</h2>
                        <a href="#" id="stock-alert-btn" class="stock-alert-btn" style="display: none;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Stocks Alert!
                        </a>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr><td>#FF-1102</td><td>Maria Lopez</td><td>Rose Bouquet (1)</td><td>₱1,250</td><td><span class="status status-pending">Pending</span></td></tr>
                                <tr><td>#FF-1101</td><td>Maria Lopez</td><td>Basket Bouquet (1)</td><td>₱2,300</td><td><span class="status status-approved">Approved</span></td></tr>
                                <tr><td>#FF-1101</td><td>Elia Santos</td><td>Basket Bouquet (1)</td><td>₱3,480</td><td><span class="status status-delivered">Delivered</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <main id="orders-view" class="view">
                <header class="main-header">
                    <h1>MANAGE ORDERS & DELIVERIES</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal" data-modal-title="Search"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal" data-modal-title="Notifications"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal" data-modal-title="My Profile"></i>
                    </div>
                </header>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header">
                        <h2>All Orders</h2>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="search-wrapper"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search by Order ID, customer name, or phone..."></div>
                            <button class="action-button" id="new-order-btn">+ New Order</button>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Quantity</th><th>Delivery</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody id="orders-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
                <section class="content-container">
                    <div class="content-header">
                        <h2>Delivery Management</h2>
                        <div class="search-wrapper"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search by Order ID, customer name, or phone..."></div>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Delivery</th><th>Status</th><th>Action</th><th>Track Orders</th></tr></thead>
                            <tbody id="delivery-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <main id="inventory-view" class="view">
                <header class="main-header">
                    <h1>MANAGE INVENTORY</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal" data-modal-title="Search"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal" data-modal-title="Notifications"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal" data-modal-title="My Profile"></i>
                    </div>
                </header>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header">
                        <h2>Items Inventory</h2>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="search-wrapper"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search by Order ID, customer name, or phone..."></div>
                            <button class="action-button" data-modal-target="item-form-modal" data-modal-type="item">+ Add new Items</button>
                        </div>
                    </div>
                    <div class="inventory-list" id="items-inventory-list">
                        </div>
                </section>
                <section class="content-container">
                    <div class="content-header">
                        <h2>Flowers Inventory</h2>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="search-wrapper"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search by Order ID, customer name, or phone..."></div>
                            <button class="action-button" data-modal-target="item-form-modal" data-modal-type="flower">+ Add new Items</button>
                        </div>
                    </div>
                    <div class="inventory-list" id="flowers-inventory-list">
                        </div>
                </section>
            </main>
    @endsection
   