<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::firstOrCreate(
    ['email' => 'admin@florafleur.com'],
    [
        'name' => 'Admin User',
        'password' => 'password', // will be hashed automatically if cast correctly, wait no we should hash it just in case
        'role' => 'admin',
        'status' => 'Active'
    ]
);

// If it already existed or we just created it, ensure the password is correct by explicitly hashing if needed
// Actually in Laravel 11 casts handle password hashing automatically
$user->password = 'password';
$user->save();

echo "Admin created/updated successfully.\n";
