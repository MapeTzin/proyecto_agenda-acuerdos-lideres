<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- kpis count ---\n";
echo DB::connection('sistema_tickets')->table('kpis')->count() . "\n";
print_r(DB::connection('sistema_tickets')->table('kpis')->get());

echo "--- kpi_results count ---\n";
echo DB::connection('sistema_tickets')->table('kpi_results')->count() . "\n";
