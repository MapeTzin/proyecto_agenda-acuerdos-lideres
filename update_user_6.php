<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(6);
if ($user) {
    $user->name = 'Alejandro Mendoza Chavarría';
    $user->email = 'a.mendoza@mapetzin.com';
    $user->password = \Illuminate\Support\Facades\Hash::make('1234');
    $user->save();
    echo "User 6 updated successfully.\n";
} else {
    echo "User 6 not found.\n";
}
