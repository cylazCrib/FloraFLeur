<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="relative w-full max-w-5xl bg-[#FDFDFB] rounded-[2.5rem] flex flex-col md:flex-row overflow-hidden text-gray-800 shadow-2xl animate-zoom">
            <button @click="$emit('close')" class="absolute top-6 right-8 text-gray-400 text-4xl font-light border-none bg-transparent cursor-pointer hover:text-red-500 transition z-10">&times;</button>
            <div class="w-full md:w-1/2 h-80 md:h-[600px] shadow-2xl"><img :src="product.image" class="w-full h-full object-cover"></div>
            <div class="w-full md:w-1/2 p-10 md:p-16 flex flex-col justify-center">
                <p class="text-[10px] text-[#86A873] uppercase font-bold tracking-widest mb-2">{{ product.shop_name }}</p>
                <h3 class="text-4xl md:text-5xl font-rosarivo mb-6 leading-tight">{{ product.name }}</h3>
                <p class="text-gray-500 mb-10 leading-relaxed font-light">{{ product.description || 'Premium arrangement handcrafted with Surigao blooms.' }}</p>
                <div class="flex justify-between items-center mb-10 border-y py-8 border-gray-100">
                    <span class="text-3xl font-bold tracking-tighter">{{ formatPrice(product.price) }}</span>
                    <div class="flex items-center gap-6 bg-gray-100 px-6 py-2 rounded-full border">
                        <button @click="qty > 1 ? qty-- : null" class="text-2xl font-light border-none bg-transparent cursor-pointer">－</button>
                        <span class="font-bold text-xl">{{ qty }}</span>
                        <button @click="qty++" class="text-2xl font-light border-none bg-transparent cursor-pointer">＋</button>
                    </div>
                </div>
                <div class="flex gap-4">
                    <button @click="$emit('add-to-cart', product, qty)" class="flex-1 bg-gray-200 py-4 rounded-2xl font-bold text-xs tracking-widest border-none cursor-pointer uppercase">Bag</button>
                    <button @click="$emit('buy-now', product, qty)" class="flex-1 bg-[#4A4A3A] text-white py-4 rounded-2xl font-bold text-xs tracking-widest shadow-xl border-none cursor-pointer uppercase">Buy Now</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
defineProps(['product']);
const qty = ref(1);
const formatPrice = (p) => {
    const val = parseFloat(p || 0);
    return '₱' + val.toLocaleString(undefined, { minimumFractionDigits: 2 });
};
</script>