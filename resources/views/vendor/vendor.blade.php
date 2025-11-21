@extends('layouts.vendor')

@section('content')
<main id="gmail-view" class="view active-view">
    <header class="main-header">
        <h1>Gmail Integration</h1>
        <div class="header-icons-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-user"></i>
        </div>
    </header>
    
    <section class="content-container form-container" style="max-width: 600px;">
        <div class="content-header" style="margin-bottom: 0.5rem;">
            <h2>Email Configuration</h2>
        </div>
        <p>Connect your gmail account to send automated notifications and updates to customers.</p>
        
        <form class="styled-form" id="gmail-form" data-url="{{ route('vendor.gmail.connect') }}">
            <div class="form-group">
                <label for="gmail-email">Gmail Address</label>
                <input type="email" name="gmail_email" id="gmail-email" placeholder="your-email@gmail.com" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="app-password">App Password</label>
                <input type="password" name="app_password" id="app-password" placeholder="Enter 16-digit app password" required autocomplete="off">
            </div>
            <button type="submit" class="action-button green" style="width: fit-content;">Connect Gmail</button>
        </form>
    </section>
</main>
@endsection