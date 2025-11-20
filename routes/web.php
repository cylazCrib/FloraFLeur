<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// We don't need to import Auth here anymore since we moved the check to the controller

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
});


// 4. Vendor Routes
// [FIX] Added ->middleware(['auth']) so only logged-in users can enter
Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
    
    // --- PRODUCT CRUD ROUTES ---
    Route::get('/products', [VendorProductController::class, 'index'])->name('products.index');
    Route::post('/products', [VendorProductController::class, 'store'])->name('products.store');
    Route::post('/products/{product}/update', [VendorProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [VendorProductController::class, 'destroy'])->name('products.destroy');

    // --- INVENTORY ROUTES ---
    Route::get('/inventory', [App\Http\Controllers\VendorInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [App\Http\Controllers\VendorInventoryController::class, 'store'])->name('inventory.store');
    Route::post('/inventory/{inventory}/update', [App\Http\Controllers\VendorInventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [App\Http\Controllers\VendorInventoryController::class, 'destroy'])->name('inventory.destroy');
    
    // --- ORDER ROUTES ---
    Route::get('/orders', [App\Http\Controllers\VendorOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [VendorOrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\VendorOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/{order}', [VendorOrderController::class, 'show'])->name('orders.show');

    // ... existing routes ...
    
    // STAFF ROUTES
    Route::get('/staff', [App\Http\Controllers\VendorStaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [App\Http\Controllers\VendorStaffController::class, 'store'])->name('staff.store');
    Route::post('/staff/{staff}/update', [App\Http\Controllers\VendorStaffController::class, 'update'])->name('staff.update');
    Route::patch('/staff/{staff}/toggle', [App\Http\Controllers\VendorStaffController::class, 'toggleStatus'])->name('staff.toggle');
});



// 5. Customer Routes
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
});


// 6. Default Auth Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// --- TEMPORARY ORDER GENERATOR ---
Route::get('/generate-test-order', function () {
    // 1. Get the current vendor
    $user = Illuminate\Support\Facades\Auth::user();
    
    if (!$user) {
        return "ERROR: You are not logged in. Please log in as a Vendor first.";
    }

    if ($user->role !== 'vendor') {
        return "ERROR: You are logged in as '" . $user->role . "'. Please log in as a Vendor.";
    }

    // 2. Get their shop
    $shop = $user->shop;
    if (!$shop) {
        return "ERROR: You don't have a shop! Visit <a href='/fix-shop'>/fix-shop</a> first.";
    }

    // 3. Ensure we have a product
    $product = $shop->products()->first();
    if (!$product) {
        // Create one if missing
        $product = $shop->products()->create([
            'name' => 'Test Rose Bouquet',
            'description' => 'Generated for testing orders',
            'price' => 1500.00,
            'image' => null
        ]);
    }

    // 4. Create the Order
    try {
        $order = $shop->orders()->create([
            'user_id' => $user->id, // We'll link it to you for simplicity
            'order_number' => 'FF-' . rand(10000, 99999),
            'customer_name' => 'Juan Dela Cruz',
            'customer_phone' => '0917-123-4567',
            'customer_email' => 'juan@example.com',
            'delivery_address' => 'Unit 404, Flora Building, Makati City',
            'delivery_date' => now()->addDays(3),
            'total_amount' => 3000.00,
            'status' => 'Pending'
        ]);

        // 5. Add Items
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'price' => $product->price
        ]);

        return "SUCCESS! Created Order <b>#{$order->order_number}</b> for shop <b>'{$shop->name}'</b>. <br><br> <a href='/vendor/orders'>Go to Orders Page</a>";

    } catch (\Exception $e) {
        return "ERROR creating order: " . $e->getMessage();
    }
});
