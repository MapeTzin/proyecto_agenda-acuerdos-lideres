<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$updated = $db->table('users')
    ->where('email', 'g.gonzalez@mapetzin.com')
    ->orWhere('position', 'LIKE', '%staff%')
    ->update([
        'role_id' => 3,
        'rol' => 'admin',
        'updated_at' => now(),
    ]);

echo "Updated {$updated} user(s) to admin role!\n";

$users = $db->table('users')
    ->where('email', 'g.gonzalez@mapetzin.com')
    ->orWhere('position', 'LIKE', '%staff%')
    ->get();

foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Position: {$u->position} | RoleID: {$u->role_id} | Rol: {$u->rol}\n";
}
