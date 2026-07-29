<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$conn = DB::connection('sistema_tickets');

$sql = [
    "CREATE TABLE IF NOT EXISTS spatie_permissions (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        guard_name varchar(255) NOT NULL,
        created_at timestamp NULL DEFAULT NULL,
        updated_at timestamp NULL DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY spatie_permissions_name_guard_name_unique (name, guard_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS spatie_roles (
        id bigint unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        guard_name varchar(255) NOT NULL,
        created_at timestamp NULL DEFAULT NULL,
        updated_at timestamp NULL DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY spatie_roles_name_guard_name_unique (name, guard_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS spatie_model_has_permissions (
        permission_id bigint unsigned NOT NULL,
        model_type varchar(255) NOT NULL,
        model_id bigint unsigned NOT NULL,
        PRIMARY KEY (permission_id, model_id, model_type),
        KEY spatie_model_has_permissions_model_id_model_type_index (model_id, model_type),
        CONSTRAINT spatie_model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES spatie_permissions (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS spatie_model_has_roles (
        role_id bigint unsigned NOT NULL,
        model_type varchar(255) NOT NULL,
        model_id bigint unsigned NOT NULL,
        PRIMARY KEY (role_id, model_id, model_type),
        KEY spatie_model_has_roles_model_id_model_type_index (model_id, model_type),
        CONSTRAINT spatie_model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES spatie_roles (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS spatie_role_has_permissions (
        permission_id bigint unsigned NOT NULL,
        role_id bigint unsigned NOT NULL,
        PRIMARY KEY (permission_id, role_id),
        KEY spatie_role_has_permissions_role_id_foreign (role_id),
        CONSTRAINT spatie_role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES spatie_permissions (id) ON DELETE CASCADE,
        CONSTRAINT spatie_role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES spatie_roles (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

foreach ($sql as $q) {
    try {
        $conn->statement($q);
        echo "Executed query successfully.\n";
    } catch (\Exception $e) {
        echo "Error executing query: " . $e->getMessage() . "\n";
    }
}

// Seed the roles
$roles = ['Administrador', 'Director General', 'Usuario'];
foreach ($roles as $role) {
    $conn->table('spatie_roles')->updateOrInsert(
        ['name' => $role, 'guard_name' => 'web'],
        ['created_at' => now(), 'updated_at' => now()]
    );
}
echo "Roles seeded.\n";

// Assign Administrador role to soporte@mapetzin.com (ID 1)
$adminRole = $conn->table('spatie_roles')->where('name', 'Administrador')->first();
if ($adminRole) {
    $conn->table('spatie_model_has_roles')->updateOrInsert(
        ['role_id' => $adminRole->id, 'model_type' => 'App\Models\User', 'model_id' => 1]
    );
    echo "Assigned Administrador role to ID 1.\n";
}
