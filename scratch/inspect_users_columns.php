<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');
$columns = $db->select('SHOW COLUMNS FROM users');
echo "=== USERS COLUMNS ===\n";
foreach ($columns as $col) {
    echo " - {$col->Field} ({$col->Type})\n";
}

$sampleUser = $db->table('users')->first();
echo "\n=== SAMPLE USER ===\n";
print_r($sampleUser);
