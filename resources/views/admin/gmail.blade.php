@extends('layouts.admin')

@section('content')

<main id="gmail-view" class="view active-view">
    <header class="main-header">
        <h1>Gmail Integration</h1>
        <div class="header-icons-wrapper">
            <a class="nav-item" href="{{ route('landing') }}" title="Back to Landing Page"><i class="fa-solid fa-arrow-left"></i></a>
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

@endsection