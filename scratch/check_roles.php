<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');
$roles = $db->table('roles')->get();
echo "=== ROLES IN SISTEMA_TICKETS ===\n";
print_r($roles);

$users = $db->table('users')->select('id', 'name', 'email', 'department')->limit(10)->get();
echo "\n=== SAMPLE USERS ===\n";
print_r($users);
