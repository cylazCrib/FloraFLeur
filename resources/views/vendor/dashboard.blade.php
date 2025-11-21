@extends('layouts.vendor')

@section('content')

<main id="dashboard-view" class="view active-view">
    <header class="main-header">
        <h1 style="text-transform: uppercase;">G'DAY, {{ Auth::user()->shop->name ?? 'SHOP' }}!</h1>
        <div class="header-icons-wrapper"> 
            <a class="nav-item" href="{{ route('landing') }}" title="Back to Landing Page"><i class="fa-solid fa-arrow-left"></i></a>
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-user"></i>
        </div>
    </header>

    <section class="summary-cards">
        <div class="card card-sales">
            <h2>Total Sales</h2>
            <p class="card-main-value">₱{{ number_format($totalSales, 2) }}</p>
            <p class="card-sub-value">All time</p>
        </div>
        <div class="card card-orders">
            <h2>Orders</h2>
            <p class="card-main-value">{{ $totalOrders }}</p>
            <p class="card-sub-value">Pending: {{ $pendingOrders }} • Delivered: {{ $deliveredOrders }}</p>
        </div>
        <div class="card card-inventory">
            <h2>Inventory</h2>
            <p class="card-main-value">{{ $inventoryCount }} items</p>
            <p class="card-sub-value" style="{{ $lowStockCount > 0 ? 'color: #D32F2F; font-weight: bold;' : '' }}">
                Low Stock: {{ $lowStockCount }}
            </p>
        </div>
    </section>

    <section class="content-container">
        <div class="content-header">
            <h2>Recent Orders</h2>
            
            @if($lowStockCount > 0)
                <a href="{{ route('vendor.inventory.index') }}" class="stock-alert-btn">
                    <i class="fa-solid fa-triangle-exclamation"></i> Stocks Alert!
                </a>
            @endif
        </div>
        
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>
                                @if($order->items->count() > 0)
                                    {{ $order->items->first()->product_name }}
                                    @if($order->items->count() > 1)
                                        <small class="text-gray-500">(+{{ $order->items->count() - 1 }})</small>
                                    @endif
                                @else
                                    No items
                                @endif
                            </td>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="status status-{{ Str::slug($order->status) }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding: 2rem;">No recent orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 1rem; text-align: right;">
            <a href="{{ route('vendor.orders.index') }}" style="color: #A38D8C; font-weight: 600; text-decoration: none;">
                View All Orders &rarr;
            </a>
        </div>
    </section>
</main>

@endsection