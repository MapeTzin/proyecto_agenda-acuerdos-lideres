<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'direccion@mapetzin.com')->first();
if ($user) {
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Area: {$user->area}\n";
    echo "Areas: " . implode(', ', $user->areas) . "\n";
    echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
} else {
    echo "direccion@mapetzin.com not found!\n";
}
