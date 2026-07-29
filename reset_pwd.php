<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 's.gema@mapetzin.com')->first();
if ($user) {
    $user->password = \Illuminate\Support\Facades\Hash::make('Mapetzin23');
    $user->save();
    echo "Password updated successfully.\n";
} else {
    echo "User not found.\n";
}
