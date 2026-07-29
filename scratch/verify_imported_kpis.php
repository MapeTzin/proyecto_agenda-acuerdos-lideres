<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$kpis = $db->table('kpis')
    ->select('category', DB::raw('count(*) as total'))
    ->groupBy('category')
    ->get();

echo "=== KPIS SUMMARY BY AREA ===\n";
foreach ($kpis as $k) {
    echo "Area [{$k->category}]: {$k->total} KPIs\n";
}
