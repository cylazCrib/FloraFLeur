@extends('layouts.customer')

@section('content')

<div id="app-container-dashboard" class="app-container active relative min-h-screen w-full flex-col font-sans">
    
    <!-- Background Image -->
    <div class="absolute inset-0 w-full h-full z-0">
        <img src="{{ asset('images/image_7f3485.jpg') }}" 
             alt="Background" 
             class="w-full h-full object-cover opacity-100">
        <!-- Dark Overlay to match the screenshot contrast -->
        <div class="absolute inset-0 bg-black/40 z-10"></div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="relative z-20 flex flex-col min-h-screen w-full text-white">
        
        <!-- Top Navigation Bar -->
        <header class="w-full bg-[#4A4A3A] shadow-md">
            <nav class="max-w-[1920px] mx-auto px-6 h-20 flex justify-between items-center">
                
                <!-- Left: Logo -->
                <div class="flex items-center gap-3">
                    <a href="#" data-page="dashboard" class="flex items-center gap-2 group">
                        <span class="font-rosarivo text-2xl tracking-widest text-white group-hover:text-gray-200 transition-colors">FLORA</span>
                        <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="w-8 h-8 object-contain">
                        <span class="font-rosarivo text-2xl tracking-widest text-white group-hover:text-gray-200 transition-colors">FLEUR</span>
                    </a>
                </div>

                <!-- Center: Navigation Links -->
                <div class="hidden xl:flex items-center space-x-10">
                    
                    <!-- Occasions Dropdown -->
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide hover:text-gray-300 transition-colors uppercase">
                            OCCASIONS
                            <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="absolute top-20 left-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top">
                            <a href="#" data-page="valentines" class="block px-6 py-3 text-sm hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30">Valentine's</a>
                            <a href="#" data-page="wedding" class="block px-6 py-3 text-sm hover:bg-[#5c5c48] transition-colors">Wedding</a>
                        </div>
                    </div>

                    <!-- Types Dropdown -->
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide hover:text-gray-300 transition-colors uppercase">
                            TYPES
                            <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute top-20 left-0 w-56 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top">
                            <a href="#" data-page="basketflowers" class="block px-6 py-3 text-sm hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30">Basket Flowers</a>
                            <a href="#" data-page="bouquetflowers" class="block px-6 py-3 text-sm hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30">Bouquet Flowers</a>
                            <a href="#" data-page="standeeflowers" class="block px-6 py-3 text-sm hover:bg-[#5c5c48] transition-colors">Standee Flowers</a>
                        </div>
                    </div>

                    <!-- Single Links -->
                    <a href="#" data-page="account" class="h-20 flex items-center font-lato text-[15px] font-medium tracking-wide hover:text-gray-300 transition-colors uppercase">ACCOUNT</a>
                    <a href="#" data-page="aboutus" class="h-20 flex items-center font-lato text-[15px] font-medium tracking-wide hover:text-gray-300 transition-colors uppercase">ABOUT US</a>
                    
                    <!-- My Purchases Dropdown -->
                    <div class="relative group h-20 flex items-center">
                        <button class="flex items-center gap-1 font-lato text-[15px] font-medium tracking-wide hover:text-gray-300 transition-colors uppercase">
                            MY PURCHASES
                            <svg class="w-3 h-3 ml-0.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute top-20 right-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top">
                            <a href="#" data-page="toship" class="block px-6 py-3 text-sm hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30">To Ship</a>
                            <a href="#" data-page="completed" class="block px-6 py-3 text-sm hover:bg-[#5c5c48] transition-colors border-b border-gray-600/30">Completed</a>
                            <a href="#" data-page="refund" class="block px-6 py-3 text-sm hover:bg-[#5c5c48] transition-colors">Return/Refund</a>
                        </div>
                    </div>
                </div>

                <!-- Right: Icons & Button -->
                <div class="flex items-center gap-8">
                    <!-- Cart -->
                    <a href="#" data-page="cart" class="flex flex-col items-center justify-center group relative">
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-gray-300 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="absolute -top-2 -right-2 bg-[#E65100] text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border border-[#4A4A3A]">2</span>
                        </div>
                        <span class="text-[10px] font-lato uppercase mt-1 tracking-wider group-hover:text-gray-300 transition-colors">Cart</span>
                    </a>

                    <!-- Favorite -->
                    <a href="#" data-page="favorite" class="flex flex-col items-center justify-center group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:text-gray-300 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="text-[10px] font-lato uppercase mt-1 tracking-wider group-hover:text-gray-300 transition-colors">Favorite</span>
                    </a>

                    <!-- Back Button -->
                    <a href="{{ route('landing') }}" class="ml-2 bg-[#6B6B61] hover:bg-[#7D7D73] text-white px-5 py-2 rounded text-xs font-medium tracking-wide transition-colors shadow-sm">
                        Back to Landing
                    </a>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <main id="page-dashboard" class="page-content active flex-grow flex flex-col justify-center px-4 lg:px-8 py-12 w-full max-w-[1400px] mx-auto">
            
            <!-- Header Text -->
            <div class="text-center mb-14 animate-fade-in mt-4">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-normal leading-tight drop-shadow-lg">
                    <span class="block mb-2 font-light tracking-wide text-gray-200">Hey there,</span>
                    <span class="font-medium tracking-wide">Welcome to Flora Fleur, where flowers are more</span>
                    <span class="block mt-2 font-light tracking-wide text-gray-200">than gifts—they're experiences.</span>
                </h1>
            </div>

            <!-- Image Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 w-full">
                <!-- Card 1 -->
                <div class="group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="https://i.pinimg.com/736x/20/d9/75/20d975ae5d4f33d0caed1a0dad56b4a1.jpg" 
                         alt="Flower Bouquet" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white font-rosarivo text-lg tracking-wider"></p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer transform hover:-translate-y-2 transition-transform duration-300 mt-0 lg:mt-8">
                    <img src="https://i.pinimg.com/1200x/43/b3/4f/43b34f35e4fe86163f3a80e32213212c.jpg" 
                         alt="Grand Opening" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white font-rosarivo text-lg tracking-wider"></p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer transform hover:-translate-y-2 transition-transform duration-300">
                    <img src="https://i.pinimg.com/736x/0e/24/00/0e2400b844843a0164813e65ced223d8.jpg" 
                         alt="Potted Plants" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-white font-rosarivo text-lg tracking-wider"></p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer transform hover:-translate-y-2 transition-transform duration-300 mt-0 lg:mt-8">
                    <img src="https://i.pinimg.com/736x/21/fb/4a/21fb4a20f8debc581e2dcb467f630f46.jpg" 
                         alt="Flower Box" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                    </div>
                </div>
            </div>

            <!-- Buy Now Button -->
            <div class="flex justify-end w-full mt-4">
                <button data-page="bouquetflowers" class="bg-[#86A873] hover:bg-[#759465] text-white text-sm font-bold py-3 px-10 rounded-lg shadow-lg transition-all duration-300 tracking-wider transform hover:scale-105">
                    Buy Now
                </button>
            </div>
        </main>

        <!-- Simple Footer -->
        <footer class="w-full p-4 text-center relative z-20 mt-auto">
             <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-500/30 to-transparent mb-4"></div>
            <p class="text-xs text-gray-400 tracking-wide">&copy; 2025 FLORA FLEUR. ALL RIGHTS RESERVED.</p>
        </footer>
    </div>
</div>

@endsection