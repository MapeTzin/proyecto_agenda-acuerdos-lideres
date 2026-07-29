<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new \App\Livewire\KpisAreas();
$data = $comp->render()->getData();

echo "=== TEST MONITOR GLOBAL DATA ===\n";
echo "Total KPI's Registrados: " . $data['totalKpisGlobal'] . "\n";
echo "Áreas Monitoreadas: " . $data['areasCount'] . "\n";
echo "Cumplimiento Promedio: " . $data['cumplimientoGlobalAvg'] . "%\n";
echo "Áreas en Meta: " . $data['areasEnMetaCount'] . "\n";

echo "\n--- Monitor Data Rows ---\n";
foreach ($data['monitorData'] as $row) {
    echo sprintf(" - %-25s | Total: %2d KPI's | Cumplimiento: %5.1f%% | Semáforo: %s\n", $row['area'], $row['total_kpis'], $row['cumplimiento'], $row['semaforo']);
}
