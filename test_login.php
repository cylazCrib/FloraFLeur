<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'vendor@example.com';
$password = 'password123';

echo "Attempting login for {$email}...\n";

if (Auth::attempt(['email' => $email, 'password' => $password])) {
    $user = Auth::user();
    echo "Login successful! User ID: {$user->id}, Role: {$user->role}\n";
    
    // Simulate the dashboard redirect
    $response = (new App\Http\Controllers\DashboardController)->redirect();
    echo "Redirection target: " . $response->getTargetUrl() . "\n";
} else {
    echo "Login failed!\n";
}

$email2 = 'admin@florafleur.com';
$password2 = 'password';

echo "\nAttempting login for {$email2}...\n";

if (Auth::attempt(['email' => $email2, 'password' => $password2])) {
    $user = Auth::user();
    echo "Login successful! User ID: {$user->id}, Role: {$user->role}\n";
    
    // Simulate the dashboard redirect
    $response = (new App\Http\Controllers\DashboardController)->redirect();
    echo "Redirection target: " . $response->getTargetUrl() . "\n";
} else {
    echo "Login failed!\n";
}

$email3 = 'customer@example.com';
$password3 = 'password';

echo "\nAttempting login for {$email3}...\n";

if (Auth::attempt(['email' => $email3, 'password' => $password3])) {
    $user = Auth::user();
    echo "Login successful! User ID: {$user->id}, Role: {$user->role}\n";
    
    // Simulate the dashboard redirect
    $response = (new App\Http\Controllers\DashboardController)->redirect();
    echo "Redirection target: " . $response->getTargetUrl() . "\n";
} else {
    echo "Login failed!\n";
}
