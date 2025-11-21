@extends('layouts.customer')

@section('content')

<div id="app-container-dashboard" class="app-container relative min-h-screen w-full flex-col font-sans bg-[#F5F5F0]">
    
    <!-- Background Image (Only visible on Dashboard Home) -->
    <div id="bg-image-container" class="absolute inset-0 w-full h-full z-0 transition-opacity duration-500">
        <img src="{{ asset('images/image_7f3485.jpg') }}" 
             alt="Background" 
             class="w-full h-full object-cover opacity-100">
        <!-- Dark Overlay for contrast -->
        <div class="absolute inset-0 bg-black/40 z-10"></div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="relative z-20 flex flex-col min-h-screen w-full">
        
        <!-- Top Navigation Bar -->
        <header class="w-full bg-[#4A4A3A] shadow-md relative z-50">
            <nav class="max-w-[1920px] mx-auto px-6 h-20 flex justify-between items-center">
                
                <!-- Left: Logo -->
                <div class="flex items-center gap-3">
                    <a href="#" onclick="app.nav.showDashboard(event)" class="flex items-center gap-2 group cursor-pointer select-none">
                        <span class="font-rosarivo text-2xl tracking-widest text-white group-hover:text-gray-200 transition-colors">FLORA</span>
                        <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="w-8 h-8 object-contain">
                        <span class="font-rosarivo text-2xl tracking-widest text-white group-hover:text-gray-200 transition-colors">FLEUR</span>
                    </a>
                </div>

                <!-- Center: Navigation Links (Visible on Large Screens) -->
                <div class="hidden lg:flex items-center space-x-10">
                    
                    <!-- Occasions Dropdown -->
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 transition-colors uppercase h-full cursor-pointer outline-none">
                            OCCASIONS
                            <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- Invisible Bridge for Dropdown Stability -->
                        <div class="absolute top-full left-0 w-full h-4 bg-transparent"></div>
                        <!-- Dropdown Menu -->
                        <div class="absolute top-[calc(100%-0.5rem)] left-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top pt-2">
                            <a href="#" onclick="app.nav.filterProducts('valentines', 'Valentine\'s Collection')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30 cursor-pointer">Valentine's</a>
                            <a href="#" onclick="app.nav.filterProducts('wedding', 'Wedding Collection')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors cursor-pointer">Wedding</a>
                        </div>
                    </div>

                    <!-- Types Dropdown -->
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 transition-colors uppercase h-full cursor-pointer outline-none">
                            TYPES
                            <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- Invisible Bridge -->
                        <div class="absolute top-full left-0 w-full h-4 bg-transparent"></div>
                        <div class="absolute top-[calc(100%-0.5rem)] left-0 w-56 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top pt-2">
                            <a href="#" onclick="app.nav.filterProducts('bouquet', 'Bouquet Flowers')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30 cursor-pointer">Bouquet Flowers</a>
                            <a href="#" onclick="app.nav.filterProducts('box', 'Flower Boxes')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30 cursor-pointer">Flower Boxes</a>
                            <a href="#" onclick="app.nav.filterProducts('standee', 'Standee Flowers')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30 cursor-pointer">Standee Flowers</a>
                            <a href="#" onclick="app.nav.filterProducts('potted', 'Potted Plants')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors cursor-pointer">Potted Plants</a>
                        </div>
                    </div>

                    <!-- Single Links -->
                    <a href="#" onclick="app.nav.showAccount(event)" class="h-20 flex items-center font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 transition-colors uppercase cursor-pointer select-none">ACCOUNT</a>
                    <a href="#" onclick="app.nav.showAbout(event)" class="h-20 flex items-center font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 transition-colors uppercase cursor-pointer select-none">ABOUT US</a>
                    
                    <!-- My Purchases Dropdown -->
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide text-white hover:text-gray-300 transition-colors uppercase h-full cursor-pointer outline-none">
                            MY PURCHASES
                            <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- Invisible Bridge -->
                        <div class="absolute top-full left-0 w-full h-4 bg-transparent"></div>
                        <div class="absolute top-[calc(100%-0.5rem)] right-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top pt-2">
                            <a href="#" onclick="app.nav.showPurchases('to-ship')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30 cursor-pointer">To Ship</a>
                            <a href="#" onclick="app.nav.showPurchases('completed')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30 cursor-pointer">Completed</a>
                            <a href="#" onclick="app.nav.showPurchases('refund')" class="block px-6 py-3 text-sm text-white hover:bg-[#5c5c48] transition-colors cursor-pointer">Return/Refund</a>
                        </div>
                    </div>
                </div>

                <!-- Right: Icons & Button -->
                <div class="flex items-center gap-8">
                    <!-- Cart -->
                    <div onclick="app.nav.showCart(event)" class="flex flex-col items-center justify-center group relative cursor-pointer hover:scale-105 transition-transform select-none">
                        <div class="relative pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-gray-300 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span id="cart-badge" class="absolute -top-2 -right-2 bg-[#E65100] text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border border-[#4A4A3A] opacity-0 transition-opacity">0</span>
                        </div>
                        <span class="text-[10px] font-lato text-white uppercase mt-1 tracking-wider group-hover:text-gray-300 transition-colors pointer-events-none">Cart</span>
                    </div>

                    <!-- Favorite -->
                    <div onclick="app.nav.showFavorites(event)" class="flex flex-col items-center justify-center group cursor-pointer relative hover:scale-105 transition-transform select-none">
                         <div class="relative pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-gray-300 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span id="fav-badge" class="absolute -top-2 -right-2 bg-[#E65100] text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border border-[#4A4A3A] opacity-0 transition-opacity">0</span>
                        </div>
                        <span class="text-[10px] font-lato text-white uppercase mt-1 tracking-wider group-hover:text-gray-300 transition-colors pointer-events-none">Favorite</span>
                    </div>

                    <!-- Back Button -->
                    <a href="{{ route('landing') }}" class="ml-2 bg-[#6B6B61] hover:bg-[#7D7D73] text-white px-5 py-2 rounded text-xs font-medium tracking-wide transition-colors shadow-sm cursor-pointer">
                        Back to Landing
                    </a>
                </div>
            </nav>
        </header>

        <!-- ================= VIEWS ================= -->

        <!-- 1. DASHBOARD HERO (Default View) -->
        <main id="view-dashboard" class="view-section active flex-grow flex flex-col justify-center px-4 lg:px-8 py-12 w-full max-w-[1400px] mx-auto text-white">
            <div class="text-center mb-14 animate-fade-in mt-4">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-normal leading-tight drop-shadow-lg">
                    <span class="block mb-2 font-light tracking-wide text-gray-200">Hey there,</span>
                    <span class="font-medium tracking-wide">Welcome to Flora Fleur, where flowers are more</span>
                    <span class="block mt-2 font-light tracking-wide text-gray-200">than gifts—they're experiences.</span>
                </h1>
            </div>

            <!-- Categories Grid (Clean Images, No Text) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 w-full">
                <!-- Bouquet -->
                <div onclick="app.nav.filterProducts('bouquet', 'Bouquets')" class="group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="https://i.pinimg.com/736x/20/d9/75/20d975ae5d4f33d0caed1a0dad56b4a1.jpg" alt="Bouquets" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80 transition-opacity group-hover:opacity-60"></div>
                </div>
                <!-- Inaugural -->
                <div onclick="app.nav.filterProducts('standee', 'Inaugural Standees')" class="group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer transform hover:-translate-y-2 transition-transform duration-300 mt-0 lg:mt-8">
                    <img src="https://i.pinimg.com/1200x/43/b3/4f/43b34f35e4fe86163f3a80e32213212c.jpg" alt="Inaugural" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80 transition-opacity group-hover:opacity-60"></div>
                </div>
                <!-- Plants -->
                <div onclick="app.nav.filterProducts('potted', 'Potted Plants')" class="group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="https://i.pinimg.com/736x/0e/24/00/0e2400b844843a0164813e65ced223d8.jpg" alt="Plants" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80 transition-opacity group-hover:opacity-60"></div>
                </div>
                <!-- Flower Boxes -->
                <div onclick="app.nav.filterProducts('box', 'Flower Boxes')" class="group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer transform hover:-translate-y-2 transition-transform duration-300 mt-0 lg:mt-8">
                    <img src="https://i.pinimg.com/736x/21/fb/4a/21fb4a20f8debc581e2dcb467f630f46.jpg" alt="Flower Boxes" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80 transition-opacity group-hover:opacity-60"></div>
                </div>
            </div>

            <div class="flex justify-end w-full mt-4">
                <button onclick="app.nav.filterProducts('all', 'All Collections')" class="bg-[#86A873] hover:bg-[#759465] text-white text-sm font-bold py-3 px-10 rounded-lg shadow-lg transition-all duration-300 tracking-wider transform hover:scale-105 cursor-pointer">
                    Shop Now
                </button>
            </div>
        </main>

        <!-- 2. PRODUCTS LIST VIEW (Hidden by default) -->
        <div id="view-products" class="view-section hidden flex-grow w-full max-w-[1400px] mx-auto px-4 py-12 pt-28">
             <h2 id="product-category-title" class="font-rosarivo text-4xl text-[#4A4A3A] mb-8 pl-4 border-l-4 border-[#86A873]">Collection</h2>
             <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                 <!-- Dynamic Content Loaded via JS -->
             </div>
        </div>

        <!-- 3. CART VIEW -->
        <div id="view-cart" class="view-section hidden flex-grow w-full max-w-5xl mx-auto px-4 py-12 pt-28 text-gray-800">
            <h2 class="font-rosarivo text-4xl text-[#4A4A3A] mb-8">Your Cart</h2>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-right"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items-container" class="divide-y divide-gray-100">
                            <!-- Dynamic Rows -->
                        </tbody>
                    </table>
                </div>
                <div class="p-8 bg-gray-50 border-t border-gray-200 flex flex-col items-end">
                    <div class="flex justify-between w-full max-w-xs mb-6 text-lg font-bold text-[#4A4A3A]">
                        <span>Total</span>
                        <span id="cart-total">₱0.00</span>
                    </div>
                    <button onclick="app.cart.checkout()" class="bg-[#4A4A3A] hover:bg-[#3a3a2e] text-white px-8 py-3 rounded-lg font-bold tracking-wide shadow-lg transition-transform transform hover:scale-105 cursor-pointer">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>

        <!-- 4. FAVORITES VIEW -->
        <div id="view-favorites" class="view-section hidden flex-grow w-full max-w-6xl mx-auto px-4 py-12 pt-28">
            <h2 class="font-rosarivo text-4xl text-[#4A4A3A] mb-8">My Favorites</h2>
            <div id="favorites-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Dynamic Favorites -->
            </div>
             <div id="no-favorites-msg" class="hidden text-center py-20 text-gray-500 italic">
                You haven't saved any items yet.
            </div>
        </div>

        <!-- 5. PURCHASES VIEW -->
        <div id="view-purchases" class="view-section hidden flex-grow w-full max-w-5xl mx-auto px-4 py-12 pt-28 text-gray-800">
            <h2 class="font-rosarivo text-4xl text-[#4A4A3A] mb-6">My Purchases</h2>
            
            <!-- Tabs -->
            <div class="flex border-b border-gray-300 mb-8">
                <button onclick="app.nav.switchPurchaseTab('to-ship')" class="purchase-tab active px-6 py-3 text-sm font-bold text-gray-500 hover:text-[#4A4A3A] border-b-2 border-transparent hover:border-[#86A873] transition-all cursor-pointer" data-tab="to-ship">To Ship</button>
                <button onclick="app.nav.switchPurchaseTab('completed')" class="purchase-tab px-6 py-3 text-sm font-bold text-gray-500 hover:text-[#4A4A3A] border-b-2 border-transparent hover:border-[#86A873] transition-all cursor-pointer" data-tab="completed">Completed</button>
                <button onclick="app.nav.switchPurchaseTab('refund')" class="purchase-tab px-6 py-3 text-sm font-bold text-gray-500 hover:text-[#4A4A3A] border-b-2 border-transparent hover:border-[#86A873] transition-all cursor-pointer" data-tab="refund">Return/Refund</button>
            </div>

            <div id="purchases-list" class="space-y-4">
                <!-- Dynamic Order Cards -->
            </div>
        </div>

        <!-- 6. ABOUT US VIEW -->
        <div id="view-about" class="view-section hidden flex-grow w-full max-w-4xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]">
            <div class="bg-white p-10 rounded-xl shadow-xl">
                <h2 class="font-rosarivo text-4xl mb-6 text-center">About Flora Fleur</h2>
                <div class="space-y-6 text-lg leading-relaxed text-gray-600 font-lato">
                    <p>
                        Welcome to <span class="font-bold text-[#86A873]">Flora Fleur</span>, where nature's beauty meets artistic expression. 
                        Established in 2025, we started with a simple mission: to make every occasion memorable through the language of flowers.
                    </p>
                    <p>
                        Our team of dedicated florists hand-picks every stem, ensuring that only the freshest and most vibrant blooms make it into your arrangements. 
                        Whether it's a grand wedding centerpiece, a heartfelt Valentine's bouquet, or a simple gesture of appreciation, we pour our heart into every petal.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                        <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                            <div class="text-3xl mb-2">🌿</div>
                            <h3 class="font-bold mb-2">Freshly Sourced</h3>
                            <p class="text-sm">Direct from local growers daily.</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                            <div class="text-3xl mb-2">🎨</div>
                            <h3 class="font-bold mb-2">Artfully Crafted</h3>
                            <p class="text-sm">Designed by expert florists.</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                            <div class="text-3xl mb-2">🚚</div>
                            <h3 class="font-bold mb-2">Reliable Delivery</h3>
                            <p class="text-sm">Hand-delivered with care.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. ACCOUNT VIEW -->
        <div id="view-account" class="view-section hidden flex-grow w-full max-w-2xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]">
            <div class="bg-white p-10 rounded-xl shadow-xl text-center">
                <!-- Display Mode -->
                <div id="account-display">
                    <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center text-3xl shadow-inner">👤</div>
                    <h2 id="display-name" class="font-rosarivo text-3xl mb-2">{{ Auth::user()->name ?? 'Guest User' }}</h2>
                    <p id="display-email" class="text-gray-500 mb-8">{{ Auth::user()->email ?? 'guest@example.com' }}</p>
                    
                    <div class="space-y-3 max-w-xs mx-auto">
                        <button onclick="app.account.toggleEdit()" class="block w-full py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-bold text-sm uppercase tracking-wide cursor-pointer">Edit Profile</button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full py-3 bg-[#4A4A3A] text-white rounded-lg hover:bg-[#3a3a2e] transition-colors font-bold text-sm uppercase tracking-wide shadow-md cursor-pointer">Log Out</button>
                        </form>
                    </div>
                </div>

                <!-- Edit Mode (Hidden by default) -->
                <div id="account-edit" class="hidden text-left">
                    <h3 class="font-rosarivo text-2xl mb-6 text-center">Edit Profile</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Name</label>
                            <input type="text" id="edit-name" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-3 focus:border-[#86A873] focus:ring focus:ring-[#86A873] focus:ring-opacity-50" value="{{ Auth::user()->name ?? '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                            <input type="email" id="edit-email" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-3 focus:border-[#86A873] focus:ring focus:ring-[#86A873] focus:ring-opacity-50" value="{{ Auth::user()->email ?? '' }}">
                        </div>
                         <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Phone</label>
                            <input type="text" id="edit-phone" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-3 focus:border-[#86A873] focus:ring focus:ring-[#86A873] focus:ring-opacity-50" placeholder="+63 900 000 0000">
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button onclick="app.account.toggleEdit()" class="flex-1 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600 font-bold text-sm uppercase transition-colors">Cancel</button>
                            <button onclick="app.account.save()" class="flex-1 py-3 bg-[#86A873] hover:bg-[#759465] text-white rounded-lg font-bold text-sm uppercase shadow-lg transition-colors">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple Footer -->
        <footer class="w-full p-4 text-center relative z-20 mt-auto bg-[#4A4A3A] text-white">
             <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-500/50 to-transparent mb-4"></div>
            <p class="text-xs text-gray-400 tracking-wide">&copy; 2025 FLORA FLEUR. ALL RIGHTS RESERVED.</p>
        </footer>
    </div>
</div>

@endsection