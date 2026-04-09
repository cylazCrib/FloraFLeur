  <script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    totalSales: Number,
    totalOrders: Number,
    pendingOrders: Number,
    deliveredOrders: Number,
    inventoryCount: Number,
    lowStockCount: Number,
    recentOrders: Array,
    orders: Array,
    products: Array,
    inventory: Array,
    items: Array,
    flowers: Array,
    staff: Array,
    drivers: Array,
    customRequests: Array,
    error: String,
});

const page = usePage();
const shop = computed(() => page.props.auth.user.shop);

// --- State for Modals ---
const showOrderModal = ref(false);
const showProductModal = ref(false);
const showInventoryModal = ref(false);
const showStaffModal = ref(false);
const showOrderDetailsModal = ref(false);
const showRequestDetailsModal = ref(false);

const selectedOrder = ref(null);
const selectedRequest = ref(null);
const productModalTitle = ref('Add New Product');
const inventoryModalTitle = ref('Add Item');

// --- Forms ---
const productForm = useForm({
    product_id: '',
    name: '',
    category: 'bouquet',
    occasion: '',
    description: '',
    price: '',
    image: null,
});

const inventoryForm = useForm({
    item_id: '',
    type: 'item',
    name: '',
    code: '',
    quantity: '',
});

const staffForm = useForm({
    staff_id: '',
    name: '',
    email: '',
    phone: '',
    role: 'Manager',
});

const manualOrderForm = useForm({
    customer_name: '',
    customer_phone: '',
    delivery_address: '',
    product_name: '',
    total_amount: '',
    payment_method: 'Cash',
});

const quoteForm = useForm({
    request_id: '',
    vendor_quote: '',
    quote_notes: '',
});

const paymentQRForm = useForm({
    email: shop.value?.email || '',
    payment_instructions: shop.value?.payment_instructions?.join('\n') || '',
    gcash_qr: null,
    maya_qr: null,
});

const reportForm = useForm({
    type: 'Sales Summary',
    period: 'today',
});

const announcementForm = useForm({
    title: '',
    message: '',
    target: 'Everyone',
});

const gmailForm = useForm({
    gmail_email: '',
    app_password: '',
});

// --- Methods ---

const openProductModal = (product = null) => {
    if (product) {
        productModalTitle.value = 'Edit Product';
        productForm.product_id = product.id;
        productForm.name = product.name;
        productForm.category = product.category;
        productForm.occasion = product.occasion || '';
        productForm.description = product.description;
        productForm.price = product.price;
    } else {
        productModalTitle.value = 'Add New Product';
        productForm.reset();
    }
    showProductModal.value = true;
};

const openInventoryModal = (item = null, type = 'item') => {
    if (item) {
        inventoryModalTitle.value = 'Edit ' + item.type;
        inventoryForm.item_id = item.id;
        inventoryForm.type = item.type;
        inventoryForm.name = item.name;
        inventoryForm.code = item.code;
        inventoryForm.quantity = item.quantity;
    } else {
        inventoryModalTitle.value = 'Add ' + type;
        inventoryForm.reset();
        inventoryForm.type = type;
    }
    showInventoryModal.value = true;
};

const openStaffModal = (member = null) => {
    if (member) {
        staffForm.staff_id = member.id;
        staffForm.name = member.name;
        staffForm.email = member.email;
        staffForm.phone = member.phone;
        staffForm.role = member.role;
    } else {
        staffForm.reset();
    }
    showStaffModal.value = true;
};

const viewOrderDetails = (order) => {
    selectedOrder.value = order;
    showOrderDetailsModal.value = true;
};

const viewRequestDetails = (request) => {
    selectedRequest.value = request;
    quoteForm.request_id = request.id;
    quoteForm.vendor_quote = request.vendor_quote || '';
    showRequestDetailsModal.value = true;
};

// --- API Calls ---

const submitProduct = () => {
    const isUpdate = !!productForm.product_id;
    const url = isUpdate ? `/vendor/products/${productForm.product_id}` : '/vendor/products';

    productForm.transform((data) => ({
        ...data,
        ...(isUpdate ? { _method: 'PUT' } : {}),
    })).post(url, {
        forceFormData: true,
        onSuccess: () => { showProductModal.value = false; productForm.reset(); }
    });
};

