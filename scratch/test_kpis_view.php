<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Route kpis.areas: " . (Route::has('kpis.areas') ? 'OK' : 'FAIL') . "\n";

$kpis = DB::connection('sistema_tickets')
    ->table('kpis')
    ->where('category', 'CONTABILIDAD')
    ->get();

echo "Total KPIs found for CONTABILIDAD: " . count($kpis) . "\n";
foreach ($kpis as $k) {
    echo " - " . $k->name . "\n";
}
