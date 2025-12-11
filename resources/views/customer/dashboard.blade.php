@extends('layouts.customer')

@section('content')

@php
    // 1. Prepare Products
    $productsData = $products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'description' => $p->description,
        'price' => $p->price,
        'image' => Storage::url($p->image),
        'category' => $p->category ?? 'bouquet',
        'occasion' => $p->occasion ?? 'all'
    ]);
    
    // 2. Prepare Orders (Fixing 'undefined' issues)
    $ordersData = $orders->map(fn($o) => [
        'id' => $o->order_number,
        'date' => $o->created_at->format('M d, Y'),
        'status' => $o->status,
        'total' => $o->total_amount,
        // [NEW] Pass the driver name
        'driver' => $o->driver_name ?? null, 
        // [FIX] Map 'product_name' to 'name' for JS
        'items' => $o->items->map(fn($i) => [
            'name' => $i->product_name, 
            'qty' => $i->quantity,
            'price' => $i->price
        ])
    ]);

    // 3. Prepare Requests
    $requestsData = isset($requests) ? $requests->map(fn($r) => [
        'id' => $r->id,
        'date' => $r->created_at->format('M d, Y'),
        'description' => $r->description,
        'status' => $r->status,
        'budget' => $r->budget
    ]) : [];
@endphp

<div id="db-products-payload" data-products="{{ json_encode($productsData) }}" class="hidden"></div>
<div id="db-orders-payload" data-orders="{{ json_encode($ordersData) }}" class="hidden"></div>
<div id="db-requests-payload" data-requests="{{ json_encode($requestsData) }}" class="hidden"></div>