// Use the same pattern for Inventory and Staff (Replace axios with form.post)
const submitInventory = () => {
    inventoryForm.post('/vendor/inventory', {
        onSuccess: () => { showInventoryModal.value = false; inventoryForm.reset(); }
    });
};
const submitStaff = () => {
    axios.post('/vendor/staff', staffForm.data()).then(res => {
        alert(res.data.message);
        showStaffModal.value = false;
        router.reload();
    });
};

const submitManualOrder = () => {
    axios.post('/vendor/orders/manual', manualOrderForm.data()).then(res => {
        alert(res.data.message);
        showOrderModal.value = false;
        router.reload();
    });
};

const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(`/vendor/products/${id}`, {
            onSuccess: () => {
                // Success! The list will refresh automatically.
            },
            onFinish: () => {
                // This runs regardless of success/fail
            }
        });
    }
};

const deleteInventory = (id) => {
    if (confirm('Are you sure?')) {
        axios.delete(`/vendor/inventory/${id}`).then(res => {
            alert(res.data.message);
            router.reload();
        });
    }
};

const deleteStaff = (id) => {
    if (confirm('Are you sure?')) {
        axios.delete(`/vendor/staff/${id}`).then(res => {
            alert(res.data.message);
            router.reload();
        });
    }
};

const updateOrderStatus = (orderId, status) => {
    if (!confirm(`Update status to "${status}"?`)) return;
    axios.patch(`/vendor/orders/${orderId}/status`, { status }).then(() => {
        router.reload();
    });
};

const assignDriver = (orderId, driverName) => {
    if (!driverName) return alert('Select driver');
    axios.patch(`/vendor/orders/${orderId}/assign`, { driver_name: driverName }).then(() => {
        router.reload();
    });
};

const submitQuote = () => {
    axios.post(`/vendor/requests/${selectedRequest.value.id}/quote`, quoteForm.data()).then(res => {
        alert(res.data.message);
        router.reload();
    });
};

const approveQuote = () => {
    if (!confirm('Are you sure you want to approve this quote and send it to the customer?')) return;
    axios.patch(`/vendor/requests/${selectedRequest.value.id}/approve`).then(res => {
        alert(res.data.message);
        showRequestDetailsModal.value = false;
        router.reload();
    });
};

const submitPaymentQR = () => {
    let data = new FormData();
    Object.keys(paymentQRForm.data()).forEach(key => {
        data.append(key, paymentQRForm[key]);
    });
    
    axios.post('/vendor/settings/payment-qr', data).then(res => {
        alert(res.data.message);
        router.reload();
    }).catch(err => {
        alert('Error: ' + (err.response?.data?.error || 'Failed to save settings'));
    });
};

const generateReport = () => {
    alert('Generating report for ' + reportForm.period);
    // You could implement the actual PDF generation logic here if you have a route for it
};

const postAnnouncement = () => {
    alert('Announcement posted: ' + announcementForm.title);
    announcementForm.reset();
};

const connectGmail = () => {
    alert('Gmail connected: ' + gmailForm.gmail_email);
    gmailForm.reset();
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(val);
};

const occasions = ['Birthday', 'Anniversary', 'Valentines', 'Mothers Day', 'Graduation', 'Funeral', 'Just Because'];

const getStatusClass = (status) => {
    return 'status-' + status.toLowerCase().replace(/\s+/g, '-');
};
</script>

