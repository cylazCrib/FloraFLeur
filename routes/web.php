<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisteredShopController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

// 1. PUBLIC LANDING PAGE
// This now points to your LandingPageController which renders Landing.vue
Route::get('/', [LandingPageController::class, 'show'])->name('landing');

// 1.5. ROLE-BASED HOMEDASH
Route::middleware(['auth'])->get('/dashboard', [App\Http\Controllers\DashboardController::class, 'redirect'])->name('dashboard');

// 2. DASHBOARDS (ROLE-PROTECTED)
Route::middleware(['auth'])->group(function () {
    
    // ADMIN DASHBOARD
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/shops/{id}/activity', [AdminController::class, 'getOwnerActivity']);
        Route::post('/admin/shops/{id}/approve', [AdminController::class, 'approveShop']);
        Route::post('/admin/shops/{id}/reject', [AdminController::class, 'rejectShop']);
        Route::post('/admin/shops/{id}/toggle', [AdminController::class, 'toggleShopStatus']);
    });

    // VENDOR & STAFF DASHBOARD
    Route::middleware(['role:vendor,staff'])->group(function () {
        Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
        Route::get('/vendor/sales/export', [VendorController::class, 'exportSales'])->name('vendor.sales.export');
        
        // Products (Consolidated to VendorProductController)
Route::post('/vendor/products', [VendorProductController::class, 'store'])->name('vendor.products.store');
Route::put('/vendor/products/{product}', [VendorProductController::class, 'update'])->name('vendor.products.update');
Route::delete('/vendor/products/{product}', [VendorProductController::class, 'destroy'])->name('vendor.products.destroy');
        
        // Inventory
        Route::post('/vendor/inventory', [VendorController::class, 'storeInventory'])->name('vendor.inventory.store');
        Route::delete('/vendor/inventory/{id}', [VendorController::class, 'destroyInventory'])->name('vendor.inventory.destroy');
        
        // Staff
        Route::post('/vendor/staff', [VendorController::class, 'storeStaff'])->name('vendor.staff.store');
        Route::delete('/vendor/staff/{id}', [VendorController::class, 'destroyStaff'])->name('vendor.staff.destroy');
        
        // Orders
        Route::post('/vendor/orders/manual', [VendorController::class, 'storeManualOrder'])->name('vendor.orders.manual');
        Route::patch('/vendor/orders/{order}/status', [VendorController::class, 'updateOrderStatus'])->name('vendor.orders.status');
        Route::patch('/vendor/orders/{order}/assign', [VendorController::class, 'assignDriver'])->name('vendor.orders.assign');
        
        // Requests
        Route::patch('/vendor/requests/{id}/status', [VendorController::class, 'updateRequestStatus'])->name('vendor.requests.status');
        Route::post('/vendor/requests/{id}/quote', [VendorController::class, 'submitRequestQuote'])->name('vendor.requests.quote');
        Route::patch('/vendor/requests/{id}/approve', [VendorController::class, 'approveRequestQuote'])->name('vendor.requests.approve');
        
        // Settings
        Route::post('/vendor/settings/payment-qr', [VendorController::class, 'savePaymentQR'])->name('vendor.settings.payment-qr');
    });

    // CUSTOMER DASHBOARD
    Route::middleware(['role:customer'])->group(function () {
        Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
        Route::post('/customer/order', [CustomerController::class, 'storeOrder'])->name('customer.order.store');
        Route::post('/customer/request', [CustomerController::class, 'storeRequest'])->name('customer.request.store');
        Route::patch('/customer/profile', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
    });
});

// 3. PROFILE MANAGEMENT (Default Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/register-shop', [RegisteredShopController::class, 'store'])->name('shop.register');


require __DIR__.'/auth.php';