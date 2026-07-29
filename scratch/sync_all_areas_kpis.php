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

$oldDb = DB::connection('areasKpi');
$newDb = DB::connection('sistema_tickets');

// Fetch all areas from old database
$oldAreas = $oldDb->table('areas')->get()->keyBy('id');

// Fetch all KPIs from old database
$oldKpis = $oldDb->table('kpis')->get();

$migratedKpis = 0;
$migratedValues = 0;

foreach ($oldKpis as $kpi) {
    $areaObj = $oldAreas->get($kpi->area_id);
    $areaName = $areaObj ? trim(strtoupper($areaObj->nombre)) : 'GENERAL';

    // Normalize area names if needed
    if ($areaName === 'TI') {
        $areaName = 'SISTEMAS Y TI';
    }

    $code = 'KPI_' . $kpi->id . '_' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $kpi->nombre), 0, 10));

    // Update or insert into new system's kpis table
    $newDb->table('kpis')->updateOrInsert(
        ['code' => $code],
        [
            'name' => $kpi->nombre,
            'description' => $kpi->descripcion,
            'category' => $areaName,
            'target' => (float)$kpi->meta > 0 ? (float)$kpi->meta : 95.00,
            'is_active' => 1,
            'updated_at' => now(),
        ]
    );

    $newKpi = $newDb->table('kpis')->where('code', $code)->first();
    $migratedKpis++;

    // Fetch values for this KPI
    $oldValues = $oldDb->table('kpi_values')->where('kpi_id', $kpi->id)->get();

    foreach ($oldValues as $v) {
        if (!$v->fecha) continue;

        $dt = \Carbon\Carbon::parse($v->fecha);
        $year = $dt->year;
        $month = $dt->month;

        $valNum = ($v->valor !== null && $v->valor !== '') ? (float)$v->valor : -1;

        $newDb->table('kpi_results')->updateOrInsert(
            [
                'kpi_id' => $newKpi->id,
                'year' => $year,
                'month' => $month
            ],
            [
                'value' => $valNum,
                'target_value' => (float)$kpi->meta > 0 ? (float)$kpi->meta : 95.00,
                'period_date' => $v->fecha,
                'notes' => $v->notas,
                'updated_at' => now(),
            ]
        );
        $migratedValues++;
    }

    // Ensure months 1 to 7 have records even if no value in old DB
    for ($m = 1; $m <= 7; $m++) {
        $exists = $newDb->table('kpi_results')
            ->where('kpi_id', $newKpi->id)
            ->where('year', 2026)
            ->where('month', $m)
            ->exists();

        if (!$exists) {
            $newDb->table('kpi_results')->insert([
                'kpi_id' => $newKpi->id,
                'year' => 2026,
                'month' => $m,
                'value' => -1,
                'target_value' => (float)$kpi->meta > 0 ? (float)$kpi->meta : 95.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

echo "Migrated {$migratedKpis} KPIs and {$migratedValues} values across all areas successfully!\n";
