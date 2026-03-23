<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
    products: Array,
    orders: Array,
    requests: Array,
    shops: Array,
    occasions: Array,
    user: Object,
});

// Reactive data
const currentView = ref('view-products');
const cart = ref([]);
const favorites = ref([]);
const selectedShop = ref(null);

// Computed helpers
const productSource = computed(() => props.products || []);
const shopSource = computed(() => props.shops || []);

const safeImageUrl = (image) => {
    if (!image) {
        return '/images/image_7f3485.jpg';
    }

    // already absolute URL
    if (image.startsWith('http') || image.startsWith('//')) {
        return image;
    }

    // already inside public root
    if (image.startsWith('/')) {
        return image;
    }

    // storage path from current DB (products/xxx.jpg)
    if (image.startsWith('products/')) {
        return `/storage/${image}`;
    }

    // fallback to storage path or public image
    return `/storage/${image}`;
};

// Computed properties
const cartTotal = computed(() => {
    return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const totalShops = computed(() => props.shops?.length || 0);
const totalProducts = computed(() => props.products?.length || 0);
const totalOrders = computed(() => props.orders?.length || 0);
const totalRequests = computed(() => props.requests?.length || 0);

// Methods
const showView = (viewId) => {
    currentView.value = viewId;
};

const addToCart = (product) => {
    const existing = cart.value.find(item => item.id === product.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.value.push({ ...product, quantity: 1 });
    }
};

const toggleFavorite = (product) => {
    const index = favorites.value.findIndex(item => item.id === product.id);
    if (index > -1) {
        favorites.value.splice(index, 1);
    } else {
        favorites.value.push(product);
    }
};

const selectShop = (shop) => {
    selectedShop.value = shop;
};

const filterProducts = (type, value) => {
    showView('view-products');
    // Filter logic would go here
};

onMounted(() => {
    // Initialize any needed data
});
</script>

<template>
    <Head title="Customer Dashboard" />

    <div id="app-container-dashboard" class="app-container active relative min-h-screen w-full flex-col font-sans">

        <div id="bg-image-container" class="absolute inset-0 w-full h-full z-0 transition-opacity duration-500">
            <img src="/images/image_7f3485.jpg" alt="Background" class="w-full h-full object-cover opacity-100">
            <div class="absolute inset-0 bg-black/40 z-10"></div>
        </div>

        <div class="relative z-20 flex flex-col min-h-screen w-full text-white">

            <header class="w-full bg-[#4A4A3A] shadow-md relative z-50">
                <nav class="max-w-[1920px] mx-auto px-6 h-20 flex justify-between items-center">
                    <div class="nav-link flex items-center gap-2 text-2xl tracking-wider font-rosarivo cursor-pointer" @click="showView('view-dashboard')">
                        <span>FLORA</span><img src="/images/image_8bd93d.png" class="w-7 h-7 object-contain"><span>FLEUR</span>
                    </div>

                    <div class="hidden md:flex items-center space-x-10 text-sm font-medium tracking-wider">
                        <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" @click="showView('view-dashboard')">HOME</button>

                        <!-- DYNAMIC OCCASIONS DROPDOWN -->
                        <div class="relative group h-20 flex items-center cursor-pointer">
                            <span class="hover:text-gray-300 flex items-center gap-1 uppercase">Occasions <i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                            <div class="absolute top-[calc(100%-0.5rem)] left-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <button
                                    v-for="occasion in occasions"
                                    :key="occasion"
                                    class="block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white"
                                    @click="filterProducts('occasion', occasion)">
                                    {{ occasion }}
                                </button>
                            </div>
                        </div>

                        <div class="relative group h-20 flex items-center cursor-pointer">
                            <span class="hover:text-gray-300 flex items-center gap-1 uppercase">Types <i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                            <div class="absolute top-[calc(100%-0.5rem)] left-0 w-48 bg-[#4A4A3A] border-t border-gray-500 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" @click="filterProducts('category', 'bouquet')">Bouquets</button>
                                <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" @click="filterProducts('category', 'basket')">Baskets</button>
                                <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" @click="filterProducts('category', 'standee')">Standees</button>
                                <button class="filter-btn block w-full text-left px-6 py-3 text-sm hover:bg-[#5c5c48] uppercase bg-transparent border-none cursor-pointer text-white" @click="filterProducts('category', 'box')">Flower Boxes</button>
                            </div>
                        </div>

                        <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" @click="showView('view-account')">ACCOUNT</button>
                        <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" @click="showView('view-shops')">ALL SHOPS</button>
                        <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" @click="showView('view-request')">REQUEST</button>
                        <button class="nav-link hover:text-gray-300 uppercase bg-transparent border-none cursor-pointer" @click="showView('view-purchases')">MY PURCHASES</button>
                    </div>

                    <div class="flex items-center gap-6">
                        <!-- FAVORITES ICON -->
                        <div class="nav-link flex flex-col items-center cursor-pointer group relative transition-transform hover:scale-110" @click="showView('view-favorites')">
                            <i class="fa-solid fa-heart text-xl text-white hover:text-red-400 transition-colors"></i>
                            <span class="text-[10px] uppercase tracking-widest mt-1 hover:text-red-400">Likes</span>
                        </div>

                        <!-- CART ICON -->
                        <div class="nav-link flex flex-col items-center cursor-pointer group relative transition-transform hover:scale-110" @click="showView('view-cart')">
                            <i class="fa-solid fa-cart-shopping text-xl"></i>
                            <span id="cart-badge" class="absolute -top-2 -right-2 bg-[#E65100] text-white text-[10px] rounded-full w-4 h-4 flex justify-center items-center opacity-0 transition-opacity" :class="{ 'opacity-100': cart.length > 0 }">{{ cart.length }}</span>
                            <span class="text-[10px] uppercase tracking-widest mt-1">Cart</span>
                        </div>

                        <form method="POST" action="/logout">
                            <button type="submit" class="bg-[#6B6B61] hover:bg-[#7D7D73] text-white px-5 py-2 rounded text-xs font-medium shadow-sm ml-2">LOGOUT</button>
                        </form>
                    </div>
                </nav>
            </header>

            <!-- DASHBOARD HOME -->
            <main id="view-dashboard" class="view-section active flex-grow flex flex-col justify-center px-4 lg:px-8 py-12 w-full max-w-[1400px] mx-auto text-white" :class="{ 'hidden': currentView !== 'view-dashboard' }">
                <div class="text-center mb-14 mt-4">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-normal leading-tight drop-shadow-lg">
                        <span class="block mb-2 font-light text-gray-200">Hey {{ user?.name }},</span>
                        <span class="font-medium tracking-wide">Welcome to Flora Fleur</span>
                    </h1>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 w-full">
                    <div class="filter-btn group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer" @click="filterProducts('all', 'all')">
                        <img src="/images/imagegranopening123.jpg" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                            <span class="text-2xl font-rosarivo tracking-wider text-white drop-shadow-md">Shop All</span>
                        </div>
                    </div>
                    <div class="filter-btn group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer mt-0 lg:mt-8" @click="filterProducts('category', 'bouquet')">
                        <img src="/images/bouquet.jpg" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                            <span class="text-2xl font-rosarivo tracking-wider text-white drop-shadow-md">Bouquets</span>
                        </div>
                    </div>
                    <div class="filter-btn group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer" @click="filterProducts('category', 'basket')">
                        <img src="/images/flower basket.jpg" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                            <span class="text-2xl font-rosarivo tracking-wider text-white drop-shadow-md">Baskets</span>
                        </div>
                    </div>
                    <div class="filter-btn group relative aspect-[3/4] overflow-hidden rounded-xl shadow-2xl cursor-pointer mt-0 lg:mt-8" @click="filterProducts('category', 'box')">
                        <img src="/images/flower boxes.jpg" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                            <span class="text-2xl font-rosarivo tracking-wider text-white drop-shadow-md">Flower Boxes</span>
                        </div>
                    </div>
                </div>
            </main>

            <!-- PRODUCTS GRID -->
            <main id="view-products" class="view-section hidden flex-grow w-full max-w-[1400px] mx-auto px-4 py-12 pt-28" :class="{ 'hidden': currentView !== 'view-products' }">
                 <h2 id="product-category-title" class="font-rosarivo text-4xl text-[#4A4A3A] mb-8 pl-4 border-l-4 border-[#86A873] bg-white/80 backdrop-blur-sm inline-block pr-6 py-2 rounded-r-lg text-gray-800">All Collection</h2>
                 <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <template v-if="productSource.length > 0">
                        <div v-for="product in productSource" :key="product.id" class="bg-white rounded-xl shadow-lg overflow-hidden group cursor-pointer hover:shadow-xl transition-shadow">
                            <div class="aspect-square overflow-hidden">
                                <img :src="safeImageUrl(product.image)" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-4">
                                <h3 class="font-rosarivo text-lg text-[#4A4A3A] mb-2">{{ product.name }}</h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ product.description }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-[#86A873]">₱{{ product.price }}</span>
                                    <div class="flex gap-2">
                                        <button @click="toggleFavorite(product)" class="p-2 rounded-full hover:bg-red-50 transition-colors" :class="{ 'text-red-500': favorites.some(f => f.id === product.id) }">
                                            <i class="fa-solid fa-heart"></i>
                                        </button>
                                        <button @click="addToCart(product)" class="bg-[#86A873] text-white px-4 py-2 rounded-lg hover:bg-[#759465] transition-colors">
                                            <i class="fa-solid fa-cart-plus mr-1"></i>Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-span-full text-center py-16 text-gray-500">No products available.</div>
                    </template>
                 </div>
            </main>

            <!-- FAVORITES VIEW -->
            <main id="view-favorites" class="view-section hidden flex-grow w-full max-w-[1400px] mx-auto px-4 py-12 pt-28" :class="{ 'hidden': currentView !== 'view-favorites' }">
                <h2 class="font-rosarivo text-4xl text-[#4A4A3A] mb-8 pl-4 border-l-4 border-red-400 bg-white/80 backdrop-blur-sm inline-block pr-6 py-2 rounded-r-lg text-gray-800">My Favorites</h2>
                <div id="favorites-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div v-for="product in favorites" :key="product.id" class="bg-white rounded-xl shadow-lg overflow-hidden group cursor-pointer hover:shadow-xl transition-shadow">
                        <div class="aspect-square overflow-hidden">
                            <img :src="product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-4">
                            <h3 class="font-rosarivo text-lg text-[#4A4A3A] mb-2">{{ product.name }}</h3>
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ product.description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-[#86A873]">₱{{ product.price }}</span>
                                <div class="flex gap-2">
                                    <button @click="toggleFavorite(product)" class="p-2 rounded-full text-red-500 hover:bg-red-50 transition-colors">
                                        <i class="fa-solid fa-heart"></i>
                                    </button>
                                    <button @click="addToCart(product)" class="bg-[#86A873] text-white px-4 py-2 rounded-lg hover:bg-[#759465] transition-colors">
                                        <i class="fa-solid fa-cart-plus mr-1"></i>Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- CART VIEW -->
            <main id="view-cart" class="view-section hidden flex-grow w-full max-w-5xl mx-auto px-4 py-12 pt-28 text-gray-800" :class="{ 'hidden': currentView !== 'view-cart' }">
                <h2 class="font-rosarivo text-4xl text-white mb-8">Your Cart</h2>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="overflow-x-auto"><table class="w-full text-left"><thead class="bg-gray-50 border-b border-gray-200"><tr><th class="px-6 py-4">Product</th><th class="px-6 py-4">Price</th><th class="px-6 py-4">Qty</th><th class="px-6 py-4">Total</th><th class="px-6 py-4"></th></tr></thead><tbody id="cart-items-container">
                        <tr v-for="item in cart" :key="item.id" class="border-b border-gray-100">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="item.image" :alt="item.name" class="w-12 h-12 object-cover rounded">
                                    <span class="font-medium">{{ item.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">₱{{ item.price }}</td>
                            <td class="px-6 py-4">{{ item.quantity }}</td>
                            <td class="px-6 py-4 font-bold">₱{{ item.price * item.quantity }}</td>
                            <td class="px-6 py-4">
                                <button class="text-red-500 hover:text-red-700" @click="cart.splice(cart.indexOf(item), 1)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody></table></div>
                    <div class="p-8 bg-gray-50 border-t flex flex-col items-end">
                        <div class="flex justify-between w-full max-w-xs mb-6 text-lg font-bold text-[#4A4A3A]"><span>Total</span><span id="cart-total">₱{{ cartTotal }}</span></div>
                        <button class="btn-checkout-page bg-[#4A4A3A] hover:bg-[#3a3a2e] text-white px-8 py-3 rounded-lg font-bold uppercase" @click="showView('view-checkout')">Proceed to Checkout</button>
                    </div>
                </div>
            </main>

            <!-- ALL SHOPS VIEW -->
            <main id="view-shops" class="view-section hidden flex-grow w-full max-w-[1400px] mx-auto px-4 py-12 pt-28" :class="{ 'hidden': currentView !== 'view-shops' }">
                <h2 class="font-rosarivo text-4xl text-[#4A4A3A] mb-8 pl-4 border-l-4 border-[#86A873] bg-white/80 backdrop-blur-sm inline-block pr-6 py-2 rounded-r-lg text-gray-800">All Shops</h2>
                <div id="shops-grid" class="space-y-6">
                    <div v-for="shop in shops" :key="shop.id" class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-rosarivo text-2xl text-[#4A4A3A] mb-2">{{ shop.name }}</h3>
                                    <p class="text-gray-600 mb-2">{{ shop.description }}</p>
                                    <p class="text-sm text-gray-500">{{ shop.address }}</p>
                                    <p class="text-sm text-gray-500">{{ shop.phone }} • {{ shop.email }}</p>
                                </div>
                                <button @click="selectShop(shop); showView('view-request')" class="bg-[#86A873] text-white px-6 py-2 rounded-lg hover:bg-[#759465] transition-colors">
                                    Request Custom
                                </button>
                            </div>
                            <div v-if="shop.products && shop.products.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div v-for="product in shop.products.slice(0, 4)" :key="product.id" class="aspect-square overflow-hidden rounded-lg">
                                    <img :src="product.image" :alt="product.name" class="w-full h-full object-cover hover:scale-105 transition-transform">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- PURCHASES VIEW -->
            <main id="view-purchases" class="view-section hidden flex-grow w-full max-w-5xl mx-auto px-4 py-12 pt-28 text-gray-800" :class="{ 'hidden': currentView !== 'view-purchases' }">
                 <h2 class="font-rosarivo text-4xl text-white mb-6">My Purchases</h2>
                 <div class="flex border-b border-gray-300 mb-8 bg-white/80 backdrop-blur-sm rounded-t-lg w-fit">
                    <button class="purchase-tab active px-6 py-3 text-[#4A4A3A] font-bold border-b-2 border-[#86A873]" data-tab="to-ship">To Ship</button>
                    <button class="purchase-tab px-6 py-3 text-gray-500 font-bold border-b-2 border-transparent" data-tab="completed">Completed</button>
                    <button class="purchase-tab px-6 py-3 text-gray-500 font-bold border-b-2 border-transparent" data-tab="requests">My Requests</button>
                 </div>
                 <div id="purchases-list" class="space-y-4">
                    <div v-for="order in orders" :key="order.id" class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-lg">Order #{{ order.id }}</h3>
                                <p class="text-gray-600">{{ order.date }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                  :class="{
                                      'bg-yellow-100 text-yellow-800': order.status === 'Pending',
                                      'bg-blue-100 text-blue-800': order.status === 'Processing',
                                      'bg-green-100 text-green-800': order.status === 'Delivered'
                                  }">
                                {{ order.status }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            <div v-for="item in order.items" :key="item.name" class="flex justify-between text-sm">
                                <span>{{ item.name }} (x{{ item.qty }})</span>
                                <span>₱{{ item.price * item.qty }}</span>
                            </div>
                        </div>
                        <div class="border-t pt-4 mt-4 flex justify-between items-center">
                            <span class="font-bold">Total: ₱{{ order.total }}</span>
                            <div v-if="order.driver" class="text-sm text-gray-600">
                                Driver: {{ order.driver }}
                            </div>
                        </div>
                    </div>
                 </div>
            </main>

            <!-- ACCOUNT VIEW -->
            <main id="view-account" class="view-section hidden flex-grow w-full max-w-4xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]" :class="{ 'hidden': currentView !== 'view-account' }">
                <div class="bg-white p-10 rounded-xl shadow-xl border border-gray-200">
                    <div class="flex items-center gap-6 pb-8 border-b">
                        <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center text-3xl shadow-inner">👤</div>
                        <div><h2 class="font-rosarivo text-3xl text-[#4A4A3A]">{{ user?.name }}</h2><p class="text-gray-500">{{ user?.email }}</p></div>
                    </div>
                    <form id="profile-form" class="mt-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div><label class="block text-sm font-bold text-gray-600 mb-1">Name</label><input id="prof-name" class="w-full p-3 border rounded bg-gray-50" :value="user?.name"></div>
                            <div><label class="block text-sm font-bold text-gray-600 mb-1">Email</label><input id="prof-email" class="w-full p-3 border rounded bg-gray-50" :value="user?.email" readonly></div>
                            <div><label class="block text-sm font-bold text-gray-600 mb-1">Phone</label><input id="prof-phone" class="w-full p-3 border rounded bg-gray-50" :value="user?.phone || ''" placeholder="09xxxxxxxxx"></div>
                        </div>
                        <div><label class="block text-sm font-bold text-gray-600 mb-1">Address</label><textarea id="prof-address" rows="3" class="w-full p-3 border rounded bg-gray-50">{{ user?.address || '' }}</textarea></div>
                        <div class="text-right"><button type="submit" class="btn-save-profile bg-[#86A873] hover:bg-[#759465] text-white font-bold py-3 px-8 rounded-lg shadow-lg">Save Changes</button></div>
                    </form>
                </div>
            </main>

            <!-- REQUEST VIEW -->
            <main id="view-request" class="view-section hidden flex-grow w-full max-w-3xl mx-auto px-4 py-12 pt-28 text-white" :class="{ 'hidden': currentView !== 'view-request' }">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 text-center">Custom Arrangement Request</h1>
                <p class="text-center text-gray-300 mb-8">Tell us your vision and we'll bring it to life!</p>

                <!-- Shop Selection Info -->
                <div id="shop-info-badge" class="bg-amber-900/40 border border-amber-400/50 rounded-lg p-4 mb-6" v-if="selectedShop">
                    <p class="text-sm text-amber-100">
                        <i class="fa-solid fa-store mr-2"></i>
                        <strong>Request will be sent to:</strong> <span id="selected-shop-name" class="font-bold text-amber-300">{{ selectedShop.name }}</span>
                    </p>
                </div>

                <div id="no-shop-warning" class="bg-red-900/40 border border-red-400/50 rounded-lg p-4 mb-6" v-else>
                    <p class="text-sm text-red-100">
                        <i class="fa-solid fa-exclamation-circle mr-2"></i>
                        Please <a href="#" class="underline font-bold hover:text-red-200" @click.prevent="showView('view-shops')">select a shop</a> first to send a custom request.
                    </p>
                </div>

                <div class="bg-white/10 border border-page-border-trans rounded-lg shadow-xl p-8 backdrop-blur-md" v-if="selectedShop">
                    <form id="custom-request-form" class="space-y-6">
                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-bold text-white/90 mb-2">
                                <i class="fa-solid fa-pencil mr-2 text-[#86A873]"></i>What's your vision?
                            </label>
                            <textarea id="request-description" rows="4" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white placeholder-white/50" required placeholder="Describe your arrangement, themes, flowers you like, style..."></textarea>
                        </div>

                        <!-- Occasion -->
                        <div>
                            <label class="block text-sm font-bold text-white/90 mb-2">
                                <i class="fa-solid fa-gift mr-2 text-[#86A873]"></i>Occasion
                            </label>
                            <select id="request-occasion" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white">
                                <option value="" class="text-gray-800">-- Select Occasion --</option>
                                <option value="Birthday" class="text-gray-800">Birthday</option>
                                <option value="Anniversary" class="text-gray-800">Anniversary</option>
                                <option value="Valentines" class="text-gray-800">Valentine's Day</option>
                                <option value="Mothers Day" class="text-gray-800">Mother's Day</option>
                                <option value="Graduation" class="text-gray-800">Graduation</option>
                                <option value="Wedding" class="text-gray-800">Wedding</option>
                                <option value="Funeral" class="text-gray-800">Funeral</option>
                                <option value="Just Because" class="text-gray-800">Just Because</option>
                                <option value="Other" class="text-gray-800">Other</option>
                            </select>
                        </div>

                        <!-- Date Needed & Budget -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-white/90 mb-2">
                                    <i class="fa-solid fa-calendar mr-2 text-[#86A873]"></i>Date Needed
                                </label>
                                <input type="datetime-local" id="request-date-needed" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-white/90 mb-2">
                                    <i class="fa-solid fa-money-bill mr-2 text-[#86A873]"></i>Budget Range (₱)
                                </label>
                                <input type="number" id="request-budget" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white placeholder-white/50" placeholder="e.g., 2000">
                            </div>
                        </div>

                        <!-- Color Preference & Contact -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-white/90 mb-2">
                                    <i class="fa-solid fa-palette mr-2 text-[#86A873]"></i>Color Preference
                                </label>
                                <input type="text" id="request-color" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white placeholder-white/50" placeholder="e.g., Red & Pink, Pastel, White">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-white/90 mb-2">
                                    <i class="fa-solid fa-phone mr-2 text-[#86A873]"></i>Contact Number
                                </label>
                                <input type="tel" id="request-contact" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white placeholder-white/50" placeholder="09xxxxxxxxx" required>
                            </div>
                        </div>

                        <!-- Reference Images -->
                        <div>
                            <label class="block text-sm font-bold text-white/90 mb-2">
                                <i class="fa-solid fa-image mr-2 text-[#86A873]"></i>Reference Images (Optional)
                            </label>
                            <input type="text" id="request-reference-link" class="w-full p-3 rounded bg-white/20 border border-white/30 text-white placeholder-white/50" placeholder="Paste link to Pinterest/Google Images or describe reference style">
                            <small class="text-white/70">Share links to images of styles you love</small>
                        </div>

                        <!-- Info Badge -->
                        <div class="bg-blue-900/30 border border-blue-400/50 rounded-lg p-4">
                            <p class="text-sm text-blue-100">
                                <i class="fa-solid fa-info-circle mr-2"></i>
                                After we receive your request, our florists will review it and provide a quote. You can discuss details and finalize the arrangement.
                            </p>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn-submit-request bg-[#86A873] hover:bg-[#759465] text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-colors">
                                <i class="fa-solid fa-send mr-2"></i>Send Request
                            </button>
                        </div>
                    </form>
                </div>
            </main>

            <!-- CHECKOUT VIEW -->
            <main id="view-checkout" class="view-section hidden flex-grow w-full max-w-6xl mx-auto px-4 py-12 pt-28 text-[#4A4A3A]" :class="{ 'hidden': currentView !== 'view-checkout' }">
                <h2 class="font-rosarivo text-4xl text-center text-white mb-10">Checkout</h2>
                <div class="flex flex-col gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-xl">
                        <h3 class="font-rosarivo text-2xl mb-6 text-center border-b pb-4">Your Order</h3>
                        <div id="checkout-cart-items" class="space-y-2 mb-6">
                            <div v-for="item in cart" :key="item.id" class="flex justify-between items-center py-2 border-b">
                                <div class="flex items-center gap-3">
                                    <img :src="item.image" :alt="item.name" class="w-12 h-12 object-cover rounded">
                                    <div>
                                        <p class="font-medium">{{ item.name }}</p>
                                        <p class="text-sm text-gray-600">Qty: {{ item.quantity }}</p>
                                    </div>
                                </div>
                                <span class="font-bold">₱{{ item.price * item.quantity }}</span>
                            </div>
                        </div>
                        <div class="border-t pt-4"><div class="flex justify-between font-bold text-lg mb-4"><span>Total</span><span id="checkout-total">₱{{ cartTotal }}</span></div></div>
                    </div>
                    <div class="bg-white p-8 rounded-xl shadow-xl">
                        <h3 class="font-rosarivo text-2xl mb-6 text-[#4A4A3A] flex items-center">
                            <i class="fa-solid fa-credit-card text-[#86A873] mr-3"></i>Payment Method
                        </h3>
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <button class="payment-btn py-3 border-2 rounded-lg bg-[#86A873] text-white selected font-bold transition-all hover:shadow-md" data-method="Cash On Delivery">
                                <i class="fa-solid fa-money-bill-wave block text-xl mb-2 mx-auto"></i>COD
                            </button>
                            <button class="payment-btn py-3 border-2 border-gray-300 rounded-lg text-gray-800 font-bold transition-all hover:shadow-md hover:border-[#86A873]" data-method="E-Wallet">
                                <i class="fa-solid fa-wallet block text-xl mb-2 mx-auto"></i>E-Wallet
                            </button>
                            <button class="payment-btn py-3 border-2 border-gray-300 rounded-lg text-gray-800 font-bold transition-all hover:shadow-md hover:border-[#86A873]" data-method="Card">
                                <i class="fa-solid fa-credit-card block text-xl mb-2 mx-auto"></i>Card
                            </button>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 text-sm text-blue-800">
                            <i class="fa-solid fa-info-circle mr-2"></i>
                            <span id="payment-info">Complete payment after checkout has been initiated.</span>
                        </div>
                        <button class="btn-place-order w-full bg-[#4A4A3A] text-white font-bold py-4 rounded-lg shadow-lg uppercase hover:bg-[#3a3a2e] transition-colors">
                            <i class="fa-solid fa-check-circle mr-2"></i>Place Order & Pay
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODALS -->
    <div id="product-modal" class="app-container fixed inset-0 z-50 flex items-center justify-center p-4 hidden" style="background-color: rgba(0,0,0,0.6);">
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

    <!-- THANK YOU MODAL -->
    <div id="thank-you-modal" class="app-container fixed inset-0 z-50 flex items-center justify-center p-4 hidden" style="background-color: rgba(0,0,0,0.6);">
        <div class="bg-white rounded-xl shadow-2xl p-10 text-center max-w-md">
            <h2 class="text-3xl font-rosarivo mb-4 text-[#4A4A3A]">Thank You!</h2>
            <p class="text-gray-600 mb-8">Order placed successfully.</p>
            <button class="btn-back-main w-full bg-[#4A4A3A] text-white font-bold py-3 rounded-lg">Back to Dashboard</button>
        </div>
    </div>

    <!-- RECEIPT MODAL -->
    <div id="receipt-modal" class="app-container fixed inset-0 z-50 flex items-center justify-center p-4 hidden" style="background-color: rgba(0,0,0,0.6);">
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl overflow-hidden relative font-mono text-sm text-black">
            <button class="modal-close-btn absolute top-2 right-2 text-gray-500 hover:text-red-500 z-50 p-2 text-xl font-bold">&times;</button>

            <div class="p-8 bg-white" id="receipt-content">
                <div class="text-center mb-6 border-b border-dashed border-gray-400 pb-4">
                    <h2 class="font-bold text-xl uppercase tracking-widest text-black">Flora Fleur</h2>
                    <p class="text-xs text-black">Official Receipt</p>
                </div>
                <div class="mb-4 text-black">
                    <div class="flex justify-between"><span>Order #:</span><span id="rec-id" class="font-bold"></span></div>
                    <div class="flex justify-between"><span>Date:</span><span id="rec-date"></span></div>
                    <div class="flex justify-between"><span>Customer:</span><span id="rec-name" class="font-bold">{{ user?.name }}</span></div>
                </div>
                <div class="border-t border-b border-dashed border-gray-400 py-4 mb-4 text-black">
                    <table class="w-full text-left">
                        <thead><tr><th class="pb-2 text-black">Item</th><th class="text-right pb-2 text-black">Amt</th></tr></thead>
                        <tbody id="rec-items"></tbody>
                        <tbody id="rec-quote-info"></tbody>
                    </table>
                </div>
                <div class="flex justify-between font-bold text-lg mb-6 text-black"><span>TOTAL</span><span id="rec-total"></span></div>
                <div class="text-center text-xs text-black mt-4"><p>Thank you for shopping with us!</p><p>Please come again.</p></div>
            </div>

            <div class="bg-gray-200 p-4 text-center border-t border-gray-300">
                <button class="bg-[#4A4A3A] text-white px-6 py-2 rounded text-xs font-bold uppercase hover:bg-gray-800 shadow-md" onclick="window.print()">Print Receipt</button>
            </div>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div id="payment-modal" class="app-container fixed inset-0 z-50 flex items-center justify-center p-4 hidden" style="background-color: rgba(0,0,0,0.8);">
        <div class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden relative font-sans">
            <button class="modal-close-btn absolute top-4 right-4 text-gray-500 hover:text-red-500 z-50 p-2 text-xl font-bold">&times;</button>

            <div class="bg-gradient-to-r from-[#86A873] to-[#6B9161] p-6 text-white text-center mb-6">
                <h3 id="pay-modal-title" class="font-rosarivo text-3xl mb-2">Complete Payment</h3>
                <p class="text-sm opacity-90">Secure & Convenient Payment Options</p>
            </div>

            <div class="p-8">
                <!-- E-WALLET SECTION -->
                <div id="pay-ewallet-content" class="hidden">
                    <div class="text-center mb-6">
                        <i class="fa-solid fa-wallet text-5xl text-[#86A873] mb-3"></i>
                        <p class="text-lg font-bold text-gray-800 mb-2">Pay via E-Wallet</p>
                        <p class="text-sm text-gray-600 mb-6">Quick and secure payment using GCash or Maya</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg mb-6 border-2 border-dashed border-gray-300">
                        <p class="text-sm font-bold text-gray-700 mb-3 text-center">Step 1: Scan QR Code</p>
                        <div class="bg-white w-32 h-32 mx-auto mb-4 flex items-center justify-center rounded-lg border-2 border-[#86A873]">
                            <i class="fa-solid fa-qrcode text-4xl text-[#86A873]"></i>
                        </div>
                        <p class="text-xs text-center text-gray-500">Using GCash or Maya app</p>
                    </div>

                    <div class="mb-4">
                        <label class="text-xs font-bold text-gray-700 uppercase block mb-2">Step 2: Enter Reference Number</label>
                        <input type="text" id="pay-ref-number" class="w-full border-2 border-gray-300 p-3 rounded-lg text-gray-800 focus:outline-none focus:border-[#86A873] focus:ring-2 focus:ring-[#86A873]/20" placeholder="e.g., 123456789">
                        <p class="text-xs text-gray-500 mt-2">You'll see this refcode in the GCash/Maya confirmation</p>
                    </div>
                </div>

                <!-- CARD SECTION -->
                <div id="pay-card-content" class="hidden">
                    <div class="text-center mb-6">
                        <i class="fa-solid fa-credit-card text-5xl text-[#86A873] mb-3"></i>
                        <p class="text-lg font-bold text-gray-800 mb-2">Pay with Card</p>
                        <p class="text-sm text-gray-600 mb-6">Visa, Mastercard, or other credit/debit cards</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-700 uppercase block mb-2">Card Number</label>
                            <input type="text" class="w-full border-2 border-gray-300 p-3 rounded-lg text-gray-800 focus:outline-none focus:border-[#86A873] focus:ring-2 focus:ring-[#86A873]/20" placeholder="0000 0000 0000 0000" maxlength="19">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-bold text-gray-700 uppercase block mb-2">Expiry</label>
                                <input type="text" class="w-full border-2 border-gray-300 p-3 rounded-lg text-gray-800 focus:outline-none focus:border-[#86A873] focus:ring-2 focus:ring-[#86A873]/20" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-700 uppercase block mb-2">CVV</label>
                                <input type="text" class="w-full border-2 border-gray-300 p-3 rounded-lg text-gray-800 focus:outline-none focus:border-[#86A873] focus:ring-2 focus:ring-[#86A873]/20" placeholder="123" maxlength="4">
                            </div>
                        </div>
                    </div>
                </div>

                <button id="btn-confirm-payment" class="w-full bg-[#4A4A3A] text-white font-bold py-3 rounded-lg shadow-lg hover:bg-[#3a3a2e] transition-colors mt-6">
                    <i class="fa-solid fa-lock mr-2"></i>Confirm & Pay Securely
                </button>

                <p class="text-xs text-gray-500 text-center mt-4">
                    <i class="fa-solid fa-shield text-green-500 mr-1"></i>Your payment information is secure
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.font-rosarivo {
    font-family: 'Rosarivo', serif;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
