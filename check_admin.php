<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'vendor2@example.com';
$password = 'password123';
$user = User::where('email', $email)->first();

if ($user) {
    echo "User Found: ID {$user->id}, Email: {$user->email}, Role: {$user->role}\n";
    // Check if password works
    if (Illuminate\Support\Facades\Hash::check($password, $user->password)) {
        echo "Password '{$password}' matches correctly.\n";
    } else {
        echo "Password '{$password}' does NOT match.\n";
    }
} else {
    echo "User NOT Found: {$email}\n";
}
