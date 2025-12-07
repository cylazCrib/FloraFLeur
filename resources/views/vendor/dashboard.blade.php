@extends('layouts.vendor')

@section('content')

@php
    $jsOrders = $orders->map(fn($o) => [ 
        'id' => $o->order_number, 'db_id' => $o->id, 'customer' => $o->customer_name, 'phone' => $o->customer_phone,
        'items' => $o->items->map(fn($i) => $i->quantity . 'x ' . $i->product_name)->join(', '),
        'quantity' => $o->items->sum('quantity'), 'total' => $o->total_amount, 
        'delivery' => $o->delivery_date ? \Carbon\Carbon::parse($o->delivery_date)->format('M d, h:i A') : 'Pending',
        'status' => $o->status, 'driver' => $o->driver_name, 'address' => $o->delivery_address
    ]);
    $jsProducts = $products->map(fn($p) => [ 
        'id' => $p->id, 'name' => $p->name, 'description' => $p->description, 'price' => $p->price, 'image' => Storage::url($p->image), 'category' => $p->category 
    ]);
    $jsInventory = $inventory->map(fn($i) => [
        'id' => $i->id, 'name' => $i->name, 'code' => $i->code ?? '-', 'type' => $i->type, 'quantity' => $i->quantity
    ]);
    
    // [FIX] Using $staff instead of $staffMembers
    $jsStaff = $staff->map(fn($s) => [
        'id' => $s->id, 'name' => $s->name, 'email' => $s->email, 'phone' => $s->phone ?? '-', 'role' => $s->role, 'status' => $s->status ?? 'Active'
    ]);
    
    $jsDrivers = $drivers->map(fn($d) => ['name' => $d->name]);
@endphp

<div id="db-data" 
    data-orders="{{ json_encode($jsOrders) }}" 
    data-products="{{ json_encode($jsProducts) }}"
    data-inventory="{{ json_encode($jsInventory) }}"
    data-staff="{{ json_encode($jsStaff) }}"
    data-drivers="{{ json_encode($jsDrivers) }}"
    class="hidden"></div>

