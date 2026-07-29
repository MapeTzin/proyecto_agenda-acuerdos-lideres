<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$users = $db->table('users')->get();

echo "=== USERS MATCHING STAFF OR JEFATURA ===\n";
foreach ($users as $u) {
    $str = json_encode($u);
    if (stripos($str, 'staff') !== false || stripos($str, 'jefatura') !== false) {
        echo $str . "\n\n";
    }
}
