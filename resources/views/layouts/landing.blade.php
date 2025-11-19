<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flora Fleur - Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])

</head>
<body class="text-white scroll-smooth">

    <div class="min-h-screen flex flex-col">
        <header class="nav-bg shadow-lg sticky top-0 z-40">
            <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-1 text-2xl font-serif font-bold tracking-wider">
                    <span>FLORA</span>
                    <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="logo-icon w-7 h-7">
                    <span>FLEUR</span>
                </div>
                <div class="hidden md:flex items-center space-x-8 text-sm tracking-widest">
                    <a href="#features" class="hover:text-gray-300 transition duration-300">FEATURES</a>
                    <a href="#how-it-works" class="hover:text-gray-300 transition duration-300">HOW IT WORKS</a>
                    <a href="#bestsellers" class="hover:text-gray-300 transition duration-300">BESTSELLERS</a>
                    <a href="#reviews" class="hover:text-gray-300 transition duration-300">REVIEWS</a>
                    <a href="#about" class="hover:text-gray-300 transition duration-300">ABOUT US</a>
                </div>
                <div>
                    <button id="register-shop-btn" class="hidden md:block bg-white text-gray-800 text-xs font-semibold py-2 px-4 rounded-full hover:bg-gray-200 transition duration-300">
                        Register as Shop
                    </button>
                    <button id="mobile-menu-btn" class="md:hidden text-xl">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </nav>
            
            <div id="mobile-menu" class="md:hidden bg-gray-800 py-4 px-6 hidden">
                <div class="flex flex-col space-y-4">
                    <a href="#features" class="hover:text-gray-300 transition duration-300">FEATURES</a>
                    <a href="#how-it-works" class="hover:text-gray-300 transition duration-300">HOW IT WORKS</a>
                    <a href="#bestsellers" class="hover:text-gray-300 transition duration-300">BESTSELLERS</a>
                    <a href="#reviews" class="hover:text-gray-300 transition duration-300">REVIEWS</a>
                    <a href="#about" class="hover:text-gray-300 transition duration-300">ABOUT US</a>
                    <button id="mobile-register-btn" class="bg-white text-gray-800 text-xs font-semibold py-2 px-4 rounded-full hover:bg-gray-200 transition duration-300 w-40">
                        Register as Shop
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            @yield('content')
        </main>

        <footer class="bg-gray-900 py-8">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center gap-1 text-xl font-serif font-bold mb-4 md:mb-0">
                        <span>FLORA</span>
                        <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="logo-icon w-5 h-5">
                        <span>FLEUR</span>
                    </div>
                    <div class="flex space-x-6 mb-4 md:mb-0">
                        <a href="#" class="hover:text-gray-300 transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-gray-300 transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover:text-gray-300 transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-gray-300 transition"><i class="fab fa-pinterest"></i></a>
                    </div>
                    <div class="text-sm text-gray-400">
                        &copy; 2023 Flora Fleur. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <div class="fixed bottom-10 left-10 p-6 rounded-lg bottom-left-bg space-y-4 z-30 animate-slide-in">
        <a href="#" id="signup-store-btn" class="block w-full text-left font-semibold hover:text-gray-300 transition">
            Sign Up As New Store
        </a>
        <a href="#" id="admin-login-btn" class="block w-full text-left font-semibold hover:text-gray-300 transition">
            LOG IN AS ADMIN
        </a>
    </div>

    <div id="signin-modal" class="fixed inset-0 modal-overlay z-50 hidden items-center justify-center p-4">
         <div class="w-full max-w-md p-8 md:p-12 rounded-2xl form-container-bg relative animate-fade-in">
            <button class="absolute top-4 right-4 text-2xl text-white hover:text-gray-300 transition" data-close-modal>&times;</button>
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-wider text-white mb-2">SIGN IN</h1>
                <div class="flex items-center justify-center gap-1 text-lg font-serif tracking-wider text-white">
                    <span>FLORA</span>
                    <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="logo-icon w-5 h-5">
                    <span>FLEUR</span>
                </div>
            </div>

            <form class="mt-10 space-y-8" id="signin-form" method="POST" action="/login">
                @csrf
                <div id="signin-errors" class="text-red-400 text-sm hidden"></div>
                <div class="relative">
                    <label for="signin-email" class="text-sm font-medium text-gray-200">Email</label>
                    <input id="signin-email" name="email" type="email" autocomplete="email" required class="w-full form-input-style">
                    <i class="fa-regular fa-envelope absolute right-2 top-8 text-gray-400"></i>
                </div>
                <div class="relative">
                    <label for="signin-password" class="text-sm font-medium text-gray-200">Password</label>
                    <input id="signin-password" name="password" type="password" autocomplete="current-password" required class="w-full form-input-style">
                    <i class="fa-solid fa-lock absolute right-2 top-8 text-gray-400"></i>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 bg-transparent">
                        <label for="remember-me" class="ml-2 block text-gray-300">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" id="forgot-password" class="font-medium text-gray-300 hover:text-white transition">Forgot Password?</a>
                </div>
                <div class="flex items-center text-sm">
                    <input id="login-as-vendor" name="login_as_vendor" type="checkbox" class="h-4 w-4 rounded border-gray-300 bg-transparent">
                    <label for="login-as-vendor" class="ml-2 block text-gray-300">Log in as Vendor</label>
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white btn-maroon hover:opacity-90 transition transform hover:scale-105">
                        LOG IN
                    </button>
                </div>
                <div class="text-center text-sm space-y-2">
                    <span class="text-gray-300">or</span>
                    <div><a href="#" id="go-to-signup" class="font-medium text-white hover:underline transition">Sign up here</a></div>
                </div>
            </form>
        </div>
    </div>
    
    <div id="signup-modal" class="fixed inset-0 modal-overlay z-50 hidden items-center justify-center p-4">
         <div class="w-full max-w-md p-8 md:p-12 rounded-2xl form-container-bg relative animate-fade-in">
            <button class="absolute top-4 right-4 text-2xl text-white hover:text-gray-300 transition" data-close-modal>&times;</button>
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-wider text-white mb-2">SIGN UP</h1>
                <div class="flex items-center justify-center gap-1 text-lg font-serif tracking-wider text-white">
                    <span>FLORA</span>
                    <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="logo-icon w-5 h-5">
                    <span>FLEUR</span>
                </div>
            </div>
            <form class="mt-10 space-y-8" id="signup-form" method="POST" action="/register">
                @csrf
                <div id="signup-errors" class="text-red-400 text-sm hidden"></div>
                 <div class="relative">
                     <label for="signup-name" class="text-sm font-medium text-gray-200">Name</label>
                    <input id="signup-name" name="name" type="text" autocomplete="name" required class="w-full form-input-style">
                </div>
                 <div class="relative">
                    <label for="signup-email" class="text-sm font-medium text-gray-200">Email</label>
                    <input id="signup-email" name="email" type="email" autocomplete="email" required class="w-full form-input-style">
                    <i class="fa-regular fa-envelope absolute right-2 top-8 text-gray-400"></i>
                </div>
                <div class="relative">
                    <label for="signup-password" class="text-sm font-medium text-gray-200">Password</label>
                    <input id="signup-password" name="password" type="password" autocomplete="new-password" required class="w-full form-input-style">
                    <i class="fa-solid fa-lock absolute right-2 top-8 text-gray-400"></i>
                </div>
                <div class="relative">
                    <label for="signup-password-confirm" class="text-sm font-medium text-gray-200">Confirm Password</label>
                    <input id="signup-password-confirm" name="password_confirmation" type="password" autocomplete="new-password" required class="w-full form-input-style">
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white btn-maroon hover:opacity-90 transition transform hover:scale-105">
                        SIGN UP
                    </button>
                </div>
                <div class="text-center text-sm space-y-2">
                    <span class="text-gray-300">Already have an account?</span>
                    <div><a href="#" id="go-to-signin" class="font-medium text-white hover:underline transition">Log in here</a></div>
                </div>
            </form>
        </div>
    </div>
    
    <div id="admin-login-modal" class="fixed inset-0 modal-overlay z-50 hidden items-center justify-center p-4">
         <div class="w-full max-w-md p-8 md:p-12 rounded-2xl form-container-bg relative animate-fade-in">
            <button class="absolute top-4 right-4 text-2xl text-white hover:text-gray-300 transition" data-close-modal>&times;</button>
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-wider text-white mb-2">ADMIN LOGIN</h1>
                <div class="flex items-center justify-center gap-1 text-lg font-serif tracking-wider text-white">
                    <span>FLORA</span>
                    <img src="{{ asset('images/image_8bd93d.png') }}" alt="logo" class="logo-icon w-5 h-5">
                    <span>FLEUR</span>
                </div>
            </div>
            <form class="mt-10 space-y-8" id="admin-login-form" method="POST" action="/login">
                @csrf
                <div id="admin-errors" class="text-red-400 text-sm hidden"></div>
                <div class="relative">
                    <label for="admin-email" class="text-sm font-medium text-gray-200">Admin Email</label>
                    <input id="admin-email" name="email" type="email" autocomplete="email" required class="w-full form-input-style">
                    <i class="fa-regular fa-envelope absolute right-2 top-8 text-gray-400"></i>
                </div>
                <div class="relative">
                    <label for="admin-password" class="text-sm font-medium text-gray-200">Password</label>
                    <input id="admin-password" name="password" type="password" autocomplete="current-password" required class="w-full form-input-style">
                    <i class="fa-solid fa-lock absolute right-2 top-8 text-gray-400"></i>
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full shadow-sm text-sm font-bold text-white btn-maroon hover:opacity-90 transition transform hover:scale-105">
                        LOG IN
                    </button>
                </div>
                 <div class="text-center text-sm">
                    <a href="#" id="back-to-user-login" class="font-medium text-white hover:underline transition">Back to user login</a>
                </div>
            </form>
        </div>
    </div>
    
    <div id="signup-store-modal" class="fixed inset-0 modal-overlay z-50 hidden items-center justify-center p-4">
        <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto p-8 md:p-12 rounded-2xl form-container-bg relative text-white animate-fade-in">
            <button class="absolute top-4 right-4 text-2xl text-white hover:text-gray-300 transition" data-close-modal>&times;</button>
            <h1 class="text-5xl font-serif font-bold text-center mb-2">Create Your Shop</h1>
            <p class="text-center text-gray-300 mb-8">Just fill out a quick form to get started!</p>
            
           <form id="store-signup-form" data-action="{{ route('shop.register') }}" enctype="multipart/form-data">
                @csrf
                <div id="store-signup-errors" class="mb-4 text-red-400 text-sm hidden">
                    <ul id="store-signup-error-list"></ul>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-lg font-semibold mb-3">Shop Owner Information:</h2>
                            <div class="space-y-4">
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Name</label>
                                    <input type="text" name="owner_name" class="w-full form-input-style" placeholder="Full name">
                                </div>
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Email</label>
                                    <input type="email" name="email" class="w-full form-input-style" placeholder="Email address">
                                </div>
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Password</label>
                                    <input type="password" name="password" class="w-full form-input-style" placeholder="Min. 8 characters">
                                </div>
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="w-full form-input-style" placeholder="Re-type password">
                                </div>
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Phone Number</label>
                                    <input type="tel" name="owner_phone" class="w-full form-input-style" placeholder="Phone number">
                                </div>
                            </div>
                        </div>
                         <div>
                            <h2 class="text-lg font-semibold mb-3">Shop Details:</h2>
                            <div class="space-y-4">
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Shop Name</label>
                                    <input type="text" name="shop_name" class="w-full form-input-style" placeholder="Your shop name">
                                </div>
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Shop Description</label>
                                    <textarea name="shop_description" class="w-full form-input-style h-24" placeholder="Describe your shop"></textarea>
                                </div>
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Shop Phone/Tel. Number</label>
                                    <input type="tel" name="shop_phone" class="w-full form-input-style" placeholder="Shop contact number">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-lg font-semibold mb-3">Business Address:</h2>
                            <div class="space-y-4">
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Address</label>
                                    <input type="text" name="address" class="w-full form-input-style" placeholder="Street address">
                                </div>
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Zip Code</label>
                                    <input type="text" name="zip_code" class="w-full form-input-style" placeholder="Zip code">
                                </div>
                                <div class="relative">
                                    <label class="text-sm font-medium text-gray-200">Delivery Coverage Area</label>
                                    <input type="text" name="delivery_coverage" class="w-full form-input-style" placeholder="Areas you deliver to">
                                </div>
                            </div>
                        </div>
                         <div>
                            <h2 class="text-lg font-semibold mb-3">Business Verification:</h2>
                            <div class="relative">
                                 <label for="file-upload" class="form-input-style w-full flex items-center justify-between cursor-pointer text-gray-300 py-3 px-0 border-b border-gray-400">
                                    <span id="file-label-text">DTI/Business Permit (PDF, JPG, PNG)</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                </label>
                                <input id="file-upload" name="permit_file" type="file" class="sr-only">
                            </div>
                        </div>
                    </div>
                     <div class="md:col-span-2 flex justify-end mt-6">
                         <button type="submit" class="btn-dark text-white font-bold py-3 px-10 rounded-xl transition transform hover:scale-105 flex items-center gap-2">
                            <i class="fas fa-store"></i> Create My Shop
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="fixed bottom-5 right-5 bg-green-700 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden transition-all duration-300">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="toast-message">Action completed successfully!</span>
        </div>
    </div>

</body>
</html>