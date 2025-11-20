@extends('layouts.vendor')

@section('content')

<main id="orders-view" class="view active-view">
    <header class="main-header">
        <h1>MANAGE ORDERS</h1>
        <div class="header-icons-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-regular fa-bell"></i>
            <i class="fa-regular fa-user"></i>
        </div>
    </header>
    
    <section class="content-container">
        <div class="content-header">
            <h2>All Orders</h2>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="order-search" placeholder="Search Order ID or Customer...">
                </div>
                <button class="action-button" id="new-order-btn">+ Add Order</button>
            </div>
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="orders-table-body">
                    @forelse($orders as $order)
                        <tr data-id="{{ $order->id }}">
                            <td>#{{ $order->order_number }}</td>
                            <td>
                                {{ $order->customer_name }}<br>
                                <small class="text-gray-500">{{ $order->customer_phone }}</small>
                            </td>
                            <td>
                                @if($order->items->count() > 0)
                                    {{ $order->items->first()->product_name }}
                                    @if($order->items->count() > 1)
                                        <small>(+{{ $order->items->count() - 1 }} more)</small>
                                    @endif
                                @else
                                    No Items
                                @endif
                            </td>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                            
                            <td>
                                <select class="status-dropdown status-{{ Str::slug($order->status) }}" 
                                        data-url="{{ route('vendor.orders.updateStatus', $order->id) }}"
                                        onchange="updateOrderStatus(this)">
                                    
                                    <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Being Made" {{ $order->status == 'Being Made' ? 'selected' : '' }}>Being Made</option>
                                    <option value="Delivered" {{ $order->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="Canceled" {{ $order->status == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                                </select>
                            </td>

                            <td>
                                <button class="table-action-btn view-order-btn" 
                                        data-url="{{ route('vendor.orders.show', $order->id) }}">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 2rem;">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="order-form-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <button class="modal-close-btn" data-close-modal> &times; </button>
        <h2 class="modal-title" style="margin-bottom: 2rem;">Create Manual Order</h2>
        
        <form class="styled-form" id="order-form">
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="customer_name" required>
            </div>
            <div class="form-group">
                <label>Customer Phone</label>
                <input type="tel" name="customer_phone" required>
            </div>
            <div class="form-group">
                <label>Delivery Address</label>
                <textarea name="delivery_address" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <label>Delivery Date</label>
                <input type="date" name="delivery_date" required>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Select Product</label>
                    <select name="product_id" required>
                        <option value="">-- Choose Item --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (₱{{ $product->price }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" required>
                </div>
            </div>
            
            <div class="form-actions" style="justify-content: flex-end; margin-top: 1rem;">
                <button type="submit" class="action-button">Create Order</button>
            </div>
        </form>
    </div>
</div>

<div id="order-details-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content" style="max-width: 600px;">
        <button class="modal-close-btn" data-close-modal> &times; </button>
        <h2 class="modal-title" style="margin-bottom: 1.5rem;">Order Details</h2>
        
        <div id="order-modal-content">
            <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
                <div>
                    <h3 style="font-weight: bold; font-size: 1.2rem;" id="modal-order-number">#FF-0000</h3>
                    <span id="modal-order-status" class="status">Pending</span>
                </div>
                <div style="text-align: right;">
                    <p id="modal-order-date" style="color: #666; font-size: 0.9rem;">-</p>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <h4 style="font-weight: 600; color: #444; margin-bottom: 0.5rem;">Customer Information</h4>
                <p><strong>Name:</strong> <span id="modal-customer-name">-</span></p>
                <p><strong>Email:</strong> <span id="modal-customer-email">-</span></p>
                <p><strong>Phone:</strong> <span id="modal-customer-phone">-</span></p>
                <p><strong>Address:</strong> <span id="modal-delivery-address">-</span></p>
            </div>

            <h4 style="font-weight: 600; color: #444; margin-bottom: 0.5rem;">Order Items</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 1.5rem;">
                <thead>
                    <tr style="background: #f9f9f9; text-align: left;">
                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Product</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Qty</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">Price</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody id="modal-items-list">
                    </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: right; font-weight: bold;">Grand Total:</td>
                        <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 1.1rem;" id="modal-grand-total">₱0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection