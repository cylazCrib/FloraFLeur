<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\VendorController;
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

// 2. VENDOR DASHBOARD
// Protected by 'auth' and 'vendor' middleware (ensure your middleware is set up)
Route::middleware(['auth'])->group(function () {
    Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
    Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
     Route::post('/customer/order', [CustomerController::class, 'storeOrder'])->name('customer.order.store');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
   
});

// 3. PROFILE MANAGEMENT (Default Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/register-shop', [RegisteredShopController::class, 'store'])->name('shop.register');


require __DIR__.'/auth.php';