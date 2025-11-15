@extends('layouts.customer')

@section('content')

<div id="app-container-dashboard" class="app-container active relative min-h-screen w-full flex-col">
    
    <div class="absolute inset-0 w-full h-full z-0">
        <img src="{{ asset('images/image_7f3485.jpg') }}" 
             alt="Flower arrangement background" 
             class="w-full h-full object-cover opacity-75"
             onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-gray-800 -z-10"></div>
        <div class="absolute inset-0 w-full h-full bg-black/40 z-10"></div>
    </div>

    <div class="relative z-20 flex flex-col min-h-screen w-full text-white">
        
        <header class="w-full">
            <nav class="flex justify-between items-center p-6 bg-olive-dark/90 text-white">
                <a href="#" data-page="dashboard" class="text-2xl tracking-wider flex items-center gap-2 font-rosarivo">
                    <span>FLORA</span>
                    <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="w-7 h-7 object-contain">
                    <span>FLEUR</span>
                </a>
                
                <div class="hidden md:flex items-center space-x-8 tracking-wider">
                    
                    <div class="relative group">
                        <span class="font-lato text-[19px] font-normal hover:text-gray-300 transition-colors cursor-pointer flex items-center">
                            OCCASIONS
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                        <div class="absolute hidden group-hover:block bg-olive-dark/95 pt-2 rounded-md shadow-lg z-50 min-w-[160px]">
                            <a href="#" data-page="valentines" class="block px-6 py-3 text-sm text-white/90 hover:bg-dashboard-btn transition-colors rounded-t-md">Valentine's</a>
                            <a href="#" data-page="wedding" class="block px-6 py-3 text-sm text-white/90 hover:bg-dashboard-btn transition-colors rounded-b-md">Wedding</a>
                        </div>
                    </div>

                    <div class="relative group">
                        <span class="font-lato text-[19px] font-normal hover:text-gray-300 transition-colors cursor-pointer flex items-center">
                            TYPES
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                        <div class="absolute hidden group-hover:block bg-olive-dark/95 pt-2 rounded-md shadow-lg z-50 min-w-[160px]">
                            <a href="#" data-page="basketflowers" class="block px-6 py-3 text-sm text-white/90 hover:bg-dashboard-btn transition-colors rounded-t-md">Basket Flowers</a>
                            <a href="#" data-page="bouquetflowers" class="block px-6 py-3 text-sm text-white/90 hover:bg-dashboard-btn transition-colors">Bouquet Flowers</a>
                            <a href="#" data-page="standeeflowers" class="block px-6 py-3 text-sm text-white/90 hover:bg-dashboard-btn transition-colors rounded-b-md">Standee Flowers</a>
                        </div>
                    </div>

                    <a href="#" data-page="account" class="font-lato text-[19px] font-normal hover:text-gray-300 transition-colors">ACCOUNT</a>
                    <a href="#" data-page="aboutus" class="font-lato text-[19px] font-normal hover:text-gray-300 transition-colors">ABOUT US</a>
                    
                    <div class="relative group">
                        <span class="font-lato text-[19px] font-normal hover:text-gray-300 transition-colors cursor-pointer flex items-center">
                            MY PURCHASES
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                        <div class="absolute hidden group-hover:block bg-olive-dark/95 pt-2 rounded-md shadow-lg z-50 min-w-[160px]">
                            <a href="#" data-page="toship" class="block px-6 py-3 text-sm text-white/90 hover:bg-dashboard-btn transition-colors rounded-t-md">To Ship</a>
                            <a href="#" data-page="completed" class="block px-6 py-3 text-sm text-white/90 hover:bg-dashboard-btn transition-colors">Completed</a>
                            <a href="#" data-page="refund" class="block px-6 py-3 text-sm text-white/90 hover:bg-dashboard-btn transition-colors rounded-b-md">Return/Refund</a>
                        </div>
                    </div>
                    
                    <a href="#" data-page="cart" class="relative hover:text-gray-300 flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span class="absolute -top-2 right-2 text-xs bg-red-500 text-white rounded-full h-4 w-4 flex items-center justify-center">2</span>
                        <span class="text-xs font-sans mt-1">Cart</span>
                    </a>
                    <a href="#" data-page="favorite" class="hover:text-gray-300 flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        <span class="text-xs font-sans mt-1">Favorite</span>
                    </a>

                    <a href="{{ route('landing') }}" class="bg-white/20 px-3 py-1 rounded-md hover:bg-white/30 transition-colors font-sans text-sm font-medium">Back to Landing</a>
                </div>

                <div class="flex items-center gap-4 md:hidden">
                    <a href="#" data-page="cart" class="relative hover:text-gray-300 flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span class="absolute -top-2 right-2 text-xs bg-red-500 text-white rounded-full h-4 w-4 flex items-center justify-center">2</span>
                        <span class="text-xs font-sans mt-1">Cart</span>
                    </a>
                    <a href="#" data-page="favorite" class="hover:text-gray-300 flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        <span class="text-xs font-sans mt-1">Favorite</span>
                    </a>
                </div>
            </nav>
        </header>

        <main id="page-dashboard" class="page-content active flex-grow flex-col justify-center items-center px-4 py-12">
            <div class="w-full max-w-7xl mx-auto">
                <h1 class="text-3xl font-light text-center text-gray-200 mb-12">
                    Hey there,<br>
                    <span class="font-medium text-white">Welcome to Flora Fleur, where flowers are more</span><br>
                    than gifts—they're experiences.
                </h1>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                    <img src="https://i.pinimg.com/736x/20/d9/75/20d975ae5d4f33d0caed1a0dad56b4a1.jpg" alt="Bouquet" class="rounded-lg shadow-md object-cover w-full h-full aspect-[2/3]">
                    <img src="https://i.pinimg.com/736x/43/b3/4f/43b34f35e4fe86163f/43b34f35e4fe86163f3a80e32213212c.jpg" alt="Grand Opening" class="rounded-lg shadow-md object-cover w-full h-full aspect-[2/3]">
                    <img src="https://i.pinimg.com/736x/0e/24/00/0e2400b844843a0164813e65ced223d8.jpg" alt="Monstera plant" class="rounded-lg shadow-md object-cover w-full h-full aspect-[2/3]">
                    <img src="https://i.pinimg.com/736x/21/fb/4a/21fb4a20f8debc581e2dcb467f630f46.jpg" alt="Flower box" class="rounded-lg shadow-md object-cover w-full h-full aspect-[2/3]">
                </div>

                <div class="text-right">
                    <button data-page="bouquetflowers" class="bg-dashboard-btn text-white font-bold py-3 px-10 rounded-lg shadow-lg hover:bg-opacity-90 transition-all duration-300">
                        Buy Now
                    </button>
                </div>
            </div>
        </main>

        <footer class="w-full p-4 text-center text-xs text-gray-400 relative z-20">
            <p>&copy; 2025 Flora Fleur. All rights reserved.</p>
        </footer>
    </div>
</div>

@endsection