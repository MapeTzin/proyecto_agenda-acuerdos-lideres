<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['database.connections.areasKpi' => [
    'driver' => 'mysql',
    'host' => '192.168.100.12',
    'port' => '3306',
    'database' => 'areasKpi',
    'username' => 'usuariomt',
    'password' => 'mape157527*',
    'charset' => 'utf8mb4',
]]);

$dbOld = DB::connection('areasKpi');

$sampleValues = $dbOld->table('kpi_values')->limit(15)->get();
foreach ($sampleValues as $v) {
    echo json_encode($v) . "\n";
}
