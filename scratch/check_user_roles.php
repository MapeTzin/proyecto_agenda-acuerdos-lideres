<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');
$modelHasRoles = $db->table('model_has_roles')->limit(10)->get();
echo "=== MODEL HAS ROLES ===\n";
print_r($modelHasRoles);

$admins = \App\Models\User::all()->filter(function($u) {
    return $u->hasRole('administrador') || $u->hasRole('admin') || strtolower($u->email) === 'soporte@mapetzin.com' || strtolower($u->email) === 'direccion@mapetzin.com';
});

echo "\n=== ADMIN USERS COUNT: " . count($admins) . " ===\n";
foreach ($admins->take(10) as $adm) {
    echo " - ID: {$adm->id} | Name: {$adm->name} | Email: {$adm->email} | Area: {$adm->area}\n";
}
