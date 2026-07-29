<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

// 1. Get User mapping (by email)
$sqliteUsers = [];
$db = new SQLite3(__DIR__ . '/database/database_backup_20260512.sqlite');
$res = $db->query('SELECT id, email FROM users');
while($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $sqliteUsers[$row['email']] = $row['id'];
}

$mysqlUsers = User::all()->pluck('id', 'email')->toArray();

$idMap = []; // [old_id => new_id]
foreach ($sqliteUsers as $email => $oldId) {
    if (isset($mysqlUsers[$email])) {
        $idMap[$oldId] = $mysqlUsers[$email];
    }
}

echo "Found " . count($idMap) . " user mappings.\n";

// 2. Assign 'Usuario' role to all users that existed in SQLite
foreach ($idMap as $oldId => $newId) {
    $user = User::find($newId);
    if ($user && !$user->roles()->exists()) {
        $user->assignRole('Usuario');
        echo "Granted access to: " . $user->email . "\n";
    }
}

// 3. Update foreign keys in all relevant tables
$tablesToUpdate = [
    'bitacoras' => ['user_id'],
    'comentarios' => ['user_id'],
    'events' => ['created_by'],
    'event_user' => ['user_id'],
    'model_has_roles' => ['model_id'],
    // Add others if necessary
];

DB::statement('SET FOREIGN_KEY_CHECKS=0');
foreach ($tablesToUpdate as $table => $columns) {
    echo "Updating table: $table\n";
    foreach ($columns as $column) {
        foreach ($idMap as $oldId => $newId) {
            $query = DB::table($table)->where($column, $oldId);
            
            if ($table === 'model_has_roles' || $table === 'model_has_permissions') {
                $query->where('model_type', 'App\Models\User');
            }
            
            $query->update([$column => $newId]);
        }
    }
}
DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "ID Remapping complete.\n";
