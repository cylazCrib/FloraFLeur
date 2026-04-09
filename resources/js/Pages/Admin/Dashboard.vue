<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    stats: Object,
    pendingShops: Array,
    activeShops: Array,
    allOrders: Array,
    allRequests: Array,
    allCustomers: Array,
    owners: Array,
});

const selectedShopActivity = ref(null);
const showActivityModal = ref(false);

const formatCurrency = (val) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(val);
};

const approveShop = (id) => {
    if (confirm('Approve this shop?')) {
        axios.post(`/admin/shops/${id}/approve`).then(res => {
            alert(res.data.message);
            router.reload();
        });
    }
};

const rejectShop = (id) => {
    if (confirm('Reject this shop? This will delete the request.')) {
        axios.post(`/admin/shops/${id}/reject`).then(res => {
            alert(res.data.message);
            router.reload();
        });
    }
};

const toggleShopStatus = (id) => {
    axios.post(`/admin/shops/${id}/toggle`).then(res => {
        alert(res.data.message);
        router.reload();
    });
};

const viewActivity = (id) => {
    axios.get(`/admin/shops/${id}/activity`).then(res => {
        selectedShopActivity.value = res.data;
        showActivityModal.value = true;
    });
};

const getStatusClass = (status) => {
    const s = status.toLowerCase();
    if (['active', 'approved', 'delivered', 'completed'].includes(s)) return 'status-approved';
    if (['pending', 'reviewing', 'being made', 'out for delivery'].includes(s)) return 'status-pending';
    if (['suspended', 'canceled', 'rejected'].includes(s)) return 'status-suspended';
    return '';
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #default="{ activeView }">
            
            <!-- Overview -->
            <main v-if="activeView === 'dashboard-view'" class="view active-view">
                <header class="main-header"><h1>SYSTEM OVERVIEW</h1></header>
                <section class="summary-cards">
                    <div class="card card-sales">
                        <h2>Total Revenue</h2>
                        <p class="card-main-value">{{ formatCurrency(stats.total_revenue) }}</p>
                        <p class="card-sub-value">From delivered orders</p>
                    </div>
                    <div class="card card-glass-dark">
                        <h2>Active Vendors</h2>
                        <p class="card-main-value">{{ stats.active_vendors }}</p>
                        <p class="card-sub-value">{{ stats.pending_shops }} pending requests</p>
                    </div>
                    <div class="card card-glass-light">
                        <h2>Total Customers</h2>
                        <p class="card-main-value">{{ stats.total_customers }}</p>
                    </div>
                    <div class="card card-glass-dark">
                        <h2>Total Orders</h2>
                        <p class="card-main-value">{{ stats.total_orders }}</p>
                    </div>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <section class="content-container">
                        <div class="content-header"><h2>Recent Orders</h2></div>
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead><tr><th>Order #</th><th>Shop</th><th>Amount</th><th>Status</th></tr></thead>
                                <tbody>
                                    <tr v-for="o in allOrders" :key="o.id">
                                        <td>#{{ o.order_number }}</td>
                                        <td>{{ o.shop?.name || 'N/A' }}</td>
                                        <td>{{ formatCurrency(o.total_amount) }}</td>
                                        <td><span :class="['status', getStatusClass(o.status)]">{{ o.status }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="content-container">
                        <div class="content-header"><h2>Recent Custom Requests</h2></div>
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead><tr><th>ID</th><th>Customer</th><th>Budget</th><th>Status</th></tr></thead>
                                <tbody>
                                    <tr v-for="r in allRequests" :key="r.id">
                                        <td>REQ-{{ r.id }}</td>
                                        <td>{{ r.user?.name }}</td>
                                        <td>{{ formatCurrency(r.budget) }}</td>
                                        <td><span :class="['status', getStatusClass(r.status)]">{{ r.status }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </main>

            <!-- Registrations -->
            <main v-if="activeView === 'registrations-view'" class="view active-view">
                <header class="main-header"><h1>SHOP REGISTRATIONS</h1></header>
                <section class="content-container">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Shop Name</th><th>Owner</th><th>Email</th><th>Applied</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr v-for="shop in pendingShops" :key="shop.id">
                                    <td>{{ shop.name }}</td>
                                    <td>{{ shop.user?.name }}</td>
                                    <td>{{ shop.user?.email }}</td>
                                    <td>{{ new Date(shop.created_at).toLocaleDateString() }}</td>
                                    <td>
                                        <button @click="approveShop(shop.id)" class="action-button green mr-2">Approve</button>
                                        <button @click="rejectShop(shop.id)" class="action-button red">Reject</button>
                                    </td>
                                </tr>
                                <tr v-if="pendingShops.length === 0"><td colspan="5" class="text-center p-8 text-gray-400 italic">No pending shop requests.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Vendors -->
            <main v-if="activeView === 'vendors-view'" class="view active-view">
                <header class="main-header"><h1>MANAGE VENDORS</h1></header>
                <section class="content-container">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Shop Name</th><th>Status</th><th>Products</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr v-for="shop in activeShops" :key="shop.id">
                                    <td>{{ shop.name }}</td>
                                    <td><span :class="['status', getStatusClass(shop.status)]">{{ shop.status }}</span></td>
                                    <td>{{ shop.products_count || 0 }}</td>
                                    <td>
                                        <button @click="viewActivity(shop.id)" class="table-action-btn">Activity</button>
                                        <button @click="toggleShopStatus(shop.id)" class="table-action-btn">
                                            {{ ['suspended', 'Suspended'].includes(shop.status) ? 'Unsuspend' : 'Suspend' }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- All Orders -->
            <main v-if="activeView === 'orders-view'" class="view active-view">
                <header class="main-header"><h1>ALL ORDERS</h1></header>
                <section class="content-container">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Date</th><th>Order #</th><th>Shop</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr v-for="o in allOrders" :key="o.id">
                                    <td>{{ new Date(o.created_at).toLocaleDateString() }}</td>
                                    <td>#{{ o.order_number }}</td>
                                    <td>{{ o.shop?.name }}</td>
                                    <td>{{ o.user?.name }}</td>
                                    <td>{{ formatCurrency(o.total_amount) }}</td>
                                    <td><span :class="['status', getStatusClass(o.status)]">{{ o.status }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Customers -->
            <main v-if="activeView === 'customers-view'" class="view active-view">
                <header class="main-header"><h1>CUSTOMERS</h1></header>
                <section class="content-container">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Name</th><th>Email</th><th>Joined</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr v-for="c in allCustomers" :key="c.id">
                                    <td>{{ c.name }}</td>
                                    <td>{{ c.email }}</td>
                                    <td>{{ new Date(c.created_at).toLocaleDateString() }}</td>
                                    <td>
                                        <button class="table-action-btn">Details</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Settings -->
            <main v-if="activeView === 'settings-view'" class="view active-view">
                <header class="main-header"><h1>SYSTEM SETTINGS</h1></header>
                <section class="content-container max-w-2xl">
                    <form @submit.prevent="" class="styled-form">
                        <div class="form-group">
                            <label>Platform Name</label>
                            <input value="Flora Fleur" readonly>
                        </div>
                        <div class="form-group">
                            <label>Admin Contact Email</label>
                            <input value="admin@florafleur.com">
                        </div>
                        <div class="form-group">
                            <label>Maintenance Mode</label>
                            <select><option>Off</option><option>On</option></select>
                        </div>
                        <button class="action-button">Save Configuration</button>
                    </form>
                </section>
            </main>

            <!-- Activity Modal -->
            <div v-if="showActivityModal" class="modal-overlay" style="display: flex;">
                <div class="modal-content" style="max-width: 600px;">
                    <button class="modal-close-btn" @click="showActivityModal = false">×</button>
                    <h2 class="modal-title mb-6">Shop Activity: {{ selectedShopActivity?.shop }}</h2>
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                        <div v-for="(act, idx) in selectedShopActivity?.activity" :key="idx" class="p-4 border rounded-xl bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-sm">{{ act.text }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase">{{ act.type }} • {{ act.date }}</p>
                                </div>
                                <span class="status text-[10px]">{{ act.status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </template>
    </AdminLayout>
</template>
