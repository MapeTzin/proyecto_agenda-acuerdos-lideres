<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new \App\Livewire\KpisAreas();
$comp->selectedArea = 'CONTABILIDAD';
$comp->loadKpiData();
$html = view($comp->render()->name(), $comp->render()->getData())->render();

echo "Rendered CONTABILIDAD area view successfully! HTML length: " . strlen($html) . "\n";
