<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$sqliteConnection = DB::connection('sqlite');
$mysqlConnection = DB::connection('sistema_tickets');

// 1. Get all users from both
$sqliteUsers = $sqliteConnection->table('users')->get();
$mysqlUsers = $mysqlConnection->table('users')->get();

$mysqlMap = [];
foreach ($mysqlUsers as $user) {
    $mysqlMap[strtolower($user->email)] = $user->id;
}

$mapping = [];
$missing = [];

foreach ($sqliteUsers as $user) {
    $email = strtolower($user->email);
    if (isset($mysqlMap[$email])) {
        $mapping[$user->id] = $mysqlMap[$email];
    } else {
        // Manual mappings
        if ($user->id == 2 || $user->id == 17) {
            $mapping[$user->id] = 78; // Marco Mercado
        } elseif ($user->id == 10) {
            $mapping[$user->id] = 90; // Jose Carlos Reyes Soto -> Irlanda Ramirez
        } else {
            $missing[] = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ];
        }
    }
}

echo "Mapping found for " . count($mapping) . " users.\n";
foreach ($mapping as $oldId => $newId) {
    echo "  SQLite ID $oldId -> MySQL ID $newId\n";
}

if (!empty($missing)) {
    echo "\nNo mapping found for " . count($missing) . " users (email mismatch):\n";
    foreach ($missing as $m) {
        echo "  SQLite ID {$m['id']}: {$m['email']} ({$m['name']})\n";
    }
}

// 2. Perform updates in SQLite
if (empty($mapping)) {
    echo "\nNo mappings found. Aborting update.\n";
    exit;
}

$tablesToUpdate = [
    'bitacoras' => ['user_id'],
    'comentarios' => ['user_id'],
    'events' => ['created_by'],
    'event_user' => ['user_id'],
];

// Spatie Permission tables if they exist
try {
    $sqliteConnection->table('model_has_roles')->first();
    $tablesToUpdate['model_has_roles'] = ['model_id'];
} catch (\Exception $e) {}

try {
    $sqliteConnection->table('model_has_permissions')->first();
    $tablesToUpdate['model_has_permissions'] = ['model_id'];
} catch (\Exception $e) {}

echo "\nStarting updates...\n";

// Disable foreign key constraints for SQLite
$sqliteConnection->statement('PRAGMA foreign_keys = OFF');

foreach ($tablesToUpdate as $table => $columns) {
    foreach ($columns as $column) {
        $count = 0;
        foreach ($mapping as $oldId => $newId) {
            // Check if we are updating a polymorphic table
            if (in_array($table, ['model_has_roles', 'model_has_permissions'])) {
                $affected = $sqliteConnection->table($table)
                    ->where($column, $oldId)
                    ->where('model_type', 'App\Models\User')
                    ->update([$column => $newId]);
            } else {
                $affected = $sqliteConnection->table($table)
                    ->where($column, $oldId)
                    ->update([$column => $newId]);
            }
            $count += $affected;
        }
        echo "  Updated $count records in table '$table' column '$column'.\n";
    }
}

echo "\nMigration complete.\n";
