<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'g.gonzalez@mapetzin.com')->first();

if ($user) {
    auth()->login($user);
    $comp = new \App\Livewire\KpisAreas();
    $isAdmin = $comp->checkIsAdmin();
    echo "User: {$user->name} ({$user->email})\n";
    echo "Position: {$user->position}\n";
    echo "RoleID: {$user->role_id} | Rol: {$user->rol}\n";
    echo "Is Admin Check Result: " . ($isAdmin ? "TRUE (ADMIN ACCESS GRANTED)" : "FALSE") . "\n";
} else {
    echo "User not found\n";
}
