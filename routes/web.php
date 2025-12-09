<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\RegisteredShopController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VendorProductController;
use App\Http\Controllers\VendorInventoryController;
use App\Http\Controllers\VendorOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Public Landing Page
Route::get('/', [LandingPageController::class, 'show'])->name('landing');

// 2. Public Shop Registration
Route::post('/register-shop', [RegisteredShopController::class, 'store'])->name('shop.register');


// 3. Admin Routes
// [FIX] Removed the function causing the error. 
// We now rely on AdminController to handle the security check.
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/registrations', [AdminController::class, 'registrations'])->name('registrations.index');
    Route::patch('/registrations/{shop}/approve', [AdminController::class, 'approveShop'])->name('registrations.approve');
    Route::delete('/registrations/{shop}/reject', [AdminController::class, 'rejectShop'])->name('registrations.reject');
    Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors.index');
    Route::patch('/vendors/{shop}/toggle', [AdminController::class, 'toggleShopStatus'])->name('vendors.toggle');
    Route::get('/owners', [AdminController::class, 'owners'])->name('owners.index');
    Route::post('/owners/notify', [AdminController::class, 'notifyOwners'])->name('owners.notify');
    Route::get('/gmail', [AdminController::class, 'gmail'])->name('gmail.index');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
    Route::post('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.password');
});



// 4. Vendor Routes
Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');

    // --- PRODUCTS ---
    Route::post('/products', [VendorController::class, 'storeProduct'])->name('products.store');
    Route::post('/products/{id}/update', [VendorController::class, 'updateProduct'])->name('products.update');
    // Fix: Add the missing update route
    Route::post('/products/{id}/update', [VendorController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [VendorController::class, 'destroyProduct'])->name('products.destroy');

    // --- INVENTORY ---
    Route::post('/inventory', [VendorController::class, 'storeInventory'])->name('inventory.store');
    // Fix: Add the missing update route
    Route::post('/inventory/{id}/update', [VendorController::class, 'updateInventory'])->name('inventory.update');
    Route::delete('/inventory/{id}', [VendorController::class, 'destroyInventory'])->name('inventory.destroy');

    // --- STAFF ---
    Route::post('/staff', [VendorController::class, 'storeStaff'])->name('staff.store');
    Route::post('/staff/{id}/update', [VendorController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/{id}', [VendorController::class, 'destroyStaff'])->name('staff.destroy');
    // --- ORDERS ---
    Route::post('/orders/manual', [VendorController::class, 'storeManualOrder'])->name('orders.manual');
    Route::patch('/orders/{order}/status', [VendorController::class, 'updateOrderStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/assign', [VendorController::class, 'assignDriver'])->name('orders.assign');
});

// ... (Keep the rest of your routes) ...


// 5. Customer Routes
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::post('/order', [CustomerController::class, 'storeOrder'])->name('order.store');
    Route::post('/request', [CustomerController::class, 'storeRequest'])->name('request.store');
    Route::patch('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
});

// 6. Default Auth Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

