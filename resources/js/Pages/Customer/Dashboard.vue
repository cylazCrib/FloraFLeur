<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch } from 'vue';

// --- MODULAR COMPONENT IMPORTS ---
import ProductCard from './Partials/ProductCard.vue';
import PurchaseItem from './Partials/PurchaseItem.vue';
import ProductModal from './Partials/ProductModal.vue';
import CartItem from './Partials/CartItem.vue';

const props = defineProps({
    products: Array,
    orders: Array,
    requests: Array,
    shops: Array,
    occasions: Array,
    user: Object,
});

// --- STATE ---
const currentView = ref('view-dashboard');
const cart = ref([]);
const favorites = ref([]);
const paymentMethod = ref('Cash On Delivery');
const currentProduct = ref(null);
const currentShop = ref(null);
const activePurchaseTab = ref('to-ship');
const filterType = ref('all');
const filterValue = ref('all');

// Modals
const showProductModal = ref(false);
const showThankYouModal = ref(false);
const showReceiptModal = ref(false);
const showPaymentModal = ref(false);
const receiptData = ref(null);
const modalQuantityValue = ref(1);
const paymentRefNumber = ref('');

// --- COMPUTED ---
const filteredProducts = computed(() => {
    let items = currentShop.value ? (currentShop.value.products || []) : (props.products || []);
    if (filterType.value === 'all') return items;
    if (filterType.value === 'occasion') return items.filter(p => p.occasion === filterValue.value);
    if (filterType.value === 'category') return items.filter(p => p.category === filterValue.value);
    return items;
});

// SAFE MATH Calculation for Cart Total
const cartTotal = computed(() => {
    return cart.value.reduce((total, item) => {
        return total + (Number(item.price || 0) * Number(item.qty || 0));
    }, 0);
});

const cartBadgeCount = computed(() => cart.value.reduce((a, b) => a + Number(b.qty || 0), 0));

const cartShops = computed(() => {
    const shopIds = [...new Set(cart.value.map(i => i.shop_id))];
    return props.shops.filter(s => shopIds.includes(s.id));
});

const filteredOrdersForView = computed(() => {
    if (activePurchaseTab.value === 'requests') return props.requests || [];
    return (props.orders || []).filter(o => {
        const s = o.status?.toLowerCase();
        if (activePurchaseTab.value === 'completed') return s === 'delivered' || s === 'completed';
        if (activePurchaseTab.value === 'to-ship') return !['delivered', 'completed', 'canceled'].includes(s);
        return false;
    });
});

