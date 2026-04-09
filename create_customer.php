<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'customer@example.com';
$user = User::updateOrCreate(
    ['email' => $email],
    [
        'name' => 'Jane Customer',
        'password' => 'password',
        'role' => 'customer',
        'status' => 'Active',
        'phone' => '0987654321',
        'address' => 'Customer Lane, Flower City'
    ]
);

echo "Customer Account Created:\n - Email: {$email}\n - Password: password\n";
