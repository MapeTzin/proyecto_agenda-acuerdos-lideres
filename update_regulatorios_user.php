<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updated = \App\Models\User::where('area', 'LIKE', '%ASUNTOS REGULATORIOS%')->update([
    'name' => 'Alejandro Mendoza Chavarría',
    'email' => 'a.mendoza@mapetzin.com',
    'password' => \Illuminate\Support\Facades\Hash::make('1234')
]);

echo "Updated $updated users.\n";
