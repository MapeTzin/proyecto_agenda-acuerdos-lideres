<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \Illuminate\Support\Facades\DB::connection('mysql')
    ->table('sistema_tickets.users')
    ->where('email', 's.gema@mapetzin.com')
    ->first();

if ($user) {
    echo "User found: " . $user->email . "\n";
    // verify password hash
    $hash = $user->password;
    if (password_verify('Mape157527*', $hash)) {
        echo "Password Mape157527* is correct!\n";
    } elseif (password_verify('mape157527*', $hash)) {
        echo "Password mape157527* is correct!\n";
    } else {
        echo "Password does not match common tests.\n";
    }
} else {
    echo "User not found\n";
}
