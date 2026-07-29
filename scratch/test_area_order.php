<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = new \App\Livewire\KpisAreas();
$data = $comp->render()->getData();

echo "=== VERIFYING ORDER OF AREAS ===\n";
foreach ($data['officialAreas'] as $idx => $area) {
    echo sprintf("%2d. %s\n", $idx + 1, $area);
}