// --- METHODS ---
const showView = (target) => {
    currentView.value = target;
    if (target === 'view-dashboard') {
        filterType.value = 'all';
        filterValue.value = 'all';
        currentShop.value = null;
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const filterProducts = (type, value) => {
    filterType.value = type;
    filterValue.value = value;
    currentShop.value = null;
    showView('view-products');
};

const openProductModal = (product) => {
    currentProduct.value = product;
    modalQuantityValue.value = 1;
    showProductModal.value = true;
};

const closeModals = () => {
    showProductModal.value = false;
    showThankYouModal.value = false;
    showReceiptModal.value = false;
    showPaymentModal.value = false;
};

const addToCart = (p, qty) => {
    const exist = cart.value.find(i => i.id === p.id);
    if (exist) exist.qty += Number(qty);
    else cart.value.push({ ...p, qty: Number(qty) });
    saveData();
    closeModals();
};

const removeFromCart = (id) => {
    cart.value = cart.value.filter(i => i.id !== id);
    saveData();
};

const toggleFavorite = (p) => {
    const idx = favorites.value.findIndex(f => f.id === p.id);
    if (idx > -1) favorites.value.splice(idx, 1);
    else favorites.value.push(p);
    saveData();
};

const isFavorite = (id) => favorites.value.some(f => f.id === id);

const saveData = () => {
    localStorage.setItem('flora_cart', JSON.stringify(cart.value));
    localStorage.setItem('flora_favs', JSON.stringify(favorites.value));
};

const viewShopProducts = (shop) => {
    currentShop.value = shop;
    filterType.value = 'all';
    filterValue.value = 'all';
    showView('view-products');
};

// RECEIPT RESTORATION LOGIC
const openReceipt = (id, type) => {
    let sourceArray = type === 'request' ? props.requests : props.orders;
    const found = sourceArray.find(o => String(o.id) === String(id));
    
    if (!found) return;

    receiptData.value = {
        id: found.order_number || (type === 'request' ? 'REQ-' + found.id : 'ORD-' + found.id),
        date: found.created_at,
        total: parseFloat(found.total_amount || found.vendor_quote || found.budget || 0),
        payment_method: found.payment_method || 'Verified',
        items: found.items && found.items.length > 0 
            ? found.items.map(i => ({ 
                qty: Number(i.quantity || 1), 
                name: i.name || i.product_name, 
                price: parseFloat(i.price || 0) 
            }))
            : [{ 
                qty: 1, 
                name: found.description ? 'Custom: ' + found.description.substring(0, 30) + '...' : 'Custom Arrangement', 
                price: parseFloat(found.total_amount || found.vendor_quote || found.budget || 0) 
            }],
        type: type
    };
    showReceiptModal.value = true;
};

const confirmOrder = () => {
    if (cart.value.length === 0) return alert('Your cart is empty.');
    if (paymentMethod.value === 'Cash On Delivery') {
        if (confirm("Confirm order via COD?")) submitOrder(null);
    } else {
        showPaymentModal.value = true;
    }
};

const submitOrder = (refNo) => {
    router.post('/customer/order', {
        items: cart.value.map(i => ({ id: i.id, qty: i.qty })),
        payment_method: paymentMethod.value,
        payment_reference: refNo
    }, {
        onSuccess: () => {
            cart.value = [];
            saveData();
            closeModals();
            showThankYouModal.value = true;
        }
    });
};

const handleConfirmPayment = () => {
    let ref = null;
    if (paymentMethod.value === 'E-Wallet') {
        if (!paymentRefNumber.value) return alert('Enter Reference Number');
        ref = 'GCash: ' + paymentRefNumber.value;
    } else if (paymentMethod.value === 'Card') {
        ref = 'Card-' + Math.floor(Math.random() * 1000000);
    }
    submitOrder(ref);
};

const saveProfile = (e) => {
    const f = e.target;
    const d = { 
        name: f.querySelector('#prof-name').value, 
        phone: f.querySelector('#prof-phone').value, 
        address: f.querySelector('#prof-address').value 
    };
    router.patch('/profile', d, { onSuccess: () => alert('Profile updated!') });
};

const submitCustomRequest = (e) => {
    if (!currentShop.value) return alert('Select a shop.');
    const f = e.target;
    const d = {
        shop_id: currentShop.value.id,
        description: f.querySelector('#request-description').value,
        occasion: f.querySelector('#request-occasion').value,
        date_needed: f.querySelector('#request-date-needed').value,
        budget: f.querySelector('#request-budget').value,
        color_preference: f.querySelector('#request-color').value,
        contact_number: f.querySelector('#request-contact').value,
        reference_image_url: f.querySelector('#request-reference-link').value
    };
    router.post('/customer/request', d, { onSuccess: () => { alert('Request sent!'); showView('view-dashboard'); } });
};

const formatPrice = (p) => {
    const val = parseFloat(p || 0);
    return '₱' + val.toLocaleString(undefined, { minimumFractionDigits: 2 });
};

onMounted(() => {
    cart.value = JSON.parse(localStorage.getItem('flora_cart')) || [];
    favorites.value = JSON.parse(localStorage.getItem('flora_favs')) || [];
});

watch([cart, favorites], () => saveData(), { deep: true });
</script>

<template>
    <Head title="Flora Fleur - Customer Dashboard" />

    <div id="app-container-dashboard" class="relative min-h-screen w-full flex flex-col font-sans bg-[#1a1a1a]">
        
        <div id="bg-image-container" class="fixed inset-0 w-full h-full z-0 transition-opacity duration-700">
            <img src="/images/image_7f3485.jpg" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50 z-10"></div>
        </div>

        <div class="relative z-20 flex flex-col min-h-screen w-full text-white">
            
            <header class="w-full bg-[#4A4A3A]/90 backdrop-blur-md shadow-md sticky top-0 z-50 border-b border-white/10">
                <nav class="max-w-[1920px] mx-auto px-6 h-20 flex justify-between items-center">
                    <div class="nav-link flex items-center gap-2 text-2xl tracking-wider font-rosarivo cursor-pointer" @click="showView('view-dashboard')">
                        <span>FLORA</span><img src="/images/image_8bd93d.png" class="w-7 h-7 object-contain"><span>FLEUR</span>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-10 text-xs font-bold tracking-[0.2em]">
                        <button class="hover:text-[#86A873] transition cursor-pointer bg-transparent border-none text-white uppercase" @click="showView('view-dashboard')">HOME</button>
                        <div class="relative group h-20 flex items-center cursor-pointer">
                            <span class="hover:text-[#86A873] flex items-center gap-1 uppercase">Occasions <i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                            <div class="absolute top-[calc(100%-0.5rem)] left-0 w-48 bg-[#4A4A3A] border border-white/10 shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 rounded-b-lg">
                                <button v-for="occ in occasions" :key="occ" @click="filterProducts('occasion', occ)" class="block w-full text-left px-6 py-4 text-xs hover:bg-[#5c5c48] uppercase cursor-pointer text-white bg-transparent border-none">{{ occ }}</button>
                            </div>
                        </div>
                        <button class="hover:text-[#86A873] transition cursor-pointer bg-transparent border-none text-white uppercase" @click="showView('view-account')">ACCOUNT</button>
                        <button class="hover:text-[#86A873] transition cursor-pointer bg-transparent border-none text-white uppercase" @click="showView('view-shops')">SHOPS</button>
                        <button class="hover:text-[#86A873] transition cursor-pointer bg-transparent border-none text-white uppercase" @click="showView('view-request')">REQUEST</button>
                        <button class="hover:text-[#86A873] transition cursor-pointer bg-transparent border-none text-white uppercase" @click="showView('view-purchases')">PURCHASES</button>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="cursor-pointer group relative transition-transform hover:scale-110" @click="showView('view-favorites')">
                            <i class="fa-solid fa-heart text-xl transition-colors" :class="favorites.length > 0 ? 'text-red-400' : 'text-white'"></i>
                        </div>
                        <div class="cursor-pointer relative group transition-transform hover:scale-110" @click="showView('view-cart')">
                            <i class="fa-solid fa-cart-shopping text-xl text-white"></i>
                            <span v-if="cartBadgeCount > 0" class="absolute -top-2 -right-2 bg-orange-600 text-white text-[9px] rounded-full w-4 h-4 flex justify-center items-center font-bold shadow-lg border border-white/20">{{ cartBadgeCount }}</span>
                        </div>
                        <button 
                         @click="router.post(route('logout'))" 
                        class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-5 py-2 rounded-full text-[10px] font-bold tracking-widest transition"
                        >
                         LOGOUT
                    </button>
                    </div>
                </nav>
            </header>

            <main v-if="currentView === 'view-dashboard'" class="view-section flex-grow flex flex-col justify-center px-4 py-12 w-full max-w-[1400px] mx-auto">
                <div class="text-center mb-14 animate-fade-in">
                    <h1 class="text-3xl md:text-6xl font-rosarivo drop-shadow-2xl">
                        <span class="block mb-2 font-light text-gray-300 text-xl tracking-[0.3em] uppercase">Greetings, {{ user.name }}</span>
                        <span class="font-medium tracking-wide">Welcome to Flora Fleur</span>
                    </h1>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12 w-full">
                    <div class="group relative aspect-[3/4] overflow-hidden rounded-2xl shadow-2xl cursor-pointer hover:-translate-y-2 transition-all duration-500" @click="filterProducts('all', 'all')">
                        <img src="/images/imagegranopening123.jpg" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                            <span class="text-xl font-rosarivo tracking-widest">Shop All</span>
                        </div>
                    </div>
                    <div v-for="cat in ['bouquet', 'basket', 'box']" :key="cat" class="group relative aspect-[3/4] overflow-hidden rounded-2xl shadow-2xl cursor-pointer hover:-translate-y-2 transition-all duration-500" @click="filterProducts('category', cat)">
                        <img :src="`/images/${cat === 'box' ? 'flower boxes' : cat === 'basket' ? 'flower basket' : cat}.jpg`" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-6">
                            <span class="text-xl font-rosarivo tracking-widest capitalize">{{ cat }}s</span>
                        </div>
                    </div>
                </div>
            </main>

            <main v-else class="view-section flex-grow w-full max-w-[1400px] mx-auto px-4 py-12 pt-16">
                <div class="bg-black/30 backdrop-blur-sm rounded-[2rem] border border-white/10 p-8 md:p-12 shadow-2xl min-h-[60vh]">
                    
                    <div v-if="currentView === 'view-products'">
                        <h2 class="font-rosarivo text-4xl mb-10 border-l-4 border-[#86A873] pl-6 uppercase tracking-widest">{{ currentShop ? currentShop.name : (filterValue === 'all' ? 'Collection' : filterValue) }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            <ProductCard 
                                v-for="p in filteredProducts" 
                                :key="p.id" 
                                :product="p" 
                                :isFav="isFavorite(p.id)"
                                @toggle-fav="toggleFavorite"
                                @open-modal="openProductModal"
                            />
                        </div>
                        <div v-if="filteredProducts.length === 0" class="text-center py-20 text-gray-400 italic">No products available in this selection.</div>
                    </div>

                    <div v-if="currentView === 'view-purchases'">
                        <h2 class="font-rosarivo text-4xl mb-10">Purchase History</h2>
                        <div class="flex gap-4 mb-8 border-b border-white/10 pb-4">
                            <button v-for="tab in ['to-ship', 'completed', 'requests']" :key="tab" @click="activePurchaseTab = tab" :class="activePurchaseTab === tab ? 'bg-[#86A873] text-white shadow-lg' : 'text-gray-400 hover:text-white'" class="px-6 py-2 rounded-full font-bold text-xs uppercase transition-all border-none cursor-pointer">{{ tab.replace('-', ' ') }}</button>
                        </div>
                        <div class="space-y-6">
                            <PurchaseItem 
                                v-for="item in filteredOrdersForView" 
                                :key="item.id" 
                                :item="item" 
                                :isRequest="activePurchaseTab === 'requests'"
                                @open-receipt="openReceipt"
                            />
                            <div v-if="filteredOrdersForView.length === 0" class="text-center py-20 text-gray-400 italic"><p>No activity found in this category.</p></div>
                        </div>
                    </div>

                    <div v-if="currentView === 'view-favorites'">
                        <h2 class="font-rosarivo text-4xl mb-10 border-l-4 border-red-400 pl-6">Saved Items</h2>
                        <div v-if="favorites.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            <ProductCard 
                                v-for="p in favorites" 
                                :key="p.id" 
                                :product="p" 
                                :isFav="true"
                                @toggle-fav="toggleFavorite"
                                @open-modal="openProductModal"
                            />
                        </div>
                        <div v-else class="text-center py-20 text-gray-400 italic text-xl">You haven't liked any arrangements yet.</div>
                    </div>

                    <div v-if="currentView === 'view-cart'">
                        <h2 class="font-rosarivo text-4xl mb-10">Your Bag</h2>
                        <div v-if="cart.length > 0" class="space-y-4">
                            <CartItem 
                                v-for="item in cart" 
                                :key="item.id" 
                                :item="item" 
                                @remove="removeFromCart"
                            />
                            <div class="pt-10 flex flex-col items-end">
                                <div class="text-3xl font-rosarivo mb-6 flex gap-10"><span>Total</span><span>{{ formatPrice(cartTotal) }}</span></div>
                                <button @click="showView('view-checkout')" class="bg-[#86A873] hover:bg-[#759465] text-white px-12 py-4 rounded-2xl font-bold shadow-xl transition-transform hover:scale-105 border-none cursor-pointer uppercase tracking-widest">Proceed to Checkout</button>
                            </div>
                        </div>
                        <div v-else class="text-center py-20 text-gray-400"><p class="text-xl italic">No items in your bag.</p></div>
                    </div>

                    <div v-if="currentView === 'view-checkout'">
                        <h2 class="font-rosarivo text-4xl mb-10 text-center">Secure Checkout</h2>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                            <div class="space-y-6">
                                <div class="bg-white/5 p-8 rounded-3xl border border-white/10">
                                    <h3 class="font-rosarivo text-2xl mb-6">Payment Method</h3>
                                    <div class="grid grid-cols-3 gap-3 mb-8">
                                        <button @click="paymentMethod = 'Cash On Delivery'" :class="paymentMethod === 'Cash On Delivery' ? 'bg-[#86A873] text-white border-[#86A873]' : 'border-white/10 bg-white/5'" class="py-4 rounded-2xl font-bold border text-xs uppercase tracking-widest cursor-pointer transition-all">COD</button>
                                        <button @click="paymentMethod = 'E-Wallet'" :class="paymentMethod === 'E-Wallet' ? 'bg-[#86A873] text-white border-[#86A873]' : 'border-white/10 bg-white/5'" class="py-4 rounded-2xl font-bold border text-xs uppercase tracking-widest cursor-pointer transition-all">E-Wallet</button>
                                        <button @click="paymentMethod = 'Card'" :class="paymentMethod === 'Card' ? 'bg-[#86A873] text-white border-[#86A873]' : 'border-white/10 bg-white/5'" class="py-4 rounded-2xl font-bold border text-xs uppercase tracking-widest cursor-pointer transition-all">Card</button>
                                    </div>
                                    <button @click="confirmOrder" class="w-full bg-[#86A873] hover:bg-[#759465] text-white py-5 rounded-2xl font-bold uppercase tracking-widest transition shadow-xl">Place Order & Pay</button>
                                </div>
                                <div class="bg-white/5 p-8 rounded-3xl border border-white/10">
                                    <h3 class="font-rosarivo text-xl mb-4">Shipping Information</h3>
                                    <div class="space-y-2 text-sm opacity-80 italic">
                                        <p>{{ user.name }}</p><p>{{ user.phone }}</p><p>{{ user.address }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white/5 p-8 rounded-3xl border border-white/10 h-fit">
                                <h3 class="font-rosarivo text-2xl mb-6">Order Summary</h3>
                                <div class="space-y-4 mb-8">
                                    <div v-for="i in cart" :key="i.id" class="flex justify-between text-sm items-center py-2 border-b border-white/5">
                                        <span class="opacity-70">{{ i.qty }}x {{ i.name }}</span>
                                        <span class="font-bold">{{ formatPrice(i.price * i.qty) }}</span>
                                    </div>
                                    <div class="pt-4 flex justify-between font-bold text-xl text-[#86A873]">
                                        <span>Total</span><span>{{ formatPrice(cartTotal) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="currentView === 'view-shops'">
                        <h2 class="font-rosarivo text-4xl mb-10 border-l-4 border-[#86A873] pl-6 uppercase tracking-widest">Local Florists</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div v-for="shop in shops" :key="shop.id" class="bg-white/10 p-8 rounded-3xl cursor-pointer border border-white/10 hover:bg-white/20 transition-all group" @click="viewShopProducts(shop)">
                                <h3 class="font-rosarivo text-2xl mb-2 group-hover:text-[#86A873] transition capitalize">{{ shop.name }}</h3>
                                <p class="text-gray-400 text-sm mb-6 leading-relaxed">{{ shop.description || 'Exclusive boutique floral arrangements.' }}</p>
                                <div class="flex flex-wrap gap-6 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    <span><i class="fa-solid fa-location-dot text-[#86A873] mr-2"></i>{{ shop.address }}</span>
                                    <span><i class="fa-solid fa-phone text-[#86A873] mr-2"></i>{{ shop.phone }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="currentView === 'view-account'" class="max-w-2xl mx-auto">
                        <h2 class="font-rosarivo text-4xl mb-10 text-center uppercase tracking-[0.2em]">Profile Settings</h2>
                        <form @submit.prevent="saveProfile" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2"><label class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Full Name</label><input id="prof-name" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white focus:bg-white/20 outline-none transition" :value="user.name"></div>
                                <div class="space-y-2"><label class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Phone</label><input id="prof-phone" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white focus:bg-white/20 outline-none transition" :value="user.phone"></div>
                            </div>
                            <div class="space-y-2"><label class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Delivery Address</label><textarea id="prof-address" rows="3" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white focus:bg-white/20 outline-none transition">{{ user.address }}</textarea></div>
                            <button type="submit" class="w-full bg-[#86A873] text-white py-4 rounded-2xl font-bold border-none cursor-pointer shadow-lg uppercase tracking-widest transition-transform hover:scale-[1.02]">Save Changes</button>
                        </form>
                    </div>

                    <div v-if="currentView === 'view-request'" class="max-w-3xl mx-auto">
                        <h2 class="font-rosarivo text-4xl mb-4 text-center uppercase tracking-widest">Custom Arrangement</h2>
                        <p class="text-center text-gray-400 mb-10 italic">Work with our designers for a bespoke floral experience.</p>
                        <div v-if="currentShop" class="space-y-6 bg-white/5 p-8 rounded-[2rem] border border-white/10">
                            <div class="text-center mb-6 pb-6 border-b border-white/5">
                                <p class="text-[10px] uppercase font-bold text-gray-500 mb-1">Requesting from</p>
                                <p class="font-rosarivo text-xl text-[#86A873] capitalize">{{ currentShop.name }}</p>
                            </div>
                            <form @submit.prevent="submitCustomRequest" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Arrangement Details</label>
                                    <textarea id="request-description" rows="4" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white focus:bg-white/20 outline-none" placeholder="Describe the look, flowers, or feel you want..."></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Occasion</label>
                                    <select id="request-occasion" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white outline-none"><option v-for="occ in occasions" :key="occ" :value="occ" class="text-black">{{ occ }}</option></select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Date Needed</label>
                                    <input type="datetime-local" id="request-date-needed" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Budget (₱)</label>
                                    <input type="number" id="request-budget" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Color Theme</label>
                                    <input type="text" id="request-color" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white outline-none" placeholder="e.g. Pastel Pink, Deep Red">
                                </div>
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Reference Image URL (Optional)</label>
                                    <input type="text" id="request-reference-link" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white outline-none" placeholder="Link to a sample image">
                                </div>
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Contact Number</label>
                                    <input type="text" id="request-contact" :value="user.phone" class="w-full p-4 rounded-2xl bg-white/10 border border-white/20 text-white outline-none">
                                </div>
                                <button type="submit" class="md:col-span-2 bg-[#86A873] py-5 rounded-2xl font-bold uppercase tracking-widest shadow-lg transition-transform hover:scale-[1.02] hover:bg-[#759465]">Submit Design Request</button>
                            </form>
                        </div>
                        <div v-else class="text-center py-24 text-gray-400 border-2 border-dashed border-white/10 rounded-[2.5rem] bg-white/5">
                            <div class="mb-6 opacity-20"><i class="fa-solid fa-store text-6xl"></i></div>
                            <p class="mb-4 text-sm uppercase tracking-[0.3em]">Florist Not Selected</p>
                            <p class="text-xs mb-8 opacity-60">Please choose a shop to start a custom arrangement.</p>
                            <button @click="showView('view-shops')" class="text-[#86A873] font-bold uppercase tracking-widest border border-[#86A873] px-8 py-3 rounded-full hover:bg-[#86A873] hover:text-white transition cursor-pointer">Browse Shops</button>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <ProductModal 
        v-if="showProductModal && currentProduct" 
        :product="currentProduct" 
        @close="closeModals" 
        @add-to-cart="addToCart"
        @buy-now="(p, q) => { addToCart(p, q); showView('view-checkout'); }"
    />

    <div v-if="showPaymentModal" class="fixed inset-0 z-[105] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 text-gray-800 text-center shadow-2xl">
            <h3 class="font-rosarivo text-3xl mb-4">Secure Payment</h3>
            <p class="text-sm opacity-60 mb-8 uppercase tracking-widest">Verify via {{ paymentMethod }}</p>
            <div v-if="paymentMethod === 'E-Wallet'" class="mb-8">
                <div v-for="shop in cartShops" :key="shop.id" class="mb-4 p-4 border rounded-xl">
                    <p class="font-bold mb-2">{{ shop.name }} QR Codes</p>
                    <div class="flex justify-around gap-2">
                        <div v-if="shop.gcash_qr_url" class="text-[10px]">
                            <p>GCash</p>
                            <img :src="shop.gcash_qr_url" class="w-24 h-24 object-contain border">
                        </div>
                        <div v-if="shop.maya_qr_url" class="text-[10px]">
                            <p>Maya</p>
                            <img :src="shop.maya_qr_url" class="w-24 h-24 object-contain border">
                        </div>
                    </div>
                    <div v-if="shop.payment_instructions" class="mt-2 text-[9px] text-left italic">
                        <p v-for="inst in shop.payment_instructions" :key="inst">• {{ inst }}</p>
                    </div>
                </div>
                <input v-model="paymentRefNumber" class="w-full p-5 rounded-2xl border-2 border-gray-100 text-center font-bold text-xl outline-none focus:border-[#86A873] transition" placeholder="Reference Number">
                <p class="text-[10px] text-gray-400 mt-3 italic uppercase">Please transfer amount to shop's GCash/E-Wallet</p>
            </div>
            <div v-else-if="paymentMethod === 'Card'" class="mb-8 p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <p class="text-xs font-bold opacity-60 uppercase tracking-widest">Payment Provider Redirect</p>
                <p class="text-sm mt-2">Connecting to secure gateway...</p>
            </div>
            <div class="flex gap-4">
                <button @click="showPaymentModal = false" class="flex-1 border border-gray-200 py-4 rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-gray-50 transition cursor-pointer">Cancel</button>
                <button @click="handleConfirmPayment" class="flex-1 bg-[#86A873] text-white py-4 rounded-2xl font-bold uppercase tracking-widest text-xs shadow-lg hover:bg-[#759465] transition cursor-pointer">Verify & Pay</button>
            </div>
        </div>
    </div>

    <div v-if="showThankYouModal" class="fixed inset-0 z-[120] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        <div class="bg-white rounded-[3rem] p-12 text-center text-gray-800 max-w-sm shadow-2xl animate-zoom">
            <div class="w-20 h-20 bg-[#86A873] rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-[#86A873]/30"><i class="fa-solid fa-check text-4xl text-white"></i></div>
            <h2 class="text-3xl font-rosarivo mb-4 uppercase tracking-widest">Order Confirmed</h2>
            <p class="text-gray-500 mb-10 leading-relaxed text-sm italic">Your beauty selection is being prepared by our florist partner. We will notify you once it's out for delivery.</p>
            <button @click="showView('view-purchases'); closeModals()" class="w-full bg-[#4A4A3A] text-white py-4 rounded-2xl font-bold uppercase tracking-widest shadow-lg cursor-pointer transition hover:scale-105">Check Order Status</button>
        </div>
    </div>

    <div v-if="showReceiptModal && receiptData" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md">
        <div class="bg-white w-full max-w-sm rounded-lg shadow-2xl relative p-8 text-gray-800 font-mono text-xs">
            <button @click="closeModals" class="absolute top-2 right-2 text-gray-400 text-xl cursor-pointer bg-transparent border-none transition hover:text-red-500">&times;</button>
            
            <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
                <h2 class="text-xl font-bold uppercase tracking-widest font-rosarivo">Flora Fleur</h2>
                <p class="mt-1">Transaction: {{ receiptData.id }}</p>
                <p>Date: {{ receiptData.date }}</p>
            </div>

            <div class="space-y-3 mb-6">
                <div v-for="(i, idx) in receiptData.items" :key="idx" class="flex justify-between items-start gap-4">
                    <span class="flex-grow">{{ i.qty }}x {{ i.name }}</span>
                    <span class="whitespace-nowrap">{{ formatPrice(i.price * i.qty) }}</span>
                </div>
            </div>

            <div v-if="receiptData.type === 'request'" class="border-t border-dashed border-gray-300 pt-4 mb-4 opacity-70 italic text-[10px]">
                <div class="flex justify-between uppercase"><span>Input Budget:</span><span>{{ formatPrice(receiptData.budget) }}</span></div>
                <div v-if="receiptData.vendor_quote" class="flex justify-between font-bold text-black uppercase"><span>Final Quote:</span><span>{{ formatPrice(receiptData.vendor_quote) }}</span></div>
            </div>

            <div class="flex justify-between font-bold text-lg pt-4 border-t-2 border-black">
                <span>TOTAL PAID</span><span>{{ formatPrice(receiptData.total) }}</span>
            </div>

            <div class="text-center mt-8 opacity-40 italic">
                *** Beauty delivered. Thank you! ***
            </div>
            
            <div class="mt-6 text-center no-print">
                 <button class="bg-[#4A4A3A] text-white px-6 py-2 rounded-full text-[9px] font-bold tracking-widest hover:bg-[#86A873] transition cursor-pointer border-none shadow-sm" onclick="window.print()">PRINT PHYSICAL COPY</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Rosarivo&display=swap');
.font-rosarivo { font-family: 'Rosarivo', serif; }
.view-section { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
.animate-zoom { animation: zoomIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes zoomIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
@media print { .no-print { display: none; } }
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #1a1a1a; }
::-webkit-scrollbar-thumb { background: #4A4A3A; border-radius: 10px; }
</style>