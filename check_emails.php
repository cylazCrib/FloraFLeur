<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::all();

echo "Email Casing Check:\n";
foreach ($users as $user) {
    echo "ID {$user->id}, Email: {$user->email}, Hex: " . bin2hex($user->email) . "\n";
}
