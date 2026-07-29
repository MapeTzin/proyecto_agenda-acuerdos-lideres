<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$officialAreas = [
    'ALMACEN',
    'VENTAS',
    'COMERCIAL',
    'ASEGURAMIENTO DE CALIDAD',
    'CAPITAL HUMANO',
    'JEFATURA DE STAFF',
    'ASIS ADM',
    'ADQUISICIONES',
    'SISTEMAS Y TI',
    'CONTABILIDAD',
    'CUENTAS POR COBRAR',
    'CULTURA ORGANIZACIONAL'
];

echo "=== COMPARATIVO MAYO vs JUNIO 2026 ===\n";

$mayoTotalSum = 0;
$junioTotalSum = 0;
$countMayo = 0;
$countJunio = 0;

foreach ($officialAreas as $area) {
    $kpiIds = $db->table('kpis')
        ->where('is_active', 1)
        ->where(function($q) use ($area) {
            $q->where('category', $area)
              ->orWhere('category', 'LIKE', '%' . $area . '%');
        })
        ->pluck('id');

    // Mayo (Month 5)
    $avgMayo = $db->table('kpi_results')
        ->whereIn('kpi_id', $kpiIds)
        ->where('year', 2026)
        ->where('month', 5)
        ->where('value', '>=', 0)
        ->avg('value');

    // Junio (Month 6)
    $avgJunio = $db->table('kpi_results')
        ->whereIn('kpi_id', $kpiIds)
        ->where('year', 2026)
        ->where('month', 6)
        ->where('value', '>=', 0)
        ->avg('value');

    $valMayo = $avgMayo !== null ? round($avgMayo, 1) : 0;
    $valJunio = $avgJunio !== null ? round($avgJunio, 1) : 0;

    $mayoTotalSum += $valMayo;
    $junioTotalSum += $valJunio;

    $diff = round($valJunio - $valMayo, 1);
    $trend = $diff > 0 ? "📈 +{$diff}%" : ($diff < 0 ? "📉 {$diff}%" : "➖ 0.0%");

    echo sprintf(
        "%-25s | Mayo: %5.1f%% | Junio: %5.1f%% | Tendencia: %s\n",
        $area,
        $valMayo,
        $valJunio,
        $trend
    );
}

$globalMayo = round($mayoTotalSum / count($officialAreas), 1);
$globalJunio = round($junioTotalSum / count($officialAreas), 1);
$globalDiff = round($globalJunio - $globalMayo, 1);

echo "\n--- CUMPLIMIENTO PROMEDIO GLOBAL ---\n";
echo "Mayo: {$globalMayo}%\n";
echo "Junio: {$globalJunio}%\n";
echo "Diferencia: " . ($globalDiff >= 0 ? "+{$globalDiff}%" : "{$globalDiff}%") . "\n";
