<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    shopName: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const activeView = ref('dashboard-view');

const setActiveView = (view) => {
    activeView.value = view;
    // Emit event if needed or just handle locally in a single-page approach
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
                <nav class="navigation">
                    <ul>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'dashboard-view' }]" 
                               @click="activeView = 'dashboard-view'">
                                <i class="fa-solid fa-table-cells-large"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'requests-view' }]" 
                               @click="activeView = 'requests-view'">
                                <i class="fa-solid fa-envelope-open-text"></i> Requests
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'orders-view' }]" 
                               @click="activeView = 'orders-view'">
                                <i class="fa-solid fa-truck-fast"></i> Orders & Deliveries
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'inventory-view' }]" 
                               @click="activeView = 'inventory-view'">
                                <i class="fa-solid fa-box-archive"></i> Inventory
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'products-view' }]" 
                               @click="activeView = 'products-view'">
                                <i class="fa-solid fa-boxes-stacked"></i> Manage Products
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'staff-view' }]" 
                               @click="activeView = 'staff-view'">
                                <i class="fa-solid fa-user-group"></i> Manage Staff
                            </a>
                        </li>
                        <li>
                            <a :class="['nav-item', { active: activeView === 'gmail-view' }]" 
                               @click="activeView = 'gmail-view'">
                                <i class="fa-brands fa-google"></i> Gmail
                            </a>
                        </li>
                         <li>
                            <a :class="['nav-item', { active: activeView === 'settings-view' }]" 
                               @click="activeView = 'settings-view'">
                                <i class="fa-solid fa-gear"></i> Settings
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
/* Import vendor CSS or include it here if preferred. 
   Since the user wants to exact replicate, I'll make sure vendor.css is loaded.
*/
@import '@/../../resources/css/vendor.css';

/* Override body styles from vendor.css to work with Vue components */
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