<div class="dashboard-container"> 
    <aside class="sidebar">
        <div class="logo"><span>FLORA</span><img src="{{ asset('images/image_8bd93d.png') }}" class="logo-icon"><span>FLEUR</span></div>
        <nav class="navigation">
            <ul>
                <li><a class="nav-item active" data-target="dashboard-view"><i class="fa-solid fa-table-cells-large"></i> Dashboard</a></li>
                <li><a class="nav-item" data-target="orders-view"><i class="fa-solid fa-truck-fast"></i> Orders & Deliveries</a></li>
                <li><a class="nav-item" data-target="inventory-view"><i class="fa-solid fa-box-archive"></i> Inventory</a></li>
                <li><a class="nav-item" data-target="products-view"><i class="fa-solid fa-boxes-stacked"></i> Manage Products</a></li>
                <li><a class="nav-item" data-target="staff-view"><i class="fa-solid fa-user-group"></i> Manage Staff</a></li>
            </ul>
        </nav>
        <div class="sidebar-bottom">
            <ul><li><form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-item" id="logout-btn" style="width:100%;text-align:left;border:none;background:none;color:#D32F2F;"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button></form></li></ul>
        </div>
    </aside>

    <div class="main-content">
        
        <main id="dashboard-view" class="view active-view">
            <header class="main-header"><h1>G'DAY, {{ Auth::user()->shop->name ?? 'SHOP' }}!</h1></header>
            <section class="summary-cards">
                <div class="card card-sales"><h2>Total Sales</h2><p class="card-main-value">₱{{ number_format($totalSales, 2) }}</p></div>
                <div class="card card-orders"><h2>Orders</h2><p class="card-main-value">{{ $totalOrders }}</p></div>
                <div class="card card-inventory">
                    <h2>Inventory</h2>
                    <p class="card-main-value">{{ $inventoryCount }} items</p>
                    <p class="card-sub-value" style="{{ ($lowStockCount ?? 0) > 0 ? 'color: #D32F2F; font-weight: bold;' : '' }}">Low Stock: {{ $lowStockCount ?? 0 }}</p>
                </div>
            </section>
             <section class="content-container">
                <div class="content-header"><h2>Recent Orders</h2></div>
                <div class="table-wrapper"><table class="data-table"><thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead><tbody>
                    @forelse($recentOrders as $order)
                        <tr><td>#{{ $order->order_number }}</td><td>{{ $order->customer_name }}</td><td>₱{{ number_format($order->total_amount, 2) }}</td><td><span class="status status-{{ Str::slug($order->status) }}">{{ $order->status }}</span></td></tr>
                    @empty
                        <tr><td colspan="4" class="text-center p-4">No recent orders.</td></tr>
                    @endforelse
                </tbody></table></div>
            </section>
        </main>

        <main id="orders-view" class="view">
            <header class="main-header"><h1>MANAGE ORDERS</h1></header>
            <section class="content-container" style="margin-bottom: 2rem;">
                <div class="content-header"><h2>All Orders</h2><div style="display:flex; gap:10px;"><button class="action-button" onclick="location.reload()">Refresh</button><button class="action-button" id="new-order-btn">+ Manual Order</button></div></div>
                <div class="table-wrapper"><table class="data-table"><thead><tr><th>ID</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th></tr></thead><tbody id="orders-table-body"></tbody></table></div>
            </section>
            <section class="content-container">
                <div class="content-header"><h2>Delivery Management</h2></div>
                <div class="table-wrapper"><table class="data-table"><thead><tr><th>Order #</th><th>Address</th><th>Driver</th><th>Action</th></tr></thead><tbody id="delivery-table-body"></tbody></table></div>
            </section>
        </main>

        <main id="products-view" class="view">
            <header class="main-header"><h1>PRODUCTS</h1></header>
            <section class="content-container">
                <div class="content-header"><h2>List</h2><button class="action-button" id="new-product-btn">+ Product</button></div>
                <div class="table-wrapper"><table class="data-table"><thead><tr><th>Img</th><th>Name</th><th>Description</th><th>Price</th><th>Action</th></tr></thead><tbody id="products-table-body"></tbody></table></div>
            </section>
        </main>

        <main id="inventory-view" class="view">
            <header class="main-header"><h1>INVENTORY</h1></header>
            <section class="content-container" style="margin-bottom: 2rem;">
                <div class="content-header"><h2>Items Inventory</h2><button class="action-button" id="new-item-btn" data-type="item">+ Add Item</button></div>
                <div class="inventory-list" id="items-inventory-list">
                    @forelse($items as $item)
                        <div class="inventory-item" style="display:flex; justify-content:space-between; align-items:center; padding:1rem; background:#FCEFF0; border-radius:10px; margin-bottom:0.5rem; border:1px solid #eee;">
                            <div>
                                <div style="font-weight:600;">{{ $item->code ? $item->code.' - ' : '' }}{{ $item->name }}</div>
                                <div style="font-size:0.85rem; color:#666;">{{ $item->quantity }} pcs remaining @if($item->quantity<=5)<span style="color:red;font-weight:bold;">Low Stock!</span>@endif</div>
                            </div>
                            <div><button class="table-action-btn update-item-btn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-code="{{ $item->code }}" data-quantity="{{ $item->quantity }}" data-type="item">Edit</button> <button class="table-action-btn del-inv" data-id="{{ $item->id }}" style="color:red;">Remove</button></div>
                        </div>
                    @empty <p class="text-center text-gray-500 p-4">No items yet.</p> @endforelse
                </div>
            </section>
            <section class="content-container">
                <div class="content-header"><h2>Flowers Inventory</h2><button class="action-button" id="new-flower-btn" data-type="flower">+ Add Flower</button></div>
                <div class="inventory-list" id="flowers-inventory-list">
                    @forelse($flowers as $item)
                        <div class="inventory-item" style="display:flex; justify-content:space-between; align-items:center; padding:1rem; background:#FCEFF0; border-radius:10px; margin-bottom:0.5rem; border:1px solid #eee;">
                            <div>
                                <div style="font-weight:600;">{{ $item->code ? $item->code.' - ' : '' }}{{ $item->name }}</div>
                                <div style="font-size:0.85rem; color:#666;">{{ $item->quantity }} pcs remaining @if($item->quantity<=5)<span style="color:red;font-weight:bold;">Low Stock!</span>@endif</div>
                            </div>
                            <div><button class="table-action-btn update-item-btn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-code="{{ $item->code }}" data-quantity="{{ $item->quantity }}" data-type="flower">Edit</button> <button class="table-action-btn del-inv" data-id="{{ $item->id }}" style="color:red;">Remove</button></div>
                        </div>
                    @empty <p class="text-center text-gray-500 p-4">No flowers yet.</p> @endforelse
                </div>
            </section>
        </main>

        <main id="staff-view" class="view">
            <header class="main-header"><h1>STAFF</h1></header>
            <section class="content-container">
                <div class="content-header"><h2>My Team</h2><button class="action-button" id="new-staff-btn">+ Add Staff</button></div>
                <div class="table-wrapper"><table class="data-table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr></thead><tbody>
                    @forelse($staff as $s)
                    <tr>
                        <td>{{ $s->name }}</td><td>{{ $s->email }}</td><td>{{ $s->role }}</td>
                        <td><span class="status status-active">Active</span></td>
                        <td>
                            <button class="table-action-btn edit-staff-btn" data-id="{{ $s->id }}" data-name="{{ $s->name }}" data-email="{{ $s->email }}" data-phone="{{ $s->phone }}" data-role="{{ $s->role }}">Edit</button>
                            <button class="table-action-btn del-staff" style="color:red" data-id="{{ $s->id }}">Remove</button>
                        </td>
                    </tr>
                    @empty <tr><td colspan="5" class="text-center p-4">No staff members found.</td></tr> @endforelse
                </tbody></table></div>
            </section>
        </main>

        <main id="gmail-view" class="view"><header class="main-header"><h1>Gmail</h1></header><section class="content-container"><p>Integration settings here.</p></section></main>
        <main id="settings-view" class="view"><header class="main-header"><h1>Settings</h1></header><section class="content-container"><p>System settings here.</p></section></main>
    </div>
