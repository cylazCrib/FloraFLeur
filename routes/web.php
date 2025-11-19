<?php

use Illuminate\Support\Facades\Route;

// Import all the controllers we are using
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\RegisteredShopController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Public Landing Page (This is your real homepage)
// THIS IS THE FIX: It points '/' to your custom controller.
Route::get('/', [LandingPageController::class, 'show'])->name('landing');

// 2. Public Shop Registration Form Handler
Route::post('/register-shop', [RegisteredShopController::class, 'store'])->name('shop.register');


// 3. Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
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
});


// 4. Vendor Routes
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
});


// 5. Customer Routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
});


// 6. Default Breeze Auth Routes
// The default '/dashboard' route is removed, which is correct.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';