<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $mysqlUsers = DB::connection('sistema_tickets')->table('users')->get();

    echo "ID,Email,Name\n";
    foreach ($mysqlUsers as $user) {
        echo "{$user->id},{$user->email},{$user->name}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