<div id="app-container-dashboard" class="app-container active relative min-h-screen w-full flex-col font-sans">
    
    <div id="bg-image-container" class="absolute inset-0 w-full h-full z-0 transition-opacity duration-500">
        <img src="{{ asset('images/image_7f3485.jpg') }}" alt="Background" class="w-full h-full object-cover opacity-100">
        <div class="absolute inset-0 bg-black/40 z-10"></div>
    </div>

    <div class="relative z-20 flex flex-col min-h-screen w-full text-white">
        
        <header class="w-full bg-[#4A4A3A] shadow-md relative z-50">
            <nav class="max-w-[1920px] mx-auto px-6 h-20 flex justify-between items-center">
                <div class="nav-link flex items-center gap-2 text-2xl tracking-wider font-rosarivo cursor-pointer" data-target="view-dashboard">
                    <span>FLORA</span><img src="{{ asset('images/image_8bd93d.png') }}" class="w-7 h-7 object-contain"><span>FLEUR</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-10 text-sm font-medium tracking-wider">
                    <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" data-target="view-dashboard">HOME</button>
                    
                    <div class="relative group h-20 flex items-center cursor-pointer">
                        <span class="hover:text-gray-300 flex items-center gap-1 uppercase">Occasions <i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                        <div class="absolute top-[calc(100%-0.5rem)] left-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" data-type="occasion" data-value="valentines">Valentine's</button>
                            <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" data-type="occasion" data-value="wedding">Wedding</button>
                            <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" data-type="occasion" data-value="birthday">Birthday</button>
                        </div>
                    </div>

                    <div class="relative group h-20 flex items-center cursor-pointer">
                        <span class="hover:text-gray-300 flex items-center gap-1 uppercase">Types <i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                        <div class="absolute top-[calc(100%-0.5rem)] left-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" data-type="category" data-value="bouquet">Bouquets</button>
                            <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" data-type="category" data-value="basket">Baskets</button>
                            <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" data-type="category" data-value="standee">Standees</button>
                            <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" data-type="category" data-value="box">Flower Boxes</button>
                        </div>
                    </div>

                    <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" data-target="view-account">ACCOUNT</button>
                    <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" data-target="view-request">REQUEST</button>
                    <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" data-target="view-purchases">MY PURCHASES</button>
                </div>

                <div class="flex items-center gap-8">
                    <div class="nav-link flex flex-col items-center cursor-pointer group relative" data-target="view-cart">
                        <i class="fa-solid fa-cart-shopping text-xl"></i>
                        <span id="cart-badge" class="absolute -top-2 -right-2 bg-[#E65100] text-white text-[10px] rounded-full w-4 h-4 flex justify-center items-center opacity-0 transition-opacity">0</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="bg-[#6B6B61] hover:bg-[#7D7D73] text-white px-5 py-2 rounded text-xs font-medium shadow-sm">LOGOUT</button></form>
                </div>
            </nav>
        </header>

        <main id="view-dashboard" class="view-section active flex-grow flex flex-col justify-center px-4 lg:px-8 py-12 w-full max-w-[1400px] mx-auto text-white">
            <div class="text-center mb-14 mt-4">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-normal leading-tight drop-shadow-lg">
                    <span class="block mb-2 font-light text-gray-200">Hey {{ Auth::user()->name }},</span>
                    <span class="font-medium tracking-wide">Welcome to Flora Fleur</span>
                </h1>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 w-full">
                
                <div class="filter-btn group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer" data-type="all" data-value="all">
                    <img src="{{ asset('images/imagegranopening123.jpg') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                        <span class="text-2xl font-rosarivo tracking-wider text-white drop-shadow-md">Shop All</span>
                    </div>
                </div>

                <div class="filter-btn group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer mt-0 lg:mt-8" data-type="category" data-value="bouquet">
                    <img src="{{ asset('images/bouquet.jpg') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                        <span class="text-2xl font-rosarivo tracking-wider text-white drop-shadow-md">Bouquets</span>
                    </div>
                </div>

                <div class="filter-btn group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer" data-type="category" data-value="basket">
                    <img src="{{ asset('images/flower basket.jpg') }}" onerror="this.src='https://placehold.co/600x800/86A873/white?text=Basket'" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                        <span class="text-2xl font-rosarivo tracking-wider text-white drop-shadow-md">Baskets</span>
                    </div>
                </div>

                <div class="filter-btn group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer mt-0 lg:mt-8" data-type="category" data-value="box">
                    <img src="{{ asset('images/flower boxes.jpg') }}" onerror="this.src='https://placehold.co/600x800/4A4A3A/white?text=Box'" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                        <span class="text-2xl font-rosarivo tracking-wider text-white drop-shadow-md">Flower Boxes</span>
                    </div>
                </div>

            </div>
        </main>

        <main id="view-products" class="view-section hidden flex-grow w-full max-w-[1400px] mx-auto px-4 py-12 pt-28">
             <h2 id="product-category-title" class="font-rosarivo text-4xl text-[#4A4A3A] mb-8 pl-4 border-l-4 border-[#86A873] bg-white/80 backdrop-blur-sm inline-block pr-6 py-2 rounded-r-lg text-gray-800">All Collection</h2>
             <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>
        </main>

        <main id="view-cart" class="view-section hidden flex-grow w-full max-w-5xl mx-auto px-4 py-12 pt-28 text-gray-800">
            <h2 class="font-rosarivo text-4xl text-white mb-8">Your Cart</h2>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto"><table class="w-full text-left"><thead class="bg-gray-50 border-b border-gray-200"><tr><th class="px-6 py-4">Product</th><th class="px-6 py-4">Price</th><th class="px-6 py-4">Qty</th><th class="px-6 py-4">Total</th><th class="px-6 py-4"></th></tr></thead><tbody id="cart-items-container"></tbody></table></div>
                <div class="p-8 bg-gray-50 border-t flex flex-col items-end">
                    <div class="flex justify-between w-full max-w-xs mb-6 text-lg font-bold text-[#4A4A3A]"><span>Total</span><span id="cart-total">₱0.00</span></div>
                    <button class="btn-checkout-page bg-[#4A4A3A] hover:bg-[#3a3a2e] text-white px-8 py-3 rounded-lg font-bold">Proceed to Checkout</button>
                </div>
            </div>
        </main>

        <main id="view-purchases" class="view-section hidden flex-grow w-full max-w-5xl mx-auto px-4 py-12 pt-28 text-gray-800">
             <h2 class="font-rosarivo text-4xl text-white mb-6">My Purchases</h2>
             <div class="flex border-b border-gray-300 mb-8 bg-white/80 backdrop-blur-sm rounded-t-lg w-fit">
                <button class="purchase-tab active px-6 py-3 text-[#4A4A3A] font-bold border-b-2 border-[#86A873]" data-tab="to-ship">To Ship</button>
                <button class="purchase-tab px-6 py-3 text-gray-500 font-bold border-b-2 border-transparent" data-tab="completed">Completed</button>
                <button class="purchase-tab px-6 py-3 text-gray-500 font-bold border-b-2 border-transparent" data-tab="requests">My Requests</button>
             </div>
             <div id="purchases-list" class="space-y-4"></div>
        </main>
        
        <main id="view-account" class="view-section hidden flex-grow w-full max-w-4xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]">
            <div class="bg-white p-10 rounded-xl shadow-xl border border-gray-200">
                <div class="flex items-center gap-6 pb-8 border-b">
                    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center text-3xl shadow-inner">👤</div>
                    <div><h2 class="font-rosarivo text-3xl text-[#4A4A3A]">{{ Auth::user()->name }}</h2><p class="text-gray-500">{{ Auth::user()->email }}</p></div>
                </div>
                <form id="profile-form" class="mt-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div><label class="block text-sm font-bold text-gray-600 mb-1">Name</label><input id="prof-name" class="w-full p-3 border rounded bg-gray-50" value="{{ Auth::user()->name }}"></div>
                        <div><label class="block text-sm font-bold text-gray-600 mb-1">Email</label><input id="prof-email" class="w-full p-3 border rounded bg-gray-50" value="{{ Auth::user()->email }}" readonly></div>
                        <div><label class="block text-sm font-bold text-gray-600 mb-1">Phone</label><input id="prof-phone" class="w-full p-3 border rounded bg-gray-50" value="{{ Auth::user()->phone ?? '' }}" placeholder="09xxxxxxxxx"></div>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-600 mb-1">Address</label><textarea id="prof-address" rows="3" class="w-full p-3 border rounded bg-gray-50">{{ Auth::user()->address ?? '' }}</textarea></div>
                    <div class="text-right"><button type="submit" class="btn-save-profile bg-[#86A873] hover:bg-[#759465] text-white font-bold py-3 px-8 rounded-lg shadow-lg">Save Changes</button></div>
                </form>
            </div>
        </main>

        <main id="view-request" class="view-section hidden flex-grow w-full max-w-2xl mx-auto px-4 py-12 pt-28 text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-10 text-center">Request Arrangement</h1>
            <div class="bg-white/10 border border-page-border-trans rounded-lg shadow-xl p-8 backdrop-blur-md">
                <p class="text-white/80 mb-6 text-center">Describe your dream bouquet or arrangement.</p>
                <form id="custom-request-form" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-white/90 mb-2">Description</label>
                        <textarea id="request-description" rows="4" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white" required placeholder="Describe what you need..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-white/90 mb-2">Budget (₱)</label><input type="number" id="request-budget" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white"></div>
                        <div><label class="block text-sm font-medium text-white/90 mb-2">Contact</label><input type="tel" id="request-contact" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white"></div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn-submit-request bg-[#86A873] text-white font-bold py-3 px-8 rounded-lg shadow-lg">Send Request</button>
                    </div>
                </form>
            </div>
        </main>

        <main id="view-checkout" class="view-section hidden flex-grow w-full max-w-6xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]">
            <h2 class="font-rosarivo text-4xl text-center text-white mb-10">Checkout</h2>
            <div class="flex flex-col gap-8">
                <div class="bg-white p-8 rounded-xl shadow-xl">
                    <h3 class="font-rosarivo text-2xl mb-6 text-center border-b pb-4">Your Order</h3>
                    <div id="checkout-cart-items" class="space-y-2 mb-6"></div>
                    <div class="border-t pt-4"><div class="flex justify-between font-bold text-lg mb-4"><span>Total</span><span id="checkout-total">₱0.00</span></div></div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-xl">
                    <h3 class="font-rosarivo text-xl mb-4">Payment Method</h3>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <button class="payment-btn py-2 border rounded bg-[#86A873] text-white selected" data-method="Cash On Delivery">COD</button>
                        <button class="payment-btn py-2 border rounded" data-method="E-Wallet">E-Wallet</button>
                        <button class="payment-btn py-2 border rounded" data-method="Card">Card</button>
                    </div>
                    <button class="btn-place-order w-full bg-[#4A4A3A] text-white font-bold py-4 rounded-lg shadow-lg uppercase">Place Order</button>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="product-modal" class="app-container fixed inset-0 z-50 items-center justify-center p-4 hidden" style="background-color: rgba(0,0,0,0.6);">
    <div class="relative w-full max-w-4xl bg-[#F5F5F0] rounded-xl shadow-2xl flex flex-col md:flex-row overflow-hidden">
        <button class="modal-close-btn absolute top-4 right-4 text-gray-500 hover:text-red-500 z-50 p-2 text-2xl font-bold">&times;</button>
        <div class="w-full md:w-1/2 h-64 md:h-auto relative"><img id="modal-product-image" class="absolute inset-0 w-full h-full object-cover"></div>
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center text-gray-800 pt-16">
            <h3 id="modal-product-title" class="text-3xl mb-4 font-rosarivo"></h3>
            <p id="modal-product-description" class="text-sm text-gray-600 mb-6"></p>
            <div class="flex justify-between items-center mb-8 border-t pt-6">
                <span id="modal-product-price" class="text-2xl font-bold"></span>
                <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-full border">
                    <button class="quantity-btn-minus font-bold text-lg">-</button>
                    <span id="modal-quantity-value" class="font-bold text-lg">1</span>
                    <button class="quantity-btn-plus font-bold text-lg">+</button>
                </div>
            </div>
            <div class="flex gap-3">
                <button class="btn-add-cart flex-1 bg-gray-200 py-3 rounded-lg font-bold uppercase">Add to Cart</button>
                <button class="btn-checkout-modal flex-1 bg-[#86A873] text-white py-3 rounded-lg font-bold uppercase shadow-lg">Check Out</button>
            </div>
        </div>
    </div>
</div>
<div id="thank-you-modal" class="app-container fixed inset-0 z-50 items-center justify-center p-4 hidden" style="background-color: rgba(0,0,0,0.6);">
    <div class="bg-white rounded-xl shadow-2xl p-10 text-center max-w-md">
        <h2 class="text-3xl font-rosarivo mb-4 text-[#4A4A3A]">Thank You!</h2>
        <p class="text-gray-600 mb-8">Order placed successfully.</p>
        <button class="btn-back-main w-full bg-[#4A4A3A] text-white font-bold py-3 rounded-lg">Back to Dashboard</button>
    </div>
</div>

@endsection