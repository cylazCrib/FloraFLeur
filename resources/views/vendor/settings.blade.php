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
    
    {{-- PAYMENT QR CODES SECTION --}}
    <section class="content-container" style="margin-bottom: 2rem;">
        <div class="content-header"><h2>Payment Settings - E-Wallet QR Codes</h2></div>
        <form class="styled-form" id="payment-qr-form" enctype="multipart/form-data" method="POST" action="{{ route('vendor.settings.payment-qr') }}">
            @csrf
            <div class="form-group">
                <label for="email">Shop Email</label>
                <input type="email" name="email" id="email" placeholder="shop@example.com" value="{{ auth()->user()->shop->email ?? '' }}">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                {{-- GCash QR --}}
                <div>
                    <div class="form-group">
                        <label for="gcash-qr">GCash QR Code</label>
                        <input type="file" name="gcash_qr" id="gcash-qr" accept="image/*" />
                        <small style="color: #666;">Upload your GCash QR code (JPG, PNG)</small>
                    </div>
                    <div id="gcash-qr-preview">
                        @if(auth()->user()->shop->gcash_qr_url ?? false)
                            <div style="margin-top: 1rem; border: 1px solid #ddd; padding: 1rem; border-radius: 8px;">
                                <p style="font-weight: bold; margin-bottom: 0.5rem;">Current GCash QR:</p>
                                <img src="{{ auth()->user()->shop->gcash_qr_url }}" alt="GCash QR" style="max-width: 150px; height: auto; border-radius: 8px;">
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Maya QR --}}
                <div>
                    <div class="form-group">
                        <label for="maya-qr">Maya QR Code</label>
                        <input type="file" name="maya_qr" id="maya-qr" accept="image/*" />
                        <small style="color: #666;">Upload your Maya QR code (JPG, PNG)</small>
                    </div>
                    <div id="maya-qr-preview">
                        @if(auth()->user()->shop->maya_qr_url ?? false)
                            <div style="margin-top: 1rem; border: 1px solid #ddd; padding: 1rem; border-radius: 8px;">
                                <p style="font-weight: bold; margin-bottom: 0.5rem;">Current Maya QR:</p>
                                <img src="{{ auth()->user()->shop->maya_qr_url }}" alt="Maya QR" style="max-width: 150px; height: auto; border-radius: 8px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="payment-instructions">Payment Instructions (for customers)</label>
                <textarea name="payment_instructions" id="payment-instructions" rows="3" placeholder="e.g., Please scan and pay within 24 hours. Include your order number as reference.">{{ auth()->user()->shop->payment_instructions ? implode("\n", auth()->user()->shop->payment_instructions) : '' }}</textarea>
                <small style="color: #666;">Clear instructions for customers on how to pay</small>
            </div>

            <button type="submit" class="action-button" style="width: fit-content;"><i class="fa-solid fa-save"></i> Save Payment Settings</button>
        </form>
    </section>

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