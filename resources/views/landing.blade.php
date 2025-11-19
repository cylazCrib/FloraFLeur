@extends('layouts.landing')

@section('content')
    
<section class="py-16 md:py-24 flex items-center">
    <div class="container mx-auto px-6 flex justify-end">
        <div class="w-full md:w-1/2 lg:w-2/5 text-center md:text-left animate-fade-in">
            <h1 class="text-6xl lg:text-7xl font-serif font-bold tracking-wide hero-text-shadow">FLORA FLEUR</h1>
            <p class="font-serif text-2xl lg:text-3xl mt-4 hero-text-shadow">
                Refresh your space with lush greenery and exquisite flower bouquets
            </p>
            <p class="text-sm mt-4 leading-relaxed max-w-md mx-auto md:mx-0">
                Create the perfect indoor jungle with our bold houseplants, blooming plants, hanging plants, and more! We connect you with the best local florists and plant shops.
            </p>
            
            <div class="mt-8 space-y-4 flex flex-col items-center md:items-start">
                <button id="login-btn" class="btn-gray font-bold py-3 px-10 rounded-full w-48 transition transform hover:scale-105 flex items-center justify-center gap-2">
                    Log In
                </button>
                <span class="text-sm">or New here?</span>
                <button id="signup-btn" class="btn-green font-bold py-3 px-10 rounded-full w-48 transition transform hover:scale-105 flex items-center justify-center gap-2">
                    Sign Up
                </button>
            </div>

        </div>
    </div>
</section>

<section id="features" class="py-16 bg-black bg-opacity-20">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-serif font-bold text-center mb-12">Why Choose Flora Fleur?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="feature-card p-6 rounded-lg text-center">
                <div class="text-4xl mb-4 text-green-300">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Fresh & Local</h3>
                <p class="text-gray-300">We partner with local growers and florists to bring you the freshest plants and flowers available.</p>
            </div>
            <div class="feature-card p-6 rounded-lg text-center">
                <div class="text-4xl mb-4 text-green-300">
                    <i class="fas fa-truck"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Fast Delivery</h3>
                <p class="text-gray-300">Get your plants and flowers delivered quickly with our reliable delivery network.</p>
            </div>
            <div class="feature-card p-6 rounded-lg text-center">
                <div class="text-4xl mb-4 text-green-300">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Quality Guarantee</h3>
                <p class="text-gray-300">We stand behind our products with a satisfaction guarantee on all purchases.</p>
            </div>
        </div>
    </div>
</section>

<section id="how-it-works" class="py-16">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-serif font-bold text-center mb-12">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="bg-green-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold">1</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Browse</h3>
                <p class="text-gray-300">Explore our wide selection of plants and flowers from local shops.</p>
            </div>
            <div class="text-center">
                <div class="bg-green-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold">2</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Select</h3>
                <p class="text-gray-300">Choose your favorites and add them to your cart.</p>
            </div>
            <div class="text-center">
                <div class="bg-green-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold">3</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Checkout</h3>
                <p class="text-gray-300">Complete your purchase with our secure checkout process.</p>
            </div>
            <div class="text-center">
                <div class="bg-green-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold">4</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Enjoy</h3>
                <p class="text-gray-300">Receive your order and enjoy the beauty of nature in your space.</p>
            </div>
        </div>
    </div>
</section>

<section id="bestsellers" class="py-16 bg-black bg-opacity-20">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-serif font-bold text-center mb-12">Customer Favorites</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1485955900006-10f4d324d411?q=80&w=2070" alt="Monstera Plant" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold mb-2">Monstera Deliciosa</h3>
                    <p class="text-gray-300 mb-4">The iconic Swiss cheese plant with beautiful split leaves.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold">₱1,200</span>
                        <button class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1563241527-3004b7be0ffd?q=80&w=2070" alt="Rose Bouquet" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold mb-2">Premium Rose Bouquet</h3>
                    <p class="text-gray-300 mb-4">A stunning arrangement of fresh red roses perfect for any occasion.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold">₱1,800</span>
                        <button class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden">
                <img src="https://images.unsplash.com/photo-1598880940086-4b62797e8e8c?q=80&w=2070" alt="Snake Plant" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold mb-2">Snake Plant</h3>
                    <p class="text-gray-300 mb-4">A low-maintenance plant that purifies the air and thrives in low light.</p>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold">₱850</span>
                        <button class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="reviews" class="py-16">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-serif font-bold text-center mb-12">What Our Customers Say</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="testimonial-card p-6 rounded-lg">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Maria Santos</h4>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-300">"The flowers I ordered for my mother's birthday were absolutely stunning! They arrived fresh and lasted for over two weeks. Will definitely order again!"</p>
            </div>
            <div class="testimonial-card p-6 rounded-lg">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">James Rodriguez</h4>
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-300">"As a plant enthusiast, I'm always looking for unique varieties. Flora Fleur has an amazing selection and the plants always arrive in perfect condition."</p>
            </div>
        </div>
    </div>
</section>

<section id="about" class="py-16 bg-black bg-opacity-20">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-serif font-bold text-center mb-12">About Flora Fleur</h2>
        <div class="max-w-3xl mx-auto text-center">
            <p class="text-lg mb-6">
                Flora Fleur was founded with a simple mission: to connect plant and flower lovers with the best local growers and florists in their area. We believe that everyone deserves to experience the beauty and benefits of plants in their daily lives.
            </p>
            <p class="text-lg mb-6">
                Our platform makes it easy to discover unique plants, order beautiful floral arrangements, and support local businesses—all while enjoying the convenience of online shopping and reliable delivery.
            </p>
            <p class="text-lg">
                Whether you're looking to brighten up your home, send a thoughtful gift, or start your own indoor garden, Flora Fleur is here to help you find exactly what you need.
            </p>
        </div>
    </div>
</section>

@endsection