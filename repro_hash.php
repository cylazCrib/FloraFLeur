<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$password = 'password123';
$hashedManually = Hash::make($password);

$user = User::create([
    'name' => 'Test User',
    'email' => 'test_' . uniqid() . '@example.com',
    'password' => $hashedManually,
    'role' => 'customer',
]);

echo "Original Password: " . $password . "\n";
echo "Manually Hashed: " . $hashedManually . "\n";
echo "Model Password attribute: " . $user->password . "\n";

if (Hash::check($password, $user->password)) {
    echo "Check Passed: Password matches model attribute.\n";
} else {
    echo "Check Failed: Password does NOT match model attribute. Potential double hashing!\n";
    
    if (Hash::check($hashedManually, $user->password)) {
        echo "Check Confirmed: Model attribute matches the FIRST hash. Double hashing occurred!\n";
    }
}
