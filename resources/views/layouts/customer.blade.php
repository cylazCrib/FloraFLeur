<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLORA FLEUR</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Rosarivo (Serif) for Headings/Logo, Lato/Inter (Sans) for body -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=Rosarivo:ital@0;1&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'olive-dark': '#4A4A3A',
                        'dashboard-btn': '#86A873',
                    },
                    fontFamily: {
                        'sans': ['Lato', 'Inter', 'sans-serif'],
                        'rosarivo': ['Rosarivo', 'serif'],
                        'lato': ['Lato', 'sans-serif'],
                        'inter': ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS/JS -->
    @vite(['resources/css/customer.css', 'resources/js/customer.js'])

</head>
<body class="bg-gray-900 text-white antialiased">

    @yield('content')

    <!-- --- MODALS (Keep existing modal structure for functionality) --- -->

    <!-- Product Modal -->
    <div id="product-modal" class="app-container fixed inset-0 z-50 items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.6); display: none;">
        <div class="relative w-full max-w-4xl bg-[#F5F5F0] rounded-xl shadow-2xl flex flex-col md:flex-row overflow-hidden animate-fade-in">
            
            <!-- Updated Back Button Style -->
            <button data-modal-close class="absolute top-6 left-6 flex items-center gap-2 text-xs font-bold text-[#4A4A3A] hover:text-white hover:bg-[#4A4A3A] transition-all z-20 uppercase tracking-wide bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full shadow-md border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                BACK TO MENU
            </button>

            <div class="w-full md:w-1/2 h-64 md:h-auto relative">
                <img id="modal-product-image" src="" alt="Product" class="absolute inset-0 w-full h-full object-cover">
            </div>

            <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center text-gray-800 pt-16"> <!-- Added pt-16 for space -->
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Flower Description</h2>
                <h3 id="modal-product-title" class="font-rosarivo text-3xl md:text-4xl mb-4 text-[#4A4A3A]">Product Name</h3>
                <p id="modal-product-description" class="text-sm text-gray-600 leading-relaxed mb-8">
                    Description goes here.
                </p>
                
                <div class="flex items-center justify-between mb-8 border-t border-gray-200 pt-6">
                    <div>
                        <span class="text-xs text-gray-400 font-bold tracking-wider block mb-1">PRICE</span>
                        <span id="modal-product-price" class="font-rosarivo text-2xl text-[#4A4A3A]">₱0</span>
                    </div>
                    
                    <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-full border border-gray-200">
                        <button class="quantity-btn quantity-btn-minus text-gray-500 hover:text-[#4A4A3A] font-bold text-lg">-</button>
                        <span id="modal-quantity-value" class="font-lato font-bold text-lg w-6 text-center text-gray-800">1</span>
                        <button class="quantity-btn quantity-btn-plus text-gray-500 hover:text-[#4A4A3A] font-bold text-lg">+</button>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-bold py-3.5 rounded-lg transition-colors tracking-wide uppercase">
                        Add to Cart
                    </button>
                    <button class="flex-1 bg-[#86A873] hover:bg-[#759465] text-white text-sm font-bold py-3.5 rounded-lg shadow-lg transition-colors tracking-wide uppercase btn-checkout">
                        Check Out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="app-container fixed inset-0 z-50 items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.6); display: none;">
        <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl p-8 text-gray-800 animate-fade-in">
            
            <button data-modal-close class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Delivery Confirmation</h2>
            <h3 class="font-rosarivo text-3xl text-[#4A4A3A] mb-6">Confirm Order</h3>
            
            <div class="bg-gray-50 p-4 rounded-lg mb-6 flex justify-between items-center border border-gray-100">
                <span class="text-sm font-bold text-gray-500 tracking-wider">TOTAL AMOUNT</span>
                <span id="payment-total-price" class="font-rosarivo text-2xl text-[#4A4A3A]">₱0</span>
            </div>

            <div class="mb-8">
                <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Payment Method</h4>
                <div class="grid grid-cols-3 gap-3">
                    <button class="payment-btn py-3 px-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:border-[#86A873] hover:text-[#86A873] transition-all">
                        Cash On Delivery
                    </button>
                    <button class="payment-btn py-3 px-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:border-[#86A873] hover:text-[#86A873] transition-all">
                        E-Wallet
                    </button>
                    <button class="payment-btn py-3 px-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:border-[#86A873] hover:text-[#86A873] transition-all">
                        Credit Card
                    </button>
                </div>
            </div>
            
            <button class="btn-place-order w-full bg-[#86A873] hover:bg-[#759465] text-white text-sm font-bold py-4 rounded-lg shadow-lg transition-all tracking-widest uppercase">
                Place Order
            </button>
        </div>
    </div>

    <!-- Thank You Modal -->
    <div id="thank-you-modal" class="app-container fixed inset-0 z-50 items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.6); display: none;">
        <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl p-10 text-center animate-fade-in">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            
            <h2 class="font-rosarivo text-3xl text-[#4A4A3A] mb-3">Thank You!</h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-8">
                Your order has been placed successfully.<br>We are preparing your flowers with love.
            </p>
            
            <button class="btn-back-main w-full bg-[#4A4A3A] hover:bg-[#3a3a2e] text-white text-sm font-bold py-3 rounded-lg transition-colors tracking-wide uppercase">
                Back to Dashboard
            </button>
        </div>
    </div>

    <!-- Toast -->
    <div id="order-toast" class="fixed top-6 right-6 bg-[#4A4A3A] text-white py-3 px-6 rounded-lg shadow-xl transform translate-x-[120%] transition-transform duration-300 ease-in-out z-[60] flex items-center gap-3">
        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
        <div>
            <span class="font-bold text-sm block">Success</span>
            <p class="text-xs text-gray-300">Operation completed successfully.</p>
        </div>
    </div>

</body>
</html>