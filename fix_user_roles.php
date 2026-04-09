<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(User::all() as $u) {
    $normalized = strtolower($u->role);
    if ($u->role !== $normalized) {
        $u->role = $normalized;
        $u->save();
        echo "Fixed role for user ID {$u->id}: {$normalized}\n";
    }
}
echo "Roles normalization completed.\n";
