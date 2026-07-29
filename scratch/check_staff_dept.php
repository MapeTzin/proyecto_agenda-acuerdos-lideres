<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$staffUsers = $db->table('users')
    ->where('department_id', 17)
    ->orWhere('position', 'LIKE', '%staff%')
    ->orWhere('email', 'g.gonzalez@mapetzin.com')
    ->get();

echo "=== JEFATURA DE STAFF USERS ===\n";
foreach ($staffUsers as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Position: {$u->position} | RoleID: {$u->role_id} | Rol: {$u->rol}\n";
}
