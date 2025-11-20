@extends('layouts.admin')

@section('content')

<main id="owners-view" class="view active-view">
    <header class="main-header">
        <h1>Owners & Notifications</h1>
        <div class="header-icons-wrapper">
            <a class="nav-item" href="{{ route('landing') }}" title="Back to Landing Page"><i class="fa-solid fa-arrow-left"></i></a>
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
                <thead>
                    <tr>
                        <th>Owner</th>
                        <th>Shop Name</th>
                        <th class="text-center">Activity</th>
                    </tr>
                </thead>
                <tbody id="owners-table-body">
                    @forelse($shops as $shop)
                        <tr data-name="{{ $shop->name }}"
                            data-products="{{ $shop->products_count ?? '0' }}" 
                            data-status="{{ $shop->status }}"
                            data-date="{{ $shop->created_at->format('M d, Y') }}"
                            data-location="{{ $shop->address ?? 'N/A' }}">
                            
                            <td>{{ $shop->user->name ?? 'Unknown User' }}</td>
                            <td>{{ $shop->name }}</td>
                            <td class="text-center">
                                <button data-action="viewActivity" class="action-button blue" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View Activity</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No active shops found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    
    <section class="content-container">
        <div class="content-header">
            <h2>Send Notification</h2>
        </div>
        
        <form id="owner-notification-form" class="styled-form" action="{{ route('admin.owners.notify') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required value="{{ old('subject') }}">
                @error('subject')
                    <span style="color: red; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                @error('message')
                    <span style="color: red; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-actions">
                <button type="submit" class="action-button blue">Send to All</button>
            </div>
        </form>
    </section>
</main>

@endsection