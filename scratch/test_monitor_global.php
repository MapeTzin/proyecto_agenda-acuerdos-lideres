<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$officialAreas = [
    'CONTABILIDAD',
    'ADQUISICIONES',
    'ASEGURAMIENTO DE CALIDAD',
    'COMERCIAL',
    'CAPITAL HUMANO',
    'SISTEMAS Y TI',
    'ASIS ADM',
    'CUENTAS POR COBRAR',
    'ALMACEN',
    'VENTAS',
    'JEFATURA DE STAFF',
    'CULTURA ORGANIZACIONAL'
];

$monitorData = [];
$totalKpisGlobal = 0;
$totalCumplimientoSum = 0;
$areasWithDataCount = 0;

foreach ($officialAreas as $area) {
    $kpiIds = $db->table('kpis')
        ->where('is_active', 1)
        ->where(function($q) use ($area) {
            $q->where('category', $area)
              ->orWhere('category', 'LIKE', '%' . $area . '%');
        })
        ->pluck('id');

    $totalKpis = count($kpiIds);
    $totalKpisGlobal += $totalKpis;

    if ($totalKpis > 0) {
        $avgVal = $db->table('kpi_results')
            ->whereIn('kpi_id', $kpiIds)
            ->where('value', '>=', 0)
            ->avg('value');

        $pct = $avgVal !== null ? round($avgVal, 1) : 0;
    } else {
        $pct = 0;
    }

    if ($pct >= 95) {
        $semaforo = 'VERDE';
        $color = '#10b981';
        $badgeBg = '#d1fae5';
    } elseif ($pct >= 90) {
        $semaforo = 'AMARILLO';
        $color = '#f59e0b';
        $badgeBg = '#fef3c7';
    } else {
        $semaforo = 'ROJO';
        $color = '#ef4444';
        $badgeBg = '#fee2e2';
    }

    $monitorData[] = [
        'area' => $area,
        'total_kpis' => $totalKpis,
        'cumplimiento' => $pct,
        'semaforo' => $semaforo,
        'color' => $color,
        'badgeBg' => $badgeBg,
    ];
}

echo "=== MONITOR GLOBAL DE KPIS POR ÁREA ===\n";
echo "Total KPI's Registrados: {$totalKpisGlobal}\n";
foreach ($monitorData as $item) {
    echo sprintf(
        "%-25s | Total KPIs: %2d | Cumplimiento: %5.1f%% | Semáforo: %s\n",
        $item['area'],
        $item['total_kpis'],
        $item['cumplimiento'],
        $item['semaforo']
    );
}
