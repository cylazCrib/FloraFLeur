<template>
    <div class="bg-white/10 p-4 rounded-2xl flex items-center justify-between border border-white/5 backdrop-blur-md">
        <div class="flex items-center gap-4">
            <img :src="item.image" class="w-20 h-20 rounded-xl object-cover shadow-lg">
            <div><h4 class="font-bold text-lg text-white">{{ item.name }}</h4><p class="text-gray-300">Qty: {{ item.qty }}</p></div>
        </div>
        <div class="text-right">
            <p class="font-bold text-xl text-[#86A873]">{{ formatPrice(item.price * item.qty) }}</p>
            <button @click="$emit('remove', item.id)" class="text-red-400 bg-transparent border-none cursor-pointer text-xs mt-2 uppercase font-bold hover:underline">Remove</button>
        </div>
    </div>
</template>

<script setup>
defineProps(['item']);
const formatPrice = (p) => {
    const val = parseFloat(p || 0);
    return '₱' + val.toLocaleString(undefined, { minimumFractionDigits: 2 });
};
</script>