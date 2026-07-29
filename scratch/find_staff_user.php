<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$users = $db->table('users')->get();

echo "=== USER LIST IN sistema_tickets ===\n";
foreach ($users as $u) {
    echo sprintf(
        "ID: %2d | Name: %-30s | Email: %-30s | Area: %-20s | RoleID: %s | Rol: %s\n",
        $u->id,
        $u->name ?? 'N/A',
        $u->email ?? 'N/A',
        $u->area ?? 'N/A',
        $u->role_id ?? 'N/A',
        $u->rol ?? 'N/A'
    );
}
