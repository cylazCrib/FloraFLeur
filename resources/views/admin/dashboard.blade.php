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
            <p class="card-main-value">{{ $totalShops }}</p>
            <p class="card-sub-value">Active</p>
        </div>
        <div id="new-entries-card" class="card card-glass-light">
            <h2>New Entries</h2>
            <p class="card-main-value">{{ $newEntries }}</p>
            <p class="card-sub-value">for approval</p>
        </div>
        <div id="total-products-card" class="card card-glass-dark">
            <h2>Total Products</h2>
            <p class="card-main-value">{{ $totalProducts }}</p>
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

@endsection