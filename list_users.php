<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(User::all() as $u) {
    echo "ID: {$u->id}, Email: {$u->email}, Role: {$u->role}\n";
}
