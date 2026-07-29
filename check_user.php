<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 's.gema@mapetzin.com')->first();
if ($user) {
    echo "User found: " . json_encode($user->toArray()) . "\n";
    echo "Is active: " . $user->is_active . "\n";
    echo "Password check: " . (password_verify('Mapetzin23', $user->password) ? 'Match' : 'Mismatch') . "\n";
    echo "Password check using Hash::check: " . (\Illuminate\Support\Facades\Hash::check('Mapetzin23', $user->password) ? 'Match' : 'Mismatch') . "\n";
    echo "Current Hash: " . $user->password . "\n";
} else {
    echo "User not found\n";
}