<template>
    <Head title="Vendor Dashboard" />

    <VendorLayout :shop-name="shop?.name">
        <template #default="{ activeView }">
            
            <!-- Dashboard View -->
            <main v-if="activeView === 'dashboard-view'" class="view active-view">
                <header class="main-header"><h1>G'DAY, {{ shop?.name || 'SHOP' }}!</h1></header>
                <section class="summary-cards">
                    <div class="card card-sales" style="position: relative;">
                        <h2>Total Sales</h2>
                        <p class="card-main-value">{{ formatCurrency(totalSales) }}</p>
                        <a :href="route('vendor.sales.export')" 
                           class="download-btn"
                           title="Download Sales Report (CSV)">
                           <i class="fa-solid fa-file-arrow-down"></i>
                        </a>
                    </div>
                    <div class="card card-orders"><h2>Orders</h2><p class="card-main-value">{{ totalOrders }}</p></div>
                    <div class="card card-inventory">
                        <h2>Inventory</h2>
                        <p class="card-main-value">{{ inventoryCount }} items</p>
                        <p class="card-sub-value" :style="lowStockCount > 0 ? 'color:#D32F2F;font-weight:bold;' : ''">
                            Low Stock: {{ lowStockCount }}
                        </p>
                    </div>
                </section>
                <section class="content-container">
                    <div class="content-header"><h2>Recent Orders</h2></div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr v-for="order in recentOrders" :key="order.id">
                                    <td>#{{ order.order_number }}</td>
                                    <td>{{ order.customer_name }}</td>
                                    <td>{{ formatCurrency(order.total_amount) }}</td>
                                    <td><span :class="['status', getStatusClass(order.status)]">{{ order.status }}</span></td>
                                </tr>
                                <tr v-if="recentOrders.length === 0">
                                    <td colspan="4" class="text-center p-4">No recent orders.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Requests View -->
            <main v-if="activeView === 'requests-view'" class="view active-view">
                <header class="main-header"><h1>CUSTOMER REQUESTS</h1></header>
                <section class="content-container">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Date</th><th>Customer</th><th>Occasion</th><th>Needed</th><th>Budget</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr v-for="req in customRequests" :key="req.id">
                                    <td>{{ new Date(req.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</td>
                                    <td>{{ req.user.name }}<br><small class="text-gray-500">{{ req.contact_number }}</small></td>
                                    <td>{{ req.occasion || 'Custom' }}</td>
                                    <td>{{ req.date_needed ? new Date(req.date_needed).toLocaleDateString() : '-' }}</td>
                                    <td class="font-bold text-[#86A873]">{{ formatCurrency(req.budget) }}</td>
                                    <td>
                                        <span class="status" :class="getStatusClass(req.status)">
                                            {{ req.status.charAt(0).toUpperCase() + req.status.slice(1) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button @click="viewRequestDetails(req)" class="table-action-btn view-request-btn text-blue-600 font-bold">
                                            <i class="fa-solid fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="customRequests.length === 0">
                                    <td colspan="7" class="text-center p-4">No custom requests yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Orders View -->
            <main v-if="activeView === 'orders-view'" class="view active-view">
                <header class="main-header"><h1>MANAGE ORDERS</h1></header>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header">
                        <h2>All Orders</h2>
                        <div style="display:flex; gap:10px;">
                            <button class="action-button" @click="router.reload()">Refresh</button>
                            <button class="action-button" @click="showOrderModal = true">+ Manual Order</button>
                        </div>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>ID</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr v-for="o in orders" :key="o.id">
                                    <td>#{{ o.order_number }}</td>
                                    <td>{{ o.customer_name }}<br><small>{{ o.customer_phone }}</small></td>
                                    <td>{{ o.items.map(i => i.quantity + 'x ' + i.product_name).join(', ') }}</td>
                                    <td>{{ formatCurrency(o.total_amount) }}</td>
                                    <td>
                                        <select v-model="o.status" @change="updateOrderStatus(o.id, o.status)" class="border p-1 rounded text-sm bg-white">
                                            <option value="Pending">Pending</option>
                                            <option value="Being Made">Being Made</option>
                                            <option value="Out for Delivery">Out for Delivery</option>
                                            <option value="Delivered">Delivered</option>
                                        </select>
                                    </td>
                                    <td class="flex gap-2">
                                         <button class="text-blue-600 hover:text-blue-800" @click="viewOrderDetails(o)"><i class="fa-regular fa-eye"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <section class="content-container">
                    <div class="content-header"><h2>Delivery Management</h2></div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Order #</th><th>Address</th><th>Driver</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr v-for="o in orders.filter(ord => !['Delivered', 'Completed', 'Canceled'].includes(ord.status))" :key="'del-'+o.id">
                                    <td>#{{ o.order_number }}</td><td>{{ o.delivery_address }}</td>
                                    <td>
                                        <select v-model="o.driver_name" @change="assignDriver(o.id, o.driver_name)" class="border p-1 rounded text-sm bg-white">
                                            <option value="">Select Driver</option>
                                            <option v-for="d in drivers" :key="d.id" :value="d.name">{{ d.name }}</option>
                                        </select>
                                    </td>
                                    <td class="flex gap-2">
                                        <a :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(o.delivery_address)" target="_blank" class="text-gray-500 hover:text-red-500" title="Map">
                                            <i class="fa-solid fa-map-location-dot"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Products View -->
            <main v-if="activeView === 'products-view'" class="view active-view">
                <header class="main-header"><h1>PRODUCTS</h1></header>
                <section class="content-container">
                    <div class="content-header"><h2>List</h2><button class="action-button" @click="openProductModal()">+ Product</button></div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Img</th><th>Name</th><th>Category</th><th>Description</th><th>Price</th><th>Action</th></tr></thead>
                          <tbody>
                                <tr v-for="p in products" :key="p.id">
                                    <td>
                                        <td>
                                            <img :src="p.image" 
                                                width="50" 
                                                class="rounded shadow-sm border border-gray-200 object-cover h-12 w-12"
                                                loading="lazy">
                                        </td>
                                    </td>
                                    <td class="font-bold">{{ p.name }}</td>
                                    <td><span class="badge">{{ p.category }}</span></td>
                                    <td class="text-gray-500 italic">{{ p.description.substring(0, 30) }}...</td>
                                    <td class="font-bold text-[#86A873]">{{ formatCurrency(p.price) }}</td>
                                    <td class="flex gap-2">
                                        <button @click="openProductModal(p)" class="text-blue-600 hover:text-blue-800 transition">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <button @click="deleteProduct(p.id)" class="text-red-500 hover:text-red-700 transition">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="products.length === 0">
                                    <td colspan="6" class="text-center py-10 text-gray-400">No products found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Inventory View -->
            <main v-if="activeView === 'inventory-view'" class="view active-view">
                <header class="main-header"><h1>INVENTORY</h1></header>
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header"><h2>Items Inventory</h2><button class="action-button" @click="openInventoryModal(null, 'item')">+ Add Item</button></div>
                    <div class="inventory-list">
                        <div v-for="i in items" :key="i.id" class="inventory-item flex justify-between items-center p-4 bg-white border border-gray-100 rounded-lg shadow-sm mb-2">
                            <div>
                                <div class="font-semibold text-gray-800">{{ i.code ? i.code + ' - ' : '' }}{{ i.name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ i.quantity }} pcs remaining 
                                    <span v-if="i.quantity <= 5" class="text-red-600 font-bold ml-2">Low Stock!</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button @click="openInventoryModal(i)" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen"></i></button>
                                <button @click="deleteInventory(i.id)" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="content-container">
                    <div class="content-header"><h2>Flowers Inventory</h2><button class="action-button" @click="openInventoryModal(null, 'flower')">+ Add Flower</button></div>
                    <div class="inventory-list">
                        <div v-for="i in flowers" :key="i.id" class="inventory-item flex justify-between items-center p-4 bg-white border border-gray-100 rounded-lg shadow-sm mb-2">
                            <div>
                                <div class="font-semibold text-gray-800">{{ i.code ? i.code + ' - ' : '' }}{{ i.name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ i.quantity }} pcs remaining 
                                    <span v-if="i.quantity <= 5" class="text-red-600 font-bold ml-2">Low Stock!</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button @click="openInventoryModal(i)" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen"></i></button>
                                <button @click="deleteInventory(i.id)" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <!-- Staff View -->
            <main v-if="activeView === 'staff-view'" class="view active-view">
                <header class="main-header"><h1>STAFF</h1></header>
                <section class="content-container">
                    <div class="content-header"><h2>My Team</h2><button class="action-button" @click="openStaffModal()">+ Add Staff</button></div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr v-for="s in staff" :key="s.id">
                                    <td>{{ s.name }}</td><td>{{ s.email }}</td><td>{{ s.role }}</td>
                                    <td><span class="status status-active">Active</span></td>
                                    <td class="flex gap-2">
                                        <button @click="openStaffModal(s)" class="text-blue-600 hover:text-blue-800"><i class="fa-regular fa-pen-to-square"></i></button>
                                        <button @click="deleteStaff(s.id)" class="text-red-500 hover:text-red-700"><i class="fa-regular fa-trash-can"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <!-- Settings View -->
            <main v-if="activeView === 'settings-view'" class="view active-view">
                <header class="main-header"><h1>Settings</h1></header>
                
                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header"><h2>Payment Settings - E-Wallet QR Codes</h2></div>
                    <form @submit.prevent="submitPaymentQR" class="styled-form">
                        <div class="form-group">
                            <label>Shop Email</label>
                            <input v-model="paymentQRForm.email" type="email" required>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                            <div>
                                <div class="form-group">
                                    <label>GCash QR Code</label>
                                    <input type="file" @input="paymentQRForm.gcash_qr = $event.target.files[0]" accept="image/*" />
                                </div>
                                <div v-if="shop?.gcash_qr_url" class="qr-preview">
                                    <p>Current GCash QR:</p>
                                    <img :src="shop.gcash_qr_url" alt="GCash QR">
                                </div>
                            </div>
                            <div>
                                <div class="form-group">
                                    <label>Maya QR Code</label>
                                    <input type="file" @input="paymentQRForm.maya_qr = $event.target.files[0]" accept="image/*" />
                                </div>
                                <div v-if="shop?.maya_qr_url" class="qr-preview">
                                    <p>Current Maya QR:</p>
                                    <img :src="shop.maya_qr_url" alt="Maya QR">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Payment Instructions</label>
                            <textarea v-model="paymentQRForm.payment_instructions" rows="3"></textarea>
                        </div>

                        <button type="submit" class="action-button">Save Payment Settings</button>
                    </form>
                </section>

                <section class="content-container" style="margin-bottom: 2rem;">
                    <div class="content-header"><h2>Reports & Analytics</h2></div>
                    <form @submit.prevent="generateReport" class="form-grid">
                        <div class="form-group"><label>Report Type</label><input v-model="reportForm.type" readonly></div>
                        <div class="form-group"><label>Period</label>
                            <select v-model="reportForm.period">
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                        <button type="submit" class="action-button full-width">Generate PDF</button>
                    </form>
                </section>

                <section class="content-container">
                    <div class="content-header"><h2>System Announcements</h2></div>
                    <form @submit.prevent="postAnnouncement" class="styled-form">
                        <div class="form-group"><label>Title</label><input v-model="announcementForm.title" required></div>
                        <div class="form-group"><label>Message</label><textarea v-model="announcementForm.message" rows="3" required></textarea></div>
                        <div class="form-group"><label>Target Audience</label>
                            <select v-model="announcementForm.target">
                                <option value="Florists">All Florists</option>
                                <option value="Drivers">All Drivers</option>
                                <option value="Everyone">Everyone</option>
                            </select>
                        </div>
                        <button type="submit" class="action-button">Post Announcement</button>
                    </form>
                </section>
            </main>

            <!-- Gmail View -->
            <main v-if="activeView === 'gmail-view'" class="view active-view">
                <header class="main-header"><h1>Gmail Integration</h1></header>
                <section class="content-container form-container" style="max-width: 600px;">
                    <div class="content-header"><h2>Email Configuration</h2></div>
                    <p>Connect your gmail account to send automated notifications.</p>
                    <form @submit.prevent="connectGmail" class="styled-form">
                        <div class="form-group"><label>Gmail Address</label><input v-model="gmailForm.gmail_email" type="email" required></div>
                        <div class="form-group"><label>App Password</label><input v-model="gmailForm.app_password" type="password" required></div>
                        <button type="submit" class="action-button">Connect Gmail</button>
                    </form>
                </section>
            </main>

            <!-- Modals (Manual Order, Product, Inventory, Staff, Details) same as before -->
            
            <!-- Manual Order Modal -->
            <div v-if="showOrderModal" class="modal-overlay" style="display: flex;">
                <div class="modal-content">
                    <button class="modal-close-btn" @click="showOrderModal = false"> × </button>
                    <h2 class="modal-title" style="margin-bottom: 2rem;">Manual Order</h2>
                    <form @submit.prevent="submitManualOrder" class="styled-form">
                        <div class="form-group"><label>Customer Name</label><input v-model="manualOrderForm.customer_name" required></div>
                        <div class="form-group"><label>Phone</label><input v-model="manualOrderForm.customer_phone" required></div>
                        <div class="form-group"><label>Address</label><input v-model="manualOrderForm.delivery_address" required></div>
                        <div class="form-group"><label>Product Name</label><input v-model="manualOrderForm.product_name" required></div>
                        <div class="form-group"><label>Total (₱)</label><input v-model="manualOrderForm.total_amount" type="number" required></div>
                        <div class="form-group"><label>Payment</label>
                            <select v-model="manualOrderForm.payment_method">
                                <option value="Cash">Cash</option>
                                <option value="G-Cash">G-Cash</option>
                            </select>
                        </div>
                        <button type="submit" class="action-button">Create</button>
                    </form>
                </div>
            </div>

            <!-- Product Modal -->
            <div v-if="showProductModal" class="modal-overlay" style="display: flex;">
                <div class="modal-content">
                    <button class="modal-close-btn" @click="showProductModal = false"> &times; </button>
                    <h2 class="modal-title" style="margin-bottom: 2rem;">{{ productModalTitle }}</h2>
                    <form @submit.prevent="submitProduct" class="styled-form">
                        <div class="form-group"><label>Name</label><input v-model="productForm.name" required></div>
                        <div class="form-group">
                            <label>Category</label>
                            <select v-model="productForm.category" required>
                                <option value="bouquet">Bouquet Flowers</option>
                                <option value="box">Flower Boxes</option>
                                <option value="standee">Standee Flowers</option>
                                <option value="potted">Potted Plants</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Occasion</label>
                            <select v-model="productForm.occasion" required>
                                <option value="" disabled>Select Occasion</option>
                                <option v-for="occ in occasions" :key="occ" :value="occ">{{ occ }}</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Description</label><textarea v-model="productForm.description" rows="3" required></textarea></div>
                        <div class="form-group"><label>Price (₱)</label><input v-model="productForm.price" type="number" step="0.01" required></div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" @input="productForm.image = $event.target.files[0]" accept="image/*">
                        </div>
                        <div class="form-actions"><button type="submit" class="action-button">Save Product</button></div>
                    </form>
                </div>
            </div>

            <!-- Inventory Modal -->
            <div v-if="showInventoryModal" class="modal-overlay" style="display: flex;">
                <div class="modal-content">
                    <button class="modal-close-btn" @click="showInventoryModal = false">×</button>
                    <h2>{{ inventoryModalTitle }}</h2>
                    <form @submit.prevent="submitInventory" class="styled-form">
                        <div class="form-group"><label>Name</label><input v-model="inventoryForm.name" required></div>
                        <div class="form-group"><label>Code</label><input v-model="inventoryForm.code"></div>
                        <div class="form-group"><label>Qty</label><input v-model="inventoryForm.quantity" type="number" required></div>
                        <button class="action-button">Save</button>
                    </form>
                </div>
            </div>

            <!-- Staff Modal -->
            <div v-if="showStaffModal" class="modal-overlay" style="display: flex;">
                <div class="modal-content">
                    <button class="modal-close-btn" @click="showStaffModal = false">×</button>
                    <h2>Staff Member</h2>
                    <form @submit.prevent="submitStaff" class="styled-form">
                        <div class="form-group"><label>Name</label><input v-model="staffForm.name" required></div>
                        <div class="form-group"><label>Email</label><input v-model="staffForm.email" type="email" required></div>
                        <div class="form-group"><label>Phone</label><input v-model="staffForm.phone" required></div>
                        <div class="form-group"><label>Role</label>
                            <select v-model="staffForm.role">
                                <option value="Manager">Manager</option>
                                <option value="Florist">Florist</option>
                                <option value="Driver">Driver</option>
                            </select>
                        </div>
                        <button class="action-button">Save</button>
                    </form>
                </div>
            </div>

            <!-- Order Details Modal -->
            <div v-if="showOrderDetailsModal" class="modal-overlay" style="display: flex;">
                <div class="modal-content">
                    <button class="modal-close-btn" @click="showOrderDetailsModal = false"> × </button>
                    <h2 class="modal-title">Order Details</h2>
                    <div v-if="selectedOrder" class="details-content">
                        <h3 style="font-size:1.2rem; font-weight:bold; margin-bottom:10px;">Order Details</h3>
                        <p><strong>Customer:</strong> {{ selectedOrder.customer_name }}</p>
                        <p><strong>Phone:</strong> {{ selectedOrder.customer_phone }}</p>
                        <p><strong>Address:</strong> {{ selectedOrder.delivery_address }}</p>
                        <p><strong>Payment Method:</strong> <span class="badge">{{ selectedOrder.payment_method }}</span></p>
                        <p v-if="selectedOrder.payment_reference"><strong>Payment Ref:</strong> <span class="text-blue-600 font-bold">{{ selectedOrder.payment_reference }}</span></p>
                        <hr style="margin:10px 0;">
                        <p><strong>Items:</strong> {{ selectedOrder.items.map(i => i.quantity + 'x ' + i.product_name).join(', ') }}</p>
                        <p><strong>Total:</strong> {{ formatCurrency(selectedOrder.total_amount) }}</p>
                        <p><strong>Status:</strong> {{ selectedOrder.status }}</p>
                    </div>
                </div>
            </div>

            <!-- Request Details Modal -->
            <div v-if="showRequestDetailsModal" class="modal-overlay" style="display: flex;">
                <div class="modal-content" style="max-width: 600px;">
                    <button class="modal-close-btn" @click="showRequestDetailsModal = false">×</button>
                    <h2 class="modal-title">Request Details</h2>
                    <div v-if="selectedRequest">
                        <div class="req-section">
                            <h4>Customer Information</h4>
                            <p><strong>Name:</strong> {{ selectedRequest.user.name }}</p>
                            <p><strong>Email:</strong> {{ selectedRequest.user.email }}</p>
                            <p><strong>Phone:</strong> {{ selectedRequest.contact_number }}</p>
                        </div>
                        <div class="req-section">
                            <h4>Request Details</h4>
                            <p><strong>Occasion:</strong> {{ selectedRequest.occasion || 'Not specified' }}</p>
                            <p><strong>Date Needed:</strong> {{ selectedRequest.date_needed ? new Date(selectedRequest.date_needed).toLocaleString() : '-' }}</p>
                            <p><strong>Budget:</strong> {{ formatCurrency(selectedRequest.budget) }}</p>
                            <p><strong>Color Preference:</strong> {{ selectedRequest.color_preference || 'Not specified' }}</p>
                            <p><strong>Description:</strong></p>
                            <p class="description-box">{{ selectedRequest.description }}</p>
                        </div>
                        
                        <div v-if="selectedRequest.vendor_quote" class="quote-display">
                            <h4>Your Quote</h4>
                            <p class="quote-amount">{{ formatCurrency(selectedRequest.vendor_quote) }}</p>
                        </div>

                        <div v-if="selectedRequest.status === 'pending'" class="quote-form-section">
                            <h3>Submit Your Quote</h3>
                            <form @submit.prevent="submitQuote" class="styled-form">
                                <div class="form-group">
                                    <label>Your Quote Price (₱)</label>
                                    <input v-model="quoteForm.vendor_quote" type="number" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label>Notes (Optional)</label>
                                    <textarea v-model="quoteForm.quote_notes" rows="3"></textarea>
                                </div>
                                <button type="submit" class="action-button">Send Quote</button>
                            </form>
                        </div>

                        <button v-if="selectedRequest.status === 'reviewing' && selectedRequest.vendor_quote" 
                                @click="approveQuote" class="action-button approve-btn">
                            Approve & Send to Customer
                        </button>
                    </div>
                </div>
            </div>

        </template>
    </VendorLayout>
</template>

<style scoped>
.download-btn {
    position: absolute; 
    top: 15px; 
    right: 15px; 
    font-size: 1.2rem; 
    color: #4A4A3A; 
    opacity: 0.6; 
    transition: opacity 0.2s;
}
.download-btn:hover { opacity: 1; }

.badge {
    background-color:#86A873; 
    color:white; 
    padding:3px 8px; 
    border-radius:4px; 
    font-weight:bold;
}

.req-section {
    background-color: #f9f9f9; 
    padding: 1rem; 
    border-radius: 8px; 
    margin-bottom: 1rem;
}
.req-section h4 { margin: 0 0 0.5rem 0; font-weight: bold; }
.req-section p { margin: 0.5rem 0; }

.description-box {
    padding: 0.5rem; 
    background-color: white; 
    border-left: 3px solid #86A873; 
    border-radius: 4px;
}

.quote-display {
    background-color: #e8f5e9; 
    padding: 1rem; 
    border-radius: 8px; 
    border-left: 4px solid #4CAF50; 
    margin-bottom: 1rem;
}
.quote-display h4 { margin: 0 0 0.5rem 0; font-weight: bold; color: #2e7d32; }
.quote-amount { margin: 0.5rem 0; font-size: 1.5rem; font-weight: bold; color: #1b5e20; }

.approve-btn {
    width: 100%; 
    margin-top: 1rem; 
    background-color: #4CAF50; 
    font-weight: bold;
}

.qr-preview {
    margin-top: 1rem; 
    border: 1px solid #ddd; 
    padding: 1rem; 
    border-radius: 8px;
}
.qr-preview img {
    max-width: 150px; 
    height: auto; 
    border-radius: 8px;
}
</style>
