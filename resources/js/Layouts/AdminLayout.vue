<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    adminName: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const activeView = ref('dashboard-view');

const setActiveView = (view) => {
    activeView.value = view;
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="admin-vendor-layout-wrapper">
        <div class="dashboard-container">
            <aside class="sidebar">
                <div class="logo">
                    <span>FLORA</span>
                    <img src="/images/image_8bd93d.png" class="logo-icon" alt="Logo">
                    <span>FLEUR</span>
                </div>
                <div class="text-center mb-6">
                    <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Administrator</p>
                </div>
                <nav class="navigation">
                    <ul>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'dashboard-view' }]" 
                               @click="activeView = 'dashboard-view'">
                                <i class="fa-solid fa-gauge"></i> Overview
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'registrations-view' }]" 
                               @click="activeView = 'registrations-view'">
                                <i class="fa-solid fa-store"></i> Shop Requests
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'vendors-view' }]" 
                               @click="activeView = 'vendors-view'">
                                <i class="fa-solid fa-users-gears"></i> Manage Vendors
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'orders-view' }]" 
                               @click="activeView = 'orders-view'">
                                <i class="fa-solid fa-cart-shopping"></i> All Orders
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'customers-view' }]" 
                               @click="activeView = 'customers-view'">
                                <i class="fa-solid fa-user-group"></i> Customers
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'settings-view' }]" 
                               @click="activeView = 'settings-view'">
                                <i class="fa-solid fa-sliders"></i> Settings
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="sidebar-bottom">
                    <ul>
                        <li>
                            <button @click="logout" class="nav-item" id="logout-btn" style="width:100%;text-align:left;border:none;background:none;color:#D32F2F;">
                                <i class="fa-solid fa-right-from-bracket"></i> Log Out
                            </button>
                        </li>
                    </ul>
                </div>
            </aside>

            <div class="main-content">
                <slot :active-view="activeView" :set-active-view="setActiveView"></slot>
            </div>
        </div>
    </div>
</template>

<style>
@import '@/../../resources/css/admin.css';

.admin-vendor-layout-wrapper {
    background: url('/images/image_7eb546.jpg') no-repeat center center/cover;
    background-attachment: fixed;
    min-height: 100vh;
    padding: 2rem;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Inter', sans-serif;
}

.nav-item {
    cursor: pointer;
}
</style>
