@extends('layouts.admin')

@section('content')

<main id="vendors-view" class="view active-view">
    <header class="main-header">
        <h1>All Shops & Vendors</h1>
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
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="shops-table-body">

                    @forelse($shops as $shop)
                        <tr data-name="{{ $shop->name }}"
                            data-owner="{{ $shop->user->name }}"
                            data-date="{{ $shop->created_at->format('M d, Y') }}"
                            data-location="{{ $shop->address ?? 'N/A' }}"
                            data-status="{{ $shop->status }}"
                            data-email="{{ $shop->user->email }}"
                            data-phone="{{ $shop->phone ?? 'N/A' }}"
                            data-category="Florist"
                            data-products="N/A" data-description="{{ $shop->description }}"
                            data-permit-url="{{ Storage::url($shop->permit_url) }}">

                            <td>{{ $shop->name }}</td>
                            <td>{{ $shop->user->name }}</td>
                            <td>{{ $shop->address ?? 'N/A' }}</td>
                            <td>
                                @if($shop->status == 'approved')
                                    <span class="status status-approved">Approved</span>
                                @else
                                    <span class="status status-suspended">Suspended</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <button data-action="details" data-type="shop" class="action-button blue" style="padding: 0.5rem 1rem; font-size: 0.85rem;">View Details</button>

                                <form action="{{ route('admin.vendors.toggle', $shop->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')

                                    @if($shop->status == 'approved')
                                        <button type="submit" class="action-button yellow" style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-left: 0.5rem;">Suspend</button>
                                    @else
                                        <button type="submit" class="action-button green" style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-left: 0.5rem;">Activate</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No approved or suspended shops found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

@endsection