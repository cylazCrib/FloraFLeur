<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

// --- PROPS ---
const props = defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    status: { type: String },
});

// --- STATE MANAGEMENT (Matches landing.js functionality) ---
const activeModal = ref(null); // 'signin', 'signup', 'admin', 'shop-signup'
const showMobileMenu = ref(false);
const showToast = ref(false);
const toastMessage = ref('Action completed successfully!');
const toastType = ref('success');
const fileLabelText = ref('DTI/Business Permit (PDF, JPG, PNG)');

// --- FORMS (Replaces manual form logic) ---
const loginForm = useForm({ email: '', password: '', remember: false, login_as_vendor: false });
const signupForm = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const storeSignupForm = useForm({ 
    owner_name: '', email: '', password: '', password_confirmation: '', owner_phone: '',
    shop_name: '', shop_description: '', shop_phone: '', address: '', zip_code: '',
    delivery_coverage: '', permit_file: null 
});

// --- UTILITY FUNCTIONS (1:1 with landing.js) ---
const triggerToast = (msg, type = 'success') => {
    toastMessage.value = msg;
    toastType.value = type;
    showToast.value = true;
    setTimeout(() => { showToast.value = false; }, 3000);
};

const openModal = (name) => {
    activeModal.value = name;
    showMobileMenu.value = false;
    document.body.style.overflow = 'hidden'; // Lock scrolling as per landing.js
};

const closeModal = () => {
    activeModal.value = null;
    document.body.style.overflow = 'auto'; // Restore scrolling
};

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (file) {
        storeSignupForm.permit_file = file;
        fileLabelText.value = file.name; // Logic from landing.js
    }
};

// --- FORM SUBMISSIONS (Matches landing.js logic & redirects) ---
const submitLogin = () => {
    loginForm.post(route('login'), {
        // We removed window.location.href because the Controller 
        // handles the redirect to the correct dashboard.
        onSuccess: () => {
            const msg = loginForm.login_as_vendor ? 'Vendor login successful!' : 'Welcome back!';
            triggerToast(msg);
            closeModal();
            // Inertia will now automatically transition the page 
            // based on the Controller's redirect response.
        },
        onFinish: () => loginForm.reset('password'),
    });
};

const submitSignup = () => {
    signupForm.post(route('register'), {
        onSuccess: () => {
            window.location.href = route('dashboard');
        },
    });
};

const submitStoreSignup = () => {
    storeSignupForm.post(route('shop.register'), {
        onSuccess: () => {
            window.location.href = route('dashboard');
        },
        onError: (errors) => {
            console.error('Store signup errors:', errors);
        }
    });
};

onMounted(() => {
    if (props.status) {
        triggerToast(props.status, 'success');
    }
    // Handle Escape key to close modals
    window.addEventListener('keydown', (e) => { if (e.key === "Escape") closeModal(); });
});
</script>

