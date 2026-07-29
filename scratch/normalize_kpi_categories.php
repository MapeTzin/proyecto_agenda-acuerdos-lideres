<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

// Re-run full sync with explicit mappings from areasKpi DB
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
$oldAreas = $oldDb->table('areas')->get()->keyBy('id');
$oldKpis = $oldDb->table('kpis')->get();

$mapArea = [
    'TI' => 'SISTEMAS Y TI',
    'SISTEMAS Y TI' => 'SISTEMAS Y TI',
    'COMERCIAL' => 'COMERCIAL',
    'VENTAS' => 'VENTAS',
    'CONTABILIDAD' => 'CONTABILIDAD',
    'CUENTAS POR COBRAR' => 'CONTABILIDAD Y CXC',
    'CAPITAL HUMANO' => 'CAPITAL HUMANO',
    'ALMACEN' => 'ALMACEN',
    'ADQUISICIONES' => 'ADQUISICIONES',
    'ASEGURAMIENTO DE CALIDAD' => 'ASEGURAMIENTO DE CALIDAD',
    'ASIS ADM' => 'ASIS ADM',
    'CULTURA ORGANIZACIONAL' => 'CULTURA ORGANIZACIONAL',
    'JEFATURA DE STAFF' => 'JEFATURA DE STAFF',
];

foreach ($oldKpis as $kpi) {
    $areaObj = $oldAreas->get($kpi->area_id);
    $rawName = $areaObj ? trim(strtoupper($areaObj->nombre)) : trim(strtoupper($kpi->category));
    $normArea = $mapArea[$rawName] ?? $rawName;

    $code = 'KPI_OLD_' . $kpi->id;

    $db->table('kpis')->updateOrInsert(
        ['code' => $code],
        [
            'name' => $kpi->nombre,
            'description' => $kpi->descripcion,
            'category' => $normArea,
            'target' => (float)$kpi->meta > 0 ? (float)$kpi->meta : 95.00,
            'is_active' => 1,
            'updated_at' => now(),
        ]
    );

    $newKpi = $db->table('kpis')->where('code', $code)->first();

    $oldValues = $oldDb->table('kpi_values')->where('kpi_id', $kpi->id)->get();
    foreach ($oldValues as $v) {
        if (!$v->fecha) continue;
        $dt = \Carbon\Carbon::parse($v->fecha);
        $valNum = ($v->valor !== null && $v->valor !== '') ? (float)$v->valor : -1;

        $db->table('kpi_results')->updateOrInsert(
            [
                'kpi_id' => $newKpi->id,
                'year' => $dt->year,
                'month' => $dt->month
            ],
            [
                'value' => $valNum,
                'target_value' => (float)$kpi->meta > 0 ? (float)$kpi->meta : 95.00,
                'period_date' => $v->fecha,
                'notes' => $v->notas,
                'updated_at' => now(),
            ]
        );
    }

    for ($m = 1; $m <= 7; $m++) {
        $exists = $db->table('kpi_results')
            ->where('kpi_id', $newKpi->id)
            ->where('year', 2026)
            ->where('month', $m)
            ->exists();

        if (!$exists) {
            $db->table('kpi_results')->insert([
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

// Clean up lower case test categories
$db->table('kpis')->where('category', 'ti')->update(['category' => 'SISTEMAS Y TI']);
$db->table('kpis')->where('category', 'ventas')->update(['category' => 'VENTAS']);
$db->table('kpis')->where('category', 'coordinador')->update(['category' => 'JEFATURA DE STAFF']);

echo "KPI categories successfully normalized!\n";
