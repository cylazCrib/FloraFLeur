<?php

use App\Models\User;
use App\Models\Shop;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. FIX ADMIN
$admin = User::where('email', 'admin@florafleur.com')->first();
if ($admin) {
    $admin->update(['role' => 'admin']);
    echo "Admin role fixed.\n";
} else {
    echo "Admin user not found.\n";
}

// 2. FIX VENDOR
$vendor = User::where('email', 'vendor2@example.com')->first();
if ($vendor) {
    $vendor->update(['role' => 'vendor']);
    $shop = Shop::firstOrCreate(
        ['user_id' => $vendor->id],
        [
            'name' => 'My Flower Shop',
            'description' => 'A beautiful shop',
            'phone' => '0987654321',
            'address' => '123 Street',
            'status' => 'approved'
        ]
    );
    $shop->update(['status' => 'approved']);
    echo "Vendor role and shop fixed.\n";
} else {
    echo "Vendor user not found.\n";
}

echo "Data fix completed.\n";