<template>
    <Head title="Flora Fleur - Welcome" />

    <div class="landing-page-wrapper min-h-screen flex flex-col text-white scroll-smooth">
        <header class="nav-bg shadow-lg sticky top-0 z-40">
            <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-1 text-2xl font-serif font-bold tracking-wider cursor-pointer" @click="router.visit('/')">
                    <span>FLORA</span>
                    <img src="/images/image_8bd93d.png" alt="logo" class="logo-icon">
                    <span>FLEUR</span>
                </div>
                <div class="hidden md:flex items-center space-x-8 text-sm tracking-widest uppercase">
                    <a href="#features" class="hover:text-gray-300 transition duration-300">FEATURES</a>
                    <a href="#how-it-works" class="hover:text-gray-300 transition duration-300">HOW IT WORKS</a>
                    <a href="#bestsellers" class="hover:text-gray-300 transition duration-300">BESTSELLERS</a>
                    <a href="#reviews" class="hover:text-gray-300 transition duration-300">REVIEWS</a>
                    <a href="#about" class="hover:text-gray-300 transition duration-300">ABOUT US</a>
                </div>
                <div>
                    <button @click="openModal('shop-signup')" class="hidden md:block bg-white text-gray-800 text-xs font-semibold py-2 px-4 rounded-full hover:bg-gray-200 transition duration-300">
                        Register as Shop
                    </button>
                    <button @click="showMobileMenu = !showMobileMenu" class="md:hidden text-xl">
                        <i class="fas" :class="showMobileMenu ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </nav>
            <div v-if="showMobileMenu" class="md:hidden bg-gray-800 py-4 px-6 border-t border-gray-700 animate-fade-in">
                <div class="flex flex-col space-y-4">
                    <a href="#features" @click="showMobileMenu = false">FEATURES</a>
                    <a href="#how-it-works" @click="showMobileMenu = false">HOW IT WORKS</a>
                    <a href="#bestsellers" @click="showMobileMenu = false">BESTSELLERS</a>
                    <a href="#reviews" @click="showMobileMenu = false">REVIEWS</a>
                    <a href="#about" @click="showMobileMenu = false">ABOUT US</a>
                    <button @click="openModal('shop-signup')" class="bg-white text-gray-800 text-xs font-semibold py-2 px-4 rounded-full w-40">Register as Shop</button>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <section class="py-16 md:py-24 flex items-center relative">
                <div class="container mx-auto px-6 flex justify-end">
                    <div class="w-full md:w-1/2 lg:w-2/5 text-center md:text-left animate-fade-in">
                        <h1 class="text-6xl lg:text-7xl font-serif font-bold tracking-wide hero-text-shadow">FLORA FLEUR</h1>
                        <p class="font-serif text-2xl lg:text-3xl mt-4 hero-text-shadow">Refresh your space with lush greenery and exquisite flower bouquets</p>
                        <p class="text-sm mt-4 leading-relaxed max-w-md mx-auto md:mx-0">
                            Create the perfect indoor jungle with our bold houseplants, blooming plants, hanging plants, and more! We connect you with the best local florists and plant shops.
                        </p>
                        <div class="mt-8 space-y-4 flex flex-col items-center md:items-start font-bold uppercase">
                            <button @click="openModal('signin')" class="btn-gray py-3 px-10 rounded-full w-48 transition transform hover:scale-105">LOG IN</button>
                            <span class="text-sm lowercase font-normal">or New here?</span>
                            <button @click="openModal('signup')" class="btn-green py-3 px-10 rounded-full w-48 transition transform hover:scale-105">SIGN UP</button>
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="py-16 bg-black bg-opacity-20">
                <div class="container mx-auto px-6">
                    <h2 class="text-4xl font-serif font-bold text-center mb-12">Why Choose Flora Fleur?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden feature-card p-6 text-center">
                            <div class="text-4xl mb-4 text-green-300">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Fresh & Local</h3>
                            <p class="text-gray-300">We partner with local growers and florists to bring you the freshest plants and flowers available.</p>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden feature-card p-6 text-center">
                            <div class="text-4xl mb-4 text-green-300">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h3 class="text-xl font-semibold mb-2">Fast Delivery</h3>
                            <p class="text-gray-300">Get your plants and flowers delivered quickly with our reliable delivery network.</p>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden feature-card p-6 text-center">
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
                        <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden feature-card">
                            <img src="https://images.unsplash.com/photo-1485955900006-10f4d324d411?q=80&w=2070" alt="Monstera Plant" class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Monstera Deliciosa</h3>
                                <p class="text-gray-300 mb-4">The iconic Swiss cheese plant with beautiful split leaves.</p>
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-lg font-bold">₱1,200</span>
                                    <button @click="triggerToast('Item added to cart!')" class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden feature-card">
                            <img src="https://images.unsplash.com/photo-1563241527-3004b7be0ffd?q=80&w=2070" alt="Rose Bouquet" class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Premium Rose Bouquet</h3>
                                <p class="text-gray-300 mb-4">A stunning arrangement of fresh red roses perfect for any occasion.</p>
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-lg font-bold">₱1,800</span>
                                    <button @click="triggerToast('Item added to cart!')" class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden feature-card">
                            <img src="https://images.unsplash.com/photo-1598880940086-4b62797e8e8c?q=80&w=2070" alt="Snake Plant" class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">Snake Plant</h3>
                                <p class="text-gray-300 mb-4">A low-maintenance plant that purifies the air and thrives in low light.</p>
                                <div class="flex justify-between items-center mt-4">
                                    <span class="text-lg font-bold">₱850</span>
                                    <button @click="triggerToast('Item added to cart!')" class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">Add to Cart</button>
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
                        <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden feature-card p-6">
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
                        <div class="bg-white bg-opacity-10 rounded-lg overflow-hidden feature-card p-6">
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
        </main>

        <footer class="bg-gray-900 py-8">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center gap-1 text-xl font-serif font-bold mb-4 md:mb-0">
                        <span>FLORA</span><img src="/images/image_8bd93d.png" class="w-5 h-5"><span>FLEUR</span>
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

        <div class="fixed bottom-10 left-10 p-6 rounded-lg bottom-left-bg space-y-4 z-30 animate-slide-in">
            <button @click="openModal('shop-signup')" class="block w-full text-left font-semibold hover:text-gray-300 transition">Sign Up As New Store</button>
            <button @click="openModal('admin')" class="block w-full text-left font-semibold hover:text-gray-300 uppercase">Log In As Admin</button>
        </div>

        <div v-if="activeModal" class="fixed inset-0 modal-overlay z-50 flex items-center justify-center p-4" @click.self="closeModal">
            
            <div v-if="activeModal === 'signin' || activeModal === 'admin'" class="w-full max-w-md p-8 md:p-12 rounded-2xl form-container-bg relative animate-fade-in text-white">
                <button @click="closeModal" class="absolute top-4 right-4 text-2xl text-white hover:text-gray-300 transition">&times;</button>
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-wider text-white mb-2">{{ activeModal === 'admin' ? 'ADMIN LOGIN' : 'SIGN IN' }}</h1>
                    <div class="flex items-center justify-center gap-1 text-lg font-serif tracking-wider text-white">
                        <span>FLORA</span>
                        <img src="/images/image_8bd93d.png" alt="logo" class="logo-icon w-5 h-5">
                        <span>FLEUR</span>
                    </div>
                </div>
                <form @submit.prevent="submitLogin" class="mt-10 space-y-8">
                    <div v-if="Object.keys(loginForm.errors).length > 0" class="text-red-400 text-sm">
                        <ul class="list-disc list-inside">
                            <li v-for="error in loginForm.errors" :key="error">{{ error }}</li>
                        </ul>
                    </div>
                    <div class="relative">
                        <label for="signin-email" class="text-sm font-medium text-gray-200">Email</label>
                        <input v-model="loginForm.email" id="signin-email" type="email" placeholder="Email" class="w-full form-input-style" required>
                        <i class="fa-regular fa-envelope absolute right-2 top-8 text-gray-400"></i>
                    </div>
                    <div class="relative">
                        <label for="signin-password" class="text-sm font-medium text-gray-200">Password</label>
                        <input v-model="loginForm.password" id="signin-password" type="password" placeholder="Password" class="w-full form-input-style" required>
                        <i class="fa-solid fa-lock absolute right-2 top-8 text-gray-400"></i>
                    </div>
                    <div v-if="activeModal === 'signin'" class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <input v-model="loginForm.remember" id="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 bg-transparent accent-[#8C5A63]">
                            <label for="remember-me" class="ml-2 block text-gray-300">Remember me</label>
                        </div>
                        <a href="#" class="font-medium text-gray-300 hover:text-white transition">Forgot Password?</a>
                    </div>
                    <div v-if="activeModal === 'signin'" class="flex items-center text-sm">
                        <input v-model="loginForm.login_as_vendor" id="login-as-vendor" type="checkbox" class="h-4 w-4 rounded border-gray-300 bg-transparent accent-[#8C5A63]">
                        <label for="login-as-vendor" class="ml-2 block text-gray-300">Log in as Vendor</label>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-full font-bold btn-maroon transition transform hover:scale-105">LOG IN</button>
                    <div class="text-center text-sm space-y-2">
                        <span v-if="activeModal === 'signin'" class="text-gray-300">or</span>
                        <span v-else-if="activeModal === 'signup'" class="text-gray-300">Already have an account?</span>
                        <div><button v-if="activeModal === 'signin'" @click.prevent="openModal('signup')" class="font-medium text-white hover:underline transition">Sign up here</button></div>
                        <div><button v-if="activeModal === 'signup'" @click.prevent="openModal('signin')" class="font-medium text-white hover:underline transition">Log in here</button></div>
                        <div><button v-if="activeModal === 'admin'" @click.prevent="openModal('signin')" class="font-medium text-white hover:underline transition">Back to user login</button></div>
                    </div>
                </form>
            </div>

            <div v-if="activeModal === 'signup'" class="w-full max-w-md p-8 md:p-12 rounded-2xl form-container-bg relative animate-fade-in text-white">
                <button @click="closeModal" class="absolute top-4 right-4 text-2xl text-white hover:text-gray-300 transition">&times;</button>
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-wider text-white mb-2">SIGN UP</h1>
                    <div class="flex items-center justify-center gap-1 text-lg font-serif tracking-wider text-white">
                        <span>FLORA</span>
                        <img src="/images/image_8bd93d.png" alt="logo" class="logo-icon w-5 h-5">
                        <span>FLEUR</span>
                    </div>
                </div>
                <form @submit.prevent="submitSignup" class="mt-10 space-y-8">
                    <div v-if="Object.keys(signupForm.errors).length > 0" class="text-red-400 text-sm">
                        <ul class="list-disc list-inside">
                            <li v-for="error in signupForm.errors" :key="error">{{ error }}</li>
                        </ul>
                    </div>
                    <div class="relative">
                        <label for="signup-name" class="text-sm font-medium text-gray-200">Name</label>
                        <input v-model="signupForm.name" id="signup-name" type="text" placeholder="Name" class="w-full form-input-style" required>
                    </div>
                    <div class="relative">
                        <label for="signup-email" class="text-sm font-medium text-gray-200">Email</label>
                        <input v-model="signupForm.email" id="signup-email" type="email" placeholder="Email" class="w-full form-input-style" required>
                        <i class="fa-regular fa-envelope absolute right-2 top-8 text-gray-400"></i>
                    </div>
                    <div class="relative">
                        <label for="signup-password" class="text-sm font-medium text-gray-200">Password</label>
                        <input v-model="signupForm.password" id="signup-password" type="password" placeholder="Password" class="w-full form-input-style" required>
                        <i class="fa-solid fa-lock absolute right-2 top-8 text-gray-400"></i>
                    </div>
                    <div class="relative">
                        <label for="signup-password-confirm" class="text-sm font-medium text-gray-200">Confirm Password</label>
                        <input v-model="signupForm.password_confirmation" id="signup-password-confirm" type="password" placeholder="Confirm Password" class="w-full form-input-style" required>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-full font-bold btn-maroon transition transform hover:scale-105">SIGN UP</button>
                    <div class="text-center text-sm space-y-2">
                        <span class="text-gray-300">Already have an account?</span>
                        <div><button @click.prevent="openModal('signin')" class="font-medium text-white hover:underline transition">Log in here</button></div>
                    </div>
                </form>
            </div>

            <div v-if="activeModal === 'shop-signup'" class="w-full max-w-2xl max-h-[90vh] overflow-y-auto p-8 md:p-12 rounded-2xl form-container-bg relative text-white animate-fade-in">
                <button @click="closeModal" class="absolute top-4 right-4 text-2xl text-white hover:text-gray-300 transition">&times;</button>
                <h1 class="text-5xl font-serif font-bold text-center mb-2">Create Your Shop</h1>
                <p class="text-center text-gray-300 mb-8">Just fill out a quick form to get started!</p>
                
                <form @submit.prevent="submitStoreSignup" class="space-y-6">
                    <div v-if="Object.keys(storeSignupForm.errors).length > 0" class="mb-4 text-red-400 text-sm">
                        <ul class="list-disc list-inside">
                            <li v-for="error in storeSignupForm.errors" :key="error">{{ error }}</li>
                        </ul>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-lg font-semibold mb-3">Shop Owner Information:</h2>
                                <div class="space-y-4">
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Name</label>
                                        <input v-model="storeSignupForm.owner_name" type="text" class="w-full form-input-style" placeholder="Full name">
                                    </div>
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Email</label>
                                        <input v-model="storeSignupForm.email" type="email" class="w-full form-input-style" placeholder="Email address">
                                    </div>
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Password</label>
                                        <input v-model="storeSignupForm.password" type="password" class="w-full form-input-style" placeholder="Min. 8 characters">
                                    </div>
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Confirm Password</label>
                                        <input v-model="storeSignupForm.password_confirmation" type="password" class="w-full form-input-style" placeholder="Re-type password">
                                    </div>
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Phone Number</label>
                                        <input v-model="storeSignupForm.owner_phone" type="tel" class="w-full form-input-style" placeholder="Phone number">
                                    </div>
                                </div>
                            </div>
                             <div>
                                <h2 class="text-lg font-semibold mb-3">Shop Details:</h2>
                                <div class="space-y-4">
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Shop Name</label>
                                        <input v-model="storeSignupForm.shop_name" type="text" class="w-full form-input-style" placeholder="Your shop name">
                                    </div>
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Shop Description</label>
                                        <textarea v-model="storeSignupForm.shop_description" class="w-full form-input-style h-24" placeholder="Describe your shop"></textarea>
                                    </div>
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Shop Phone/Tel. Number</label>
                                        <input v-model="storeSignupForm.shop_phone" type="tel" class="w-full form-input-style" placeholder="Shop contact number">
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
                                        <input v-model="storeSignupForm.address" type="text" class="w-full form-input-style" placeholder="Street address">
                                    </div>
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Zip Code</label>
                                        <input v-model="storeSignupForm.zip_code" type="text" class="w-full form-input-style" placeholder="Zip code">
                                    </div>
                                    <div class="relative">
                                        <label class="text-sm font-medium text-gray-200">Delivery Coverage Area</label>
                                        <input v-model="storeSignupForm.delivery_coverage" type="text" class="w-full form-input-style" placeholder="Areas you deliver to">
                                    </div>
                                </div>
                            </div>
                             <div>
                                <h2 class="text-lg font-semibold mb-3">Business Verification:</h2>
                                <div class="relative">
                                     <label for="file-upload" class="form-input-style w-full flex items-center justify-between cursor-pointer text-gray-300 py-3 px-0 border-b border-gray-400">
                                        <span id="file-label-text">{{ fileLabelText }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                    </label>
                                    <input id="file-upload" type="file" @change="handleFileSelect" class="sr-only">
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

        <div v-if="showToast" :class="`fixed bottom-5 right-5 ${toastType === 'error' ? 'bg-red-600' : 'bg-green-700'} text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center animate-fade-in` ">
            <i class="fas fa-check-circle mr-2"></i><span>{{ toastMessage }}</span>
        </div>
    </div>
</template>

<style scoped>
/* Exact CSS ported from landing.css */
.landing-page-wrapper {
    background: url('/images/image_7f3485.jpg') no-repeat center center/cover;
    background-attachment: fixed;
    font-family: 'Inter', sans-serif;
}
.font-serif { font-family: 'Cormorant Garamond', serif; }
.nav-bg { background-color: rgba(68, 78, 65, 0.95); backdrop-filter: blur(10px); }
.btn-green { background-color: #8BAE86; color: #333; }
.btn-gray { background-color: #D9D9D9; color: #333; }
.bottom-left-bg { background-color: rgba(0,0,0,0.3); backdrop-filter: blur(5px); }
.modal-overlay { background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); }
.form-container-bg { background-color: rgba(68, 78, 65, 0.9); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(25px); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3); }
.btn-maroon { background-color: #8C5A63; }
.btn-dark { background-color: #3a4235; }
.form-input-style { background-color: transparent; border-bottom: 1px solid rgba(255, 255, 255, 0.3); padding: 0.5rem 0; width: 100%; color: white; transition: 0.3s; }
.form-input-style:focus { outline: none; border-bottom-color: #8C5A63; }
.feature-card { background-color: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); transition: 0.3s; }
.feature-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); }
.hero-text-shadow { text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3); }
.animate-fade-in { animation: fadeIn 1s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-in { animation: slideIn 0.8s ease-out; }
@keyframes slideIn { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
.logo-icon { width: 50px; height: 50px; object-fit: contain; }
</style>