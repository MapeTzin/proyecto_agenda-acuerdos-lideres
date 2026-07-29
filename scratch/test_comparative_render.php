<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new \App\Livewire\KpisAreas();
$data = $comp->render()->getData();

echo "=== TEST COMPARATIVO MAYO vs JUNIO ===\n";
echo "Mes Anterior: " . $data['prevMonthName'] . " (" . $data['cumplimientoMayoAvg'] . "%)\n";
echo "Mes Presentado: " . $data['latestMonthName'] . " (" . $data['cumplimientoJunioAvg'] . "%)\n";
echo "Diferencia Global: " . $data['globalDiff'] . "%\n\n";

echo "--- Filas Comparativas por Área ---\n";
foreach ($data['monitorData'] as $row) {
    echo sprintf(
        " %-25s | Mayo: %5.1f%% (%2d dots) | Junio: %5.1f%% (%2d dots) | Tendencia: %s\n",
        $row['area'],
        $row['cumplimiento_mayo'],
        count($row['kpi_dots_mayo']),
        $row['cumplimiento_junio'],
        count($row['kpi_dots_junio']),
        $row['trendText']
    );
}
