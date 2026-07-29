<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DESCRIBE kpis ---\n";
print_r(DB::connection('sistema_tickets')->select('DESCRIBE kpis'));

echo "\n--- DESCRIBE kpi_results ---\n";
print_r(DB::connection('sistema_tickets')->select('DESCRIBE kpi_results'));
