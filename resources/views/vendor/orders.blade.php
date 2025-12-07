@extends('layouts.vendor')

@section('content')

@php
    $ordersData = $orders->map(function($order) {
        return [
            'id' => $order->order_number, // displayed ID
            'db_id' => $order->id, // real DB ID for ajax calls
            'customer' => $order->customer_name,
            'phone' => $order->customer_phone,
            'email' => $order->customer_email,
            'items' => $order->items->pluck('product_name')->join(', '), // Simple string list
            'quantity' => $order->items->sum('quantity') . ' pcs',
            'total' => $order->total_amount,
            'delivery' => $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('M d, h:i A') : 'Pending',
            'status' => $order->status, // Pending, Being Made, Delivered, etc.
            'driver' => $order->driver_name,
            'address' => $order->delivery_address,
            // Full items array for details modal
            'full_items' => $order->items->map(fn($i) => [
                'name' => $i->product_name,
                'qty' => $i->quantity,
                'price' => $i->price,
                'total' => $i->quantity * $i->price
            ])
        ];
    });

    $driversData = $drivers->map(fn($d) => ['name' => $d->name]);
@endphp

<div id="db-orders-data" data-json="{{ json_encode($ordersData) }}" class="hidden"></div>
<div id="db-drivers-data" data-json="{{ json_encode($driversData) }}" class="hidden"></div>

<main id="orders-view" class="view active-view">
    <header class="main-header">
        <h1>MANAGE ORDERS & DELIVERIES</h1>
        <div class="header-icons-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-user"></i>
        </div>
    </header>

    <section class="content-container" style="margin-bottom: 2rem;">
        <div class="content-header">
            <h2>All Orders</h2>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="search-wrapper"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search..."></div>
                <button class="action-button" id="new-order-btn">+ Manual Order</button>
            </div>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Qty</th><th>Delivery</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="orders-table-body"></tbody>
            </table>
        </div>
    </section>

    <section class="content-container">
        <div class="content-header">
            <h2>Delivery Management</h2>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>Order #</th><th>Customer</th><th>Address</th><th>Driver</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="delivery-table-body"></tbody>
            </table>
        </div>
    </section>
</main>

<div id="modal-container">
    <div class="modal-overlay" id="order-details-modal" style="display: none;">
        <div class="modal-content">
            <button class="modal-close-btn" data-close-modal> &times; </button>
            <h2 class="modal-title">Order Details</h2>
            <div id="order-details-content"></div>
        </div>
    </div>
</div>

@endsection