<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLORA FLEUR</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rosarivo&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;500;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'olive-dark': '#4a4a3a',
                        'green-card': 'rgba(40, 90, 70, 0.85)',
                        'input-bg': 'rgba(255, 255, 255, 0.2)',
                        'dashboard-bg': '#f3f4f6',
                        'dashboard-btn': '#86a873',
                        'page-bg-trans': 'rgba(255, 255, 255, 0.15)',
                        'page-border-trans': 'rgba(255, 255, 255, 0.2)',
                        'account-card-bg': 'rgba(255, 255, 255, 0.25)',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'rosarivo': ['Rosarivo', 'serif'],
                        'lato': ['Lato', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    @vite(['resources/css/customer.css', 'resources/js/customer.js'])

</head>
<body class="bg-gray-900 text-white">

    @yield('content')

    <div id="product-modal" class="app-container fixed inset-0 z-40 items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="relative w-full max-w-2xl bg-stone-200/95 rounded-2xl shadow-2xl flex flex-col md:flex-row gap-6 p-8" style="backdrop-filter: blur(10px);">
            
            <button data-modal-close class="absolute top-4 left-6 flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-black transition-colors z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                BACK TO MENU
            </button>

            <div class="w-full md:w-1/2 flex-shrink-0 pt-12 md:pt-0">
                <img id="modal-product-image" src="https://i.pinimg.com/736x/93/70/e3/9370e3f359041ff4e3ae470e15e8a4cd.jpg" alt="Product Image" class="w-full h-auto object-cover rounded-xl shadow-lg">
            </div>

            <div class="w-full md:w-1/2 flex flex-col text-gray-900 pt-12 md:pt-0">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Flower Description</h2>
                <h3 id="modal-product-title" class="font-rosarivo text-3xl font-medium mb-4">Classic White Bouquet</h3>
                <p id="modal-product-description" class="text-sm text-gray-700 mb-6 flex-grow">
                    An elegant arrangement of pure white roses and lilies, perfect for any occasion.
                </p>
                
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <span class="text-sm text-gray-600">PRICE</span>
                        <span id="modal-product-price" class="font-bold text-3xl ml-2">₱550</span>
                    </div>
                    
                    <div class="flex items-center justify-center gap-3">
                        <button class="quantity-btn quantity-btn-minus !bg-gray-300/50 !text-gray-800 !border-gray-400/50 hover:!bg-gray-300/80">-</button>
                        <span id="modal-quantity-value" class="quantity-value font-bold text-xl w-8 text-center text-gray-900">1</span>
                        <button class="quantity-btn quantity-btn-plus !bg-gray-300/50 !text-gray-800 !border-gray-400/50 hover:!bg-gray-300/80">+</button>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="flex-1 bg-gray-500 text-white text-sm font-bold py-3 px-4 rounded-lg shadow-lg hover:bg-gray-600 transition-all duration-300">
                        Add to Cart
                    </button>
                    <button class="flex-1 bg-dashboard-btn text-white text-sm font-bold py-3 px-4 rounded-lg shadow-lg hover:bg-opacity-90 transition-all duration-300 btn-checkout">
                        Check Out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="payment-modal" class="app-container fixed inset-0 z-50 items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="relative w-full max-w-xl bg-stone-200/95 rounded-2xl shadow-2xl flex flex-col gap-6 p-8" style="backdrop-filter: blur(10px);">
            
            <button data-modal-close class="absolute top-4 right-6 text-gray-700 hover:text-black transition-colors z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <div class="w-full flex flex-col text-gray-900">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Delivery Confirmation</h2>
                <h3 class="font-rosarivo text-3xl font-medium mb-6">Confirm Your Order</h3>
                
                <div class="mb-6">
                    <span class="text-sm text-gray-600">TOTAL</span>
                    <span id="payment-total-price" class="font-bold text-3xl ml-2">₱0</span>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Payment Method</h4>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button class="payment-btn flex-1 bg-gray-300/50 border border-gray-400/50 text-gray-800 text-sm font-bold py-3 px-4 rounded-lg hover:bg-gray-300/80 transition-all duration-300">
                            Cash On Delivery
                        </button>
                        <button class="payment-btn flex-1 bg-gray-300/50 border border-gray-400/50 text-gray-800 text-sm font-bold py-3 px-4 rounded-lg hover:bg-gray-300/80 transition-all duration-300">
                            E-Wallet
                        </button>
                        <button class="payment-btn flex-1 bg-gray-300/50 border border-gray-400/50 text-gray-800 text-sm font-bold py-3 px-4 rounded-lg hover:bg-gray-300/80 transition-all duration-300">
                            Others
                        </button>
                    </div>
                </div>
                
                <button class="btn-place-order w-full mt-8 bg-dashboard-btn text-white text-base font-bold py-3 px-4 rounded-lg shadow-lg hover:bg-opacity-90 transition-all duration-300">
                    PLACE ORDER
                </button>
            </div>
        </div>
    </div>

    <div id="thank-you-modal" class="app-container fixed inset-0 z-50 items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.5);">

    <div class="relative w-full max-w-lg bg-stone-200/95 rounded-2xl shadow-2xl flex flex-col items-center gap-6 p-8" style="backdrop-filter: blur(10px);">

        <button data-modal-close class="absolute top-4 right-6 text-gray-700 hover:text-black transition-colors z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <div class="w-full flex flex-col text-gray-900 text-center">
            <h2 class="font-rosarivo text-4xl font-medium mb-4">Thank you for your Order!</h2>
            <p class="text-gray-700 mb-8">
                You just made our business grow<br>and for that we are GRATEFUL!
            </p>
            <button class="btn-back-main w-full max-w-xs mx-auto bg-dashboard-btn text-white text-base font-bold py-3 px-4 rounded-lg shadow-lg hover:bg-opacity-90 transition-all duration-300">
                Back to Main Menu
            </button>
        </div>
    </div>
</div>

    <div id="order-toast" class="fixed top-6 right-6 bg-green-600 text-white py-3 px-5 rounded-lg shadow-lg transform translate-x-[120%] transition-transform duration-300 ease-in-out z-50">
        <span class="font-semibold">Order Placed!</span>
        <p class="text-sm">Your order has been confirmed.</p>
    </div>

</body>
</html>