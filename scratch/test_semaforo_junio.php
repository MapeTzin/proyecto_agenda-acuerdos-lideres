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

$latestMonthNum = 6; // Junio
$latestMonthName = 'JUNIO';

echo "=== SEMÁFORO INDIVIDUAL DE KPIS PARA {$latestMonthName} ===\n";

foreach ($officialAreas as $area) {
    $kpis = $db->table('kpis')
        ->where('is_active', 1)
        ->where(function($q) use ($area) {
            $q->where('category', $area)
              ->orWhere('category', 'LIKE', '%' . $area . '%');
        })
        ->orderBy('id', 'asc')
        ->get();

    echo "\nÁrea: {$area} (Total KPIs: " . count($kpis) . ")\n";

    foreach ($kpis as $idx => $kpi) {
        $res = $db->table('kpi_results')
            ->where('kpi_id', $kpi->id)
            ->where('year', 2026)
            ->where('month', $latestMonthNum)
            ->first();

        $val = ($res && $res->value !== null) ? (float)$res->value : -1;
        
        $color = 'GREY';
        if ($val >= 0) {
            if ($val >= $kpi->target) {
                $color = 'GREEN (🟢)';
            } elseif ($val >= 90) {
                $color = 'YELLOW (🟡)';
            } else {
                $color = 'RED (🔴)';
            }
        }

        $valDisplay = $val == -1 ? '-' : $val . '%';
        echo sprintf("   KPI %d: %-55s | Val: %6s | Semáforo: %s\n", $idx + 1, $kpi->name, $valDisplay, $color);
    }
}
