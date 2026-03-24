<template>
    <div class="bg-white/5 p-8 rounded-[2rem] border border-white/10 mb-6 shadow-xl backdrop-blur-md">
        <div class="flex justify-between border-b border-white/5 pb-4 mb-6">
            <div>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">
                    {{ item.type === 'request' ? 'Custom Request' : 'Standard Order' }} #{{ item.order_number }}
                </p>
                <span class="px-3 py-1 rounded-full text-[9px] uppercase font-bold" 
                      :class="item.status === 'approved' || item.status === 'completed' ? 'bg-green-500/20 text-green-400' : 'bg-orange-500/20 text-orange-400'">
                    {{ item.status }}
                </span>
            </div>
            <div class="text-right text-[10px] opacity-40 uppercase tracking-tighter">{{ item.created_at }}</div>
        </div>

        <div class="space-y-4">
            <template v-if="item.items && item.items.length > 0">
                <div v-for="(prod, index) in item.items" :key="index" class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/10 rounded-xl overflow-hidden shadow-lg border border-white/5">
                            <img v-if="prod.image" :src="prod.image" class="w-full h-full object-cover">
                            <div v-else class="w-full h-full flex items-center justify-center bg-[#4A4A3A]">
                                <i class="fa-solid fa-image text-white opacity-20"></i>
                            </div>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-white">{{ prod.name }}</p>
                            <p class="text-[11px] text-[#86A873]">{{ formatPrice(prod.price) }} × {{ prod.quantity }}</p>
                        </div>
                    </div>
                    <span class="font-bold text-sm text-gray-300">{{ formatPrice(prod.price * prod.quantity) }}</span>
                </div>
            </template>

            <template v-else>
                <div class="bg-white/5 p-4 rounded-xl border border-white/5 italic text-sm text-gray-300">
                    "{{ item.description || 'No description provided.' }}"
                </div>
            </template>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-end md:items-center pt-6 mt-4 border-t border-white/5 gap-4">
            <div v-if="item.driver_name" class="flex items-center gap-3 bg-[#86A873]/10 px-4 py-2 rounded-full border border-[#86A873]/20">
                <i class="fa-solid fa-motorcycle text-[#86A873] text-xs"></i>
                <span class="text-xs font-bold text-white">{{ item.driver_name }}</span>
            </div>
            <div v-else class="text-[10px] italic opacity-30">Awaiting Driver Assignment</div>

            <div class="text-right">
                <p class="text-[10px] uppercase font-bold opacity-40 mb-1">Total Amount</p>
                <p class="text-3xl font-bold text-[#86A873] tracking-tighter">{{ formatPrice(item.total_amount) }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    item: Object
});

const formatPrice = (p) => {
    const val = parseFloat(p || 0);
    return '₱' + val.toLocaleString(undefined, { minimumFractionDigits: 2 });
};
</script>