@extends('layouts.admin')

@section('content')

<main id="registrations-view" class="view active-view">
    <header class="main-header">
        <h1>New Shop Registrations</h1>
        <div class="header-icons-wrapper">
            <a class="nav-item" href="{{ route('landing') }}" title="Back to Landing Page"><i class="fa-solid fa-arrow-left"></i></a>
            <i class="fa-solid fa-magnifying-glass" data-modal-target="search-modal"></i>
            <i class="fa-regular fa-bell" data-modal-target="notifications-modal"></i>
            <i class="fa-regular fa-user" data-modal-target="profile-modal"></i>
        </div>
    </header>
    
    <section class="content-container">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Shop Name</th>
                        <th>Owner</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="registrations-table-body">
                    
                    @forelse($pending_shops as $shop)
                        <tr data-name="{{ $shop->name }}"
                            data-owner="{{ $shop->user->name }}"
                            data-date="{{ $shop->created_at->format('M d, Y') }}"
                            data-location="{{ $shop->address ?? 'N/A' }}"
                            data-status="{{ $shop->status }}"
                            data-email="{{ $shop->user->email }}"
                            data-phone="{{ $shop->phone ?? 'N/A' }}"
                            data-category="Florist"
                            data-products="N/A"
                            data-description="{{ $shop->description }}"
                            data-permit-url="{{ Storage::url($shop->permit_url) }}">
                            
                            <td>{{ $shop->name }}</td>
                            <td>{{ $shop->user->name }}</td> 
                            <td>{{ $shop->created_at->format('M d, Y') }}</td>
                            <td><span class="status status-pending">{{ $shop->status }}</span></td>
                            
                            <td class="text-center">
                                <button data-action="details" data-type="registration" class="action-button blue" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View Details</button>
                                
                                <form action="{{ route('admin.registrations.approve', $shop->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-button green" style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-left: 0.5rem;">Approve</button>
                                </form>

                                <form action="{{ route('admin.registrations.reject', $shop->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-button red" style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-left: 0.5rem;">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No new registrations.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

@endsection