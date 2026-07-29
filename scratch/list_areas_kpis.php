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
$dbNew = DB::connection('sistema_tickets');

$areas = $dbOld->table('areas')->get();

echo "=== AREAS IN areasKpi ===\n";
foreach ($areas as $area) {
    $kpiCount = $dbOld->table('kpis')->where('area_id', $area->id)->count();
    echo "ID: {$area->id} | Nombre: {$area->nombre} | Slug: {$area->slug} | Total KPIs: {$kpiCount}\n";
}

echo "\n=== ALL KPIS AND VALUES ===\n";
$kpis = $dbOld->table('kpis')->get();
foreach ($kpis as $kpi) {
    $areaObj = $areas->firstWhere('id', $kpi->area_id);
    $areaName = $areaObj ? $areaObj->nombre : 'GENERAL';
    $valCount = $dbOld->table('kpi_values')->where('kpi_id', $kpi->id)->count();
    echo "KPI #{$kpi->id} [{$areaName}]: {$kpi->nombre} (Meta: {$kpi->meta}) - {$valCount} values\n";
}
