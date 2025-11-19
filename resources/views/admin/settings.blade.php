@extends('layouts.admin')

@section('content')

<main id="settings-view" class="view active-view">
    <header class="main-header">
        <h1>Settings</h1>
        <div class="header-icons-wrapper">
            <a class="nav-item" href="{{ route('landing') }}" title="Back to Landing Page"><i class="fa-solid fa-arrow-left"></i></a>
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