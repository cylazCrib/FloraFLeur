@extends('layouts.vendor')

@section('content')
<main id="settings-view" class="view active-view">
    <header class="main-header">
        <h1>Settings</h1>
        <div class="header-icons-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-user"></i>
        </div>
    </header>
    
    <section class="content-container" style="margin-bottom: 2rem;">
        <div class="content-header"><h2>Reports & Analytics</h2></div>
        <form class="form-grid" id="report-form" data-url="{{ route('vendor.settings.report') }}">
            <div class="form-group">
                <label for="report-type">Report Type</label>
                <input type="text" name="type" id="report-type" value="Sales Summary" readonly>
            </div>
            <div class="form-group">
                <label>Period</label>
                <select name="period">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
            <div class="form-group full-width">
                <div class="form-actions">
                    <button type="submit" class="action-button" id="generate-pdf-btn">Generate PDF</button>
                </div>
            </div>
        </form>
    </section>

    <section class="content-container">
        <div class="content-header"><h2>System Announcements</h2></div>
        <form class="styled-form" id="announcement-form" data-url="{{ route('vendor.settings.announcement') }}">
            <div class="form-group">
                <label for="announcement-title">Announcement Title</label>
                <input type="text" name="title" id="announcement-title" placeholder="e.g., Holiday Schedule" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="announcement-message">Message</label>
                <textarea name="message" id="announcement-message" rows="3" placeholder="Enter your message here..." required></textarea>
            </div>
            <div class="form-group">
                <label for="target-audience">Target Audience</label>
                <select name="target" id="target-audience">
                    <option value="Florists">All Florists</option>
                    <option value="Drivers">All Drivers</option>
                    <option value="Everyone">Everyone</option>
                </select>
            </div>
            <button type="submit" class="action-button" style="width: fit-content;">Post Announcement</button>
        </form>
    </section>
</main>
@endsection