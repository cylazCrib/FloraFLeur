<?php

use App\Models\User;
use App\Models\Shop;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'vendor@example.com')->first();
if ($user) {
    echo "User ID: {$user->id}, Role: {$user->role}\n";
    $shop = $user->shop;
    if ($shop) {
        echo "Shop Found: {$shop->name}, Status: {$shop->status}\n";
    } else {
        echo "No Shop Found for this user.\n";
    }
} else {
    echo "User not found.\n";
}
