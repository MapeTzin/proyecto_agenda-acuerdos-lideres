<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();
echo "Total users: " . count($users) . "\n\n";

foreach ($users as $u) {
    $roleId = $u->role_id ?? 'N/A';
    $rolStr = $u->attributes['rol'] ?? 'N/A';
    $isAdmin = (
        in_array(strtolower($u->email), ['soporte@mapetzin.com', 'direccion@mapetzin.com', 'v.arochi@mapetzin.com']) ||
        $roleId == 3 ||
        in_array(strtolower($rolStr), ['admin', 'administrador']) ||
        $u->hasRole('administrador') ||
        $u->hasRole('admin')
    );

    echo sprintf(
        "User: %-30s | Email: %-30s | Area: %-22s | RoleID: %3s | Rol: %-12s | isAdmin: %s\n",
        $u->name,
        $u->email,
        $u->area,
        $roleId,
        $rolStr,
        $isAdmin ? 'YES (ADMIN)' : 'NO (AREA USER)'
    );
}
