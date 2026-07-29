<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all(['id', 'name', 'area', 'email'])->toArray();
foreach ($users as $u) {
    if (stripos($u['area'] ?? '', 'regular') !== false || stripos($u['area'] ?? '', 'regulatorio') !== false || stripos($u['area'] ?? '', 'asuntos') !== false) {
        print_r($u);
    }
}
$user = \App\Models\User::where('name', 'LIKE', '%Alejandro Mendoza%')->first();
if ($user) {
    echo "Found user by name:\n";
    print_r($user->toArray());
}