</div>

<div id="order-form-modal" class="modal-overlay" style="display: none;"><div class="modal-content"><button class="modal-close-btn" data-close-modal> × </button><h2 class="modal-title">Manual Order</h2><form class="styled-form" id="order-form"><div class="form-group"><label>Customer</label><input name="customer_name" required></div><div class="form-group"><label>Phone</label><input name="customer_phone" required></div><div class="form-group"><label>Address</label><input name="delivery_address" required></div><div class="form-group"><label>Item</label><input name="product_name" required></div><div class="form-group"><label>Total</label><input name="total_amount" type="number" required></div><div class="form-group"><label>Payment</label><select name="payment_method"><option>Cash</option><option>G-Cash</option></select></div><button type="submit" class="action-button">Create</button></form></div></div>
<div id="product-form-modal" class="modal-overlay" style="display:none;"><div class="modal-content"><button class="modal-close-btn" data-close>x</button><h2>Product</h2><form id="product-form" class="styled-form" enctype="multipart/form-data"><input type="hidden" name="product_id" id="prod_id"><div class="form-group"><label>Name</label><input name="name" id="prod_name" required></div><div class="form-group"><label>Desc</label><input name="description" id="prod_desc" required></div><div class="form-group"><label>Price</label><input name="price" id="prod_price" type="number" required></div><div class="form-group"><label>Image</label><input name="image" type="file"></div><div class="form-group"><label>Category</label><select name="category" id="prod_cat"><option value="bouquet">Bouquet</option><option value="box">Box</option></select></div><button class="action-button">Save</button></form></div></div>
<div id="inventory-form-modal" class="modal-overlay" style="display:none;"><div class="modal-content"><button class="modal-close-btn" data-close>x</button><h2 id="inv-modal-title">Item</h2><form id="inventory-form" class="styled-form"><input type="hidden" name="item_id" id="item_id"><input type="hidden" name="type" id="inv-type"><div class="form-group"><label>Name</label><input name="name" id="inv_name" required></div><div class="form-group"><label>Code</label><input name="code" id="inv_code"></div><div class="form-group"><label>Qty</label><input name="quantity" id="inv_qty" type="number" required></div><button class="action-button">Save</button></form></div></div>
<div id="staff-form-modal" class="modal-overlay" style="display:none;"><div class="modal-content"><button class="modal-close-btn" data-close>x</button><h2>Staff</h2><form id="staff-form" class="styled-form"><input type="hidden" name="staff_id" id="staff_id"><div class="form-group"><label>Name</label><input name="name" id="s_name" required></div><div class="form-group"><label>Email</label><input name="email" id="s_email" required></div><div class="form-group"><label>Phone</label><input name="phone" id="s_phone"></div><div class="form-group"><label>Role</label><select name="role" id="s_role"><option>Manager</option><option>Driver</option></select></div><button class="action-button">Save</button></form></div></div>

<div id="order-details-modal" class="modal-overlay" style="display: none;"><div class="modal-content"><button class="modal-close-btn" data-close-modal> × </button><h2 class="modal-title">Order Details</h2><div id="order-details-content"></div></div></div>
<div id="toast"></div>

@endsection