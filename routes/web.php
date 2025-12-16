<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\RegisteredShopController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;
// [IMPORTANT] Add this new controller
use App\Http\Controllers\VendorProductController; 

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
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/owners/{id}/activity', [AdminController::class, 'getOwnerActivity'])->name('owners.activity');

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

    // --- [FIXED] PRODUCT ROUTES (Using VendorProductController) ---
    // Since the group name is 'vendor.', these names become 'vendor.products.store', etc.
    Route::post('/products', [VendorProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [VendorProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [VendorProductController::class, 'destroy'])->name('products.destroy');

    // Inventory
    Route::post('/inventory', [VendorController::class, 'storeInventory']);
    Route::post('/inventory/{id}/update', [VendorController::class, 'storeInventory']);
    Route::delete('/inventory/{id}', [VendorController::class, 'destroyInventory']);

    // Staff
    Route::post('/staff', [VendorController::class, 'storeStaff']);
    Route::post('/staff/{id}/update', [VendorController::class, 'storeStaff']);
    Route::delete('/staff/{id}', [VendorController::class, 'destroyStaff']);

    // Orders & Requests
    Route::post('/orders/manual', [VendorController::class, 'storeManualOrder']);
    Route::patch('/orders/{order}/status', [VendorController::class, 'updateOrderStatus']);
    Route::patch('/orders/{order}/assign', [VendorController::class, 'assignDriver']);
    Route::patch('/requests/{id}/status', [VendorController::class, 'updateRequestStatus']);
    Route::get('/sales/export', [VendorController::class, 'exportSales'])->name('sales.export');
});

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