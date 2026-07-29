<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Re-configure sqlite connection dynamically
config(['database.connections.sqlite_copy' => [
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]]);

$sqlite = DB::connection('sqlite_copy');
$mysql = DB::connection('mysql');

$tables = [
    'areas',
    'acuerdos',
    'bitacoras',
    'comentarios',
    'avances_diarios',
    'events',
    'event_user',
    'event_area',
    'roles',
    'permissions',
    'model_has_roles',
    'model_has_permissions',
    'role_has_permissions',
];

foreach ($tables as $table) {
    echo "Migrating table: $table\n";
    try {
        $rows = DB::connection('sqlite_copy')->table($table)->get();
        if ($rows->count() > 0) {
            // Disable foreign key checks for truncate/insert
            DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');
            DB::connection('mysql')->table($table)->truncate();
            
            foreach ($rows->chunk(100) as $chunk) {
                DB::connection('mysql')->table($table)->insert(array_map(function($row) {
                    return (array)$row;
                }, $chunk->toArray()));
            }
            DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');
            echo "  Copied " . $rows->count() . " rows.\n";
        } else {
            echo "  Table is empty.\n";
        }
    } catch (\Exception $e) {
        echo "  Error migrating table $table: " . $e->getMessage() . "\n";
    }
}

echo "\nData migration complete.\n";
