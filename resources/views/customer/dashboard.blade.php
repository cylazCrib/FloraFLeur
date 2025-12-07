@extends('layouts.customer')

@section('content')

@php
    $productsData = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => Storage::url($product->image),
            'shop_name' => $product->shop->name ?? 'Shop',
            'category' => $product->category ?? 'bouquet',
            'occasion' => 'all' 
        ];
    });

    $ordersData = $orders->map(function($order) {
        return [
            'id' => $order->order_number,
            'date' => $order->created_at->format('M d, Y'),
            'status' => match($order->status) {
                'Pending', 'Being Made', 'Out for Delivery' => 'to-ship',
                'Delivered' => 'completed',
                'Canceled' => 'refund',
                default => 'to-ship'
            },
            'real_status' => $order->status,
            'total' => $order->total_amount,
            'items' => $order->items->map(function($item) {
                return [
                    'name' => $item->product_name,
                    'qty' => $item->quantity,
                    'price' => $item->price
                ];
            })
        ];
    });
@endphp

<div id="db-products-payload" data-products="{{ json_encode($productsData) }}" class="hidden"></div>
<div id="db-orders-payload" data-orders="{{ json_encode($ordersData) }}" class="hidden"></div>

<div id="app-container-dashboard" class="app-container relative min-h-screen w-full flex-col font-sans bg-[#F5F5F0]">
    
    <div id="bg-image-container" class="absolute inset-0 w-full h-full z-0 transition-opacity duration-500">
        <img src="{{ asset('images/image_7f3485.jpg') }}" alt="Background" class="w-full h-full object-cover opacity-100">
        <div class="absolute inset-0 bg-black/40 z-10"></div>
    </div>

    <div class="relative z-20 flex flex-col min-h-screen w-full">
        <header class="w-full bg-[#4A4A3A] shadow-md relative z-50">
            <nav class="max-w-[1920px] mx-auto px-6 h-20 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="#" class="nav-link flex items-center gap-2 group cursor-pointer select-none" data-target="view-dashboard">
                        <span class="font-rosarivo text-2xl tracking-widest text-white">FLORA</span>
                        <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="w-8 h-8 object-contain">
                        <span class="font-rosarivo text-2xl tracking-widest text-white">FLEUR</span>
                    </a>
                </div>
                <div class="hidden lg:flex items-center space-x-10">
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 uppercase h-full cursor-pointer outline-none bg-transparent border-none">
                            OCCASIONS <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute top-full left-0 w-full h-4 bg-transparent"></div>
                        <div class="absolute top-[calc(100%-0.5rem)] left-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top pt-2">
                            <button class="nav-link-filter block w-full text-left px-6 py-3 text-sm text-white hover:bg-[#5c5c48] bg-transparent border-none" data-filter="valentines" data-title="Valentine's Collection">Valentine's</button>
                            <button class="nav-link-filter block w-full text-left px-6 py-3 text-sm text-white hover:bg-[#5c5c48] bg-transparent border-none" data-filter="wedding" data-title="Wedding Collection">Wedding</button>
                        </div>
                    </div>
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 uppercase h-full cursor-pointer outline-none bg-transparent border-none">
                            TYPES <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute top-full left-0 w-full h-4 bg-transparent"></div>
                        <div class="absolute top-[calc(100%-0.5rem)] left-0 w-56 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top pt-2">
                            <button class="nav-link-filter block w-full text-left px-6 py-3 text-sm text-white hover:bg-[#5c5c48] bg-transparent border-none" data-filter="bouquet" data-title="Bouquet Flowers">Bouquets</button>
                            <button class="nav-link-filter block w-full text-left px-6 py-3 text-sm text-white hover:bg-[#5c5c48] bg-transparent border-none" data-filter="box" data-title="Flower Boxes">Boxes</button>
                            <button class="nav-link-filter block w-full text-left px-6 py-3 text-sm text-white hover:bg-[#5c5c48] bg-transparent border-none" data-filter="standee" data-title="Standee Flowers">Standees</button>
                            <button class="nav-link-filter block w-full text-left px-6 py-3 text-sm text-white hover:bg-[#5c5c48] bg-transparent border-none" data-filter="potted" data-title="Potted Plants">Plants</button>
                        </div>
                    </div>
                    <button class="nav-link h-20 flex items-center font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 uppercase bg-transparent border-none" data-target="view-account">ACCOUNT</button>
                    <button class="nav-link h-20 flex items-center font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 uppercase bg-transparent border-none" data-target="view-about">ABOUT US</button>
                    <button class="nav-link h-20 flex items-center font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 uppercase bg-transparent border-none" data-target="view-request">REQUEST</button>
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 uppercase h-full cursor-pointer outline-none bg-transparent border-none">
                            MY PURCHASES <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute top-[calc(100%-0.5rem)] right-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top pt-2">
                            <button class="nav-link block w-full text-left px-6 py-3 text-sm text-white hover:bg-[#5c5c48] bg-transparent border-none" data-target="view-purchases" data-tab="to-ship">To Ship</button>
                            <button class="nav-link block w-full text-left px-6 py-3 text-sm text-white hover:bg-[#5c5c48] bg-transparent border-none" data-target="view-purchases" data-tab="completed">Completed</button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-8">
                    <div class="nav-link flex flex-col items-center justify-center group relative cursor-pointer" data-target="view-cart">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span id="cart-badge" class="absolute -top-2 -right-2 bg-[#E65100] text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center opacity-0">0</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="bg-[#6B6B61] hover:bg-[#7D7D73] text-white px-5 py-2 rounded text-xs font-medium shadow-sm">LOGOUT</button></form>
                </div>
            </nav>
        </header>

        <main id="view-dashboard" class="view-section active flex-grow flex flex-col items-center px-4 py-12">
            <div class="text-center mb-14 mt-4">
                <h1 class="text-3xl md:text-4xl font-normal text-white">
                    <span class="block mb-2 font-light text-gray-200">Hey {{ Auth::user()->name }},</span>
                    Welcome to Flora Fleur
                </h1>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 w-full max-w-[1400px]">
                <div class="nav-link-filter group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer" data-filter="all" data-title="All Products">
                    <img src="{{ asset('images/image_7eb546.jpg') }}" class="w-full h-full object-cover transition duration-700 hover:scale-110">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center"><span class="text-2xl font-rosarivo text-white">Shop All</span></div>
                </div>
                 <div class="nav-link-filter group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer mt-0 lg:mt-8" data-filter="bouquet" data-title="Bouquets">
                    <img src="{{ asset('images/image_8bd93d.png') }}" class="w-full h-full object-cover transition duration-700 hover:scale-110">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center"><span class="text-2xl font-rosarivo text-white">Bouquets</span></div>
                </div>
                 <div class="nav-link-filter group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer" data-filter="box" data-title="Flower Boxes">
                    <img src="{{ asset('images/image_855a2d.png') }}" class="w-full h-full object-cover transition duration-700 hover:scale-110">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center"><span class="text-2xl font-rosarivo text-white">Boxes</span></div>
                </div>
                 <div class="nav-link-filter group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer mt-0 lg:mt-8" data-filter="potted" data-title="Potted Plants">
                    <img src="{{ asset('images/image_956669.png') }}" class="w-full h-full object-cover transition duration-700 hover:scale-110">
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center"><span class="text-2xl font-rosarivo text-white">Plants</span></div>
                </div>
            </div>
            <div class="flex justify-center w-full mt-4">
                <button class="nav-link-filter bg-[#86A873] hover:bg-[#759465] text-white text-sm font-bold py-3 px-10 rounded-lg shadow-lg" data-filter="all" data-title="All Collections">Browse Shop</button>
            </div>
        </main>

        <main id="view-products" class="view-section hidden flex-grow w-full max-w-[1400px] mx-auto px-4 py-12 pt-28">
             <h2 id="product-category-title" class="font-rosarivo text-4xl text-white mb-8 pl-4 border-l-4 border-dashboard-btn bg-white/10 backdrop-blur-md pr-4 py-2 rounded-r-lg inline-block">Collection</h2>
             <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-20"></div>
        </main>

        <main id="view-cart" class="view-section hidden flex-grow w-full max-w-5xl mx-auto px-4 py-12 pt-28 text-gray-800">
            <h2 class="font-rosarivo text-4xl text-white mb-8">Your Cart</h2>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr><th class="px-6 py-4">Product</th><th class="px-6 py-4">Price</th><th class="px-6 py-4">Qty</th><th class="px-6 py-4">Total</th><th class="px-6 py-4"></th></tr>
                        </thead>
                        <tbody id="cart-items-container"></tbody>
                    </table>
                </div>
                <div class="p-8 bg-gray-50 border-t flex flex-col items-end">
                    <div class="flex justify-between w-full max-w-xs mb-6 text-lg font-bold text-[#4A4A3A]">
                        <span>Total</span><span id="cart-total">₱0.00</span>
                    </div>
                    <button class="btn-checkout-page bg-[#4A4A3A] text-white px-8 py-3 rounded-lg font-bold shadow-lg">Proceed to Checkout</button>
                </div>
            </div>
        </main>
        
        <main id="view-purchases" class="view-section hidden flex-grow w-full max-w-5xl mx-auto px-4 py-12 pt-28 text-gray-800">
             <h2 class="font-rosarivo text-4xl text-white mb-6">My Purchases</h2>
             <div class="flex border-b border-white/30 mb-8 w-fit">
                <button class="purchase-tab active px-6 py-3 text-white border-b-2 border-[#86A873]" data-tab="to-ship">To Ship</button>
                <button class="purchase-tab px-6 py-3 text-gray-400 border-b-2 border-transparent hover:text-white" data-tab="completed">Completed</button>
             </div>
             <div id="purchases-list" class="w-full max-w-4xl space-y-4"></div>
        </main>

        <main id="view-account" class="view-section hidden flex-grow w-full max-w-4xl mx-auto px-4 py-12 pt-28 text-white">
            <div class="bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-xl text-center h-full max-w-md mx-auto border border-white/20">
                <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center text-3xl text-black shadow-inner">👤</div>
                <h2 class="font-rosarivo text-2xl mb-1 text-gray-800">{{ Auth::user()->name }}</h2>
                <p class="text-sm text-gray-300 mb-6">{{ Auth::user()->email }}</p>
                <div class="space-y-4 text-left bg-black/20 p-4 rounded">
                     <label class="text-xs text-gray-400 uppercase">Address</label>
                     <p class="font-medium">Philippines</p>
                </div>
            </div>
        </main>

        <main id="view-about" class="view-section hidden flex-grow w-full max-w-4xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]">
            <div class="bg-white p-10 rounded-xl shadow-xl">
                <h2 class="font-rosarivo text-4xl mb-6 text-center">About Us</h2>
                <p class="text-center">Welcome to Flora Fleur.</p>
            </div>
        </main>

        <main id="view-request" class="view-section hidden flex-grow w-full max-w-2xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]">
             <div class="bg-white p-10 rounded-xl shadow-xl">
                <h2 class="font-rosarivo text-3xl mb-4 text-center">Request Arrangement</h2>
                <textarea class="w-full h-40 border rounded-lg p-4 mb-4 bg-gray-50" placeholder="Describe your dream bouquet..."></textarea>
                <button class="w-full bg-dashboard-btn text-white py-3 rounded-lg font-bold hover:bg-opacity-90">Send Request</button>
            </div>
        </main>
        
        <main id="view-checkout" class="view-section hidden flex-grow w-full max-w-6xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]">
             <div class="bg-white p-10 rounded-xl shadow-xl w-full max-w-2xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Checkout</h2>
                <div id="checkout-cart-items" class="space-y-2 mb-6"></div>
                <div class="border-t pt-4">
                    <div class="flex justify-between font-bold text-lg mb-4"><span>Total</span><span id="checkout-total">₱0.00</span></div>
                    <div class="grid grid-cols-3 gap-2 mb-6">
                        <button class="payment-btn py-2 border rounded bg-[#86A873] text-white selected" data-method="Cash On Delivery">COD</button>
                        <button class="payment-btn py-2 border rounded" data-method="E-Wallet">E-Wallet</button>
                        <button class="payment-btn py-2 border rounded" data-method="Card">Card</button>
                    </div>
                    <button class="btn-place-order w-full bg-[#4A4A3A] text-white py-3 rounded-lg font-bold shadow-lg">PLACE ORDER</button>
                </div>
             </div>
        </main>

        <footer class="w-full p-4 text-center relative z-20 mt-auto bg-[#4A4A3A] text-white">
            <p class="text-xs text-gray-400 tracking-wide">&copy; 2025 FLORA FLEUR. ALL RIGHTS RESERVED.</p>
        </footer>
    </div>
</div>

<div id="product-modal" class="app-container fixed inset-0 z-50 items-center justify-center p-4 hidden" style="background-color: rgba(0,0,0,0.6);">
    <div class="relative w-full max-w-4xl bg-[#F5F5F0] rounded-xl shadow-2xl flex flex-col md:flex-row overflow-hidden">
        <button class="modal-close-btn absolute top-6 left-6 bg-white/80 px-4 py-2 rounded-full shadow font-bold text-[#4A4A3A]">BACK</button>
        <div class="w-full md:w-1/2 h-64 md:h-auto relative"><img id="modal-product-image" class="absolute inset-0 w-full h-full object-cover"></div>
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center text-gray-800 pt-16">
            <input type="hidden" id="modal-product-id">
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
                <button class="btn-add-cart flex-1 bg-gray-200 py-3 rounded-lg font-bold uppercase hover:bg-gray-300">Add to Cart</button>
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

<div id="order-toast" class="fixed top-6 right-6 bg-[#4A4A3A] text-white py-3 px-6 rounded-lg shadow-xl transform translate-x-[120%] transition-transform duration-300 z-[60]">
    <p class="font-bold text-sm">Notification</p>
</div>

@endsection