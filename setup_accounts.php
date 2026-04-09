<?php

use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Setting up accounts...\n";

// 1. CREATE ADMIN
$adminEmail = 'admin@florafleur.com';
$admin = User::updateOrCreate(
    ['email' => $adminEmail],
    [
        'name' => 'System Admin',
        'password' => 'password',
        'role' => 'admin',
        'status' => 'Active'
    ]
);
echo "Admin Account Created:\n - Email: {$adminEmail}\n - Password: password\n\n";

// 2. CREATE VENDOR
$vendorEmail = 'vendor@example.com';
$vendor = User::updateOrCreate(
    ['email' => $vendorEmail],
    [
        'name' => 'John Vendor',
        'password' => 'password123',
        'role' => 'vendor',
        'status' => 'Active'
    ]
);

// 3. CREATE & APPROVE SHOP FOR VENDOR
$shop = Shop::updateOrCreate(
    ['user_id' => $vendor->id],
    [
        'name' => 'Flora Fleur Vendor Shop',
        'description' => 'Official vendor shop for testing.',
        'phone' => '09123456789',
        'address' => 'Vendor Street, Flower City',
        'status' => 'approved'
    ]
);
echo "Vendor Account Created:\n - Email: {$vendorEmail}\n - Password: password123\n - Shop: {$shop->name} (Approved)\n";

echo "\nSetup completed successfully.\n";
