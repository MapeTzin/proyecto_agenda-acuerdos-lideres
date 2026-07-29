<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new \App\Livewire\KpisAreas();
$data = $comp->render()->getData();

echo "=== TEST SEMAFORO COLUMN HEADER & DOTS ===\n";
echo "Header: SEMÁFORO " . $data['latestMonthName'] . "\n\n";

foreach ($data['monitorData'] as $row) {
    echo "Área: {$row['area']} ({$row['total_kpis']} KPI's)\n";
    foreach ($row['kpi_dots'] as $dIdx => $dot) {
        echo "   Dot " . ($dIdx + 1) . ": Val={$dot['val']}, Color={$dot['color']} [{$dot['name']}]\n";
    }
    echo "\n";
}
