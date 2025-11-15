@extends('layouts.admin')

@section('content')

  <main id="dashboard-view" class="view active-view">
                <header class="main-header">
                    <h1>Hi, Admin!</h1>
                    <div class="header-icons-wrapper"> 
                        <a class="nav-item" href="{{ route('landing') }}" title="Back to Landing Page"><i class="fa-solid fa-arrow-left"></i></a>
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal"></i>
                    </div>
                </header>
                <section class="summary-cards">
                    <div id="total-shops-card" class="card card-sales">
                        <h2>Total Shops</h2>
                        <p class="card-main-value">0</p>
                        <p class="card-sub-value">Active</p>
                    </div>
                    <div id="new-entries-card" class="card card-glass-light">
                        <h2>New Entries</h2>
                        <p class="card-main-value">0</p>
                        <p class="card-sub-value">for approval</p>
                    </div>
                    <div id="total-products-card" class="card card-glass-dark">
                        <h2>Total Products</h2>
                        <p class="card-main-value">0</p>
                        <p class="card-sub-value">across all shops</p>
                    </div>
                </section>
                <section class="chart-container">
                    <div class="content-header">
                        <h2>Shop Registrations Overview</h2>
                    </div>
                    <canvas id="registrationsChart" height="100"></canvas>
                </section>
            </main>

            <main id="registrations-view" class="view">
                <header class="main-header">
                    <h1>New Shop Registrations</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal"></i>
                    </div>
                </header>
                <section class="content-container">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Shop Name</th><th>Owner</th><th>Date Submitted</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                            <tbody id="registrations-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <main id="vendors-view" class="view">
                <header class="main-header">
                    <h1>All Shops & Vendors</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal"></i>
                    </div>
                </header>
                <section class="content-container">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Shop Name</th><th>Owner</th><th>Location</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                            <tbody id="shops-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <main id="owners-view" class="view">
                <header class="main-header">
                    <h1>Owners & Notifications</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal"></i>
                    </div>
                </header>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header">
                        <h2>Shop Owners</h2>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Owner</th><th>Shop Name</th><th class="text-center">Activity</th></tr></thead>
                            <tbody id="owners-table-body">
                                </tbody>
                        </table>
                    </div>
                </section>
                <section class="content-container">
                    <div class="content-header">
                        <h2>Send Notification</h2>
                    </div>
                    <form id="owner-notification-form" class="styled-form">
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" rows="5" required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="action-button blue">Send to All</button>
                        </div>
                    </form>
                </section>
            </main>

            <main id="gmail-view" class="view">
                <header class="main-header">
                    <h1>Gmail Integration</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal"></i>
                    </div>
                </header>
                <section class="content-container form-container">
                    <p>Connect your gmail account to send automated notifications and updates to customers.</p>
                    <form class="styled-form" style="max-width: 500px;" id="gmail-form">
                        <div class="form-group">
                            <label for="gmail-email">Gmail Address</label>
                            <input type="email" id="gmail-email" required>
                        </div>
                        <div class="form-group">
                            <label for="gmail-app-password">App Password</label>
                            <input type="password" id="gmail-app-password" required>
                        </div>
                        <button type="submit" class="action-button green" style="width: fit-content;">Connect Account</button>
                    </form>
                </section>
            </main>

            <main id="settings-view" class="view">
                <header class="main-header">
                    <h1>Settings</h1>
                    <div class="header-icons-wrapper">
                        <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal"></i>
                        <i class="fa-regular fa-bell" data-modal-target="notifications-modal"></i>
                        <i class="fa-regular fa-user" data-modal-target="profile-modal"></i>
                    </div>
                </header>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header"><h2>Admin Settings</h2></div>
                    <form class="styled-form" style="max-width: 500px;" id="password-form">
                        <div class="form-group">
                            <label for="current-password">Current Password</label>
                            <input type="password" id="current-password" required>
                        </div>
                        <div class="form-group">
                            <label for="new-password">New Password</label>
                            <input type="password" id="new-password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirm New Password</label>
                            <input type="password" id="confirm-password" required>
                        </div>
                        <button type="submit" class="action-button blue" style="width: fit-content;">Save Changes</button>
                    </form>
                </section>
                <section class="content-container">
                    <div class="content-header"><h2>Notification Preferences</h2></div>
                    <div class="styled-form">
                        <label class="flex items-center"><input type="checkbox" class="h-4 w-4 rounded mr-2" checked> Email on New Registration</label>
                        <label class="flex items-center"><input type="checkbox" class="h-4 w-4 rounded mr-2" checked> Email for Low Stock Alerts</label>
                    </div>
                </section>
            </main>

@endsection