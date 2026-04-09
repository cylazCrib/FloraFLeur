<template>
    <div class="bg-white rounded-2xl overflow-hidden group relative shadow-lg hover:shadow-2xl transition-all">
        <button @click.stop="$emit('toggle-fav', product)" class="absolute top-3 right-3 z-10 bg-white/80 p-2.5 rounded-full border-none cursor-pointer">
            <i :class="isFav ? 'fa-solid text-red-500' : 'fa-regular text-gray-400'" class="fa-heart"></i>
        </button>
        <div class="h-64 overflow-hidden cursor-pointer" @click="$emit('open-modal', product)">
            <img :src="product.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        </div>
        <div class="p-6 text-center text-gray-900">
            <p class="text-[10px] text-[#86A873] uppercase font-bold tracking-widest mb-1">{{ product.shop_name }}</p>
            <h3 class="font-rosarivo text-lg mb-1">{{ product.name }}</h3>
            <p class="text-[#86A873] font-bold text-xl">{{ formatPrice(product.price) }}</p>
            <button @click="$emit('open-modal', product)" class="w-full bg-[#4A4A3A] text-white py-3 rounded-xl mt-4 hover:bg-[#86A873] transition-colors border-none cursor-pointer text-xs font-bold tracking-widest uppercase">Details</button>
        </div>
    </div>
</template>

<script setup>
defineProps(['product', 'isFav']);
const formatPrice = (p) => '₱' + (parseFloat(p) || 0).toLocaleString(undefined, { minimumFractionDigits: 2 });
</script>