<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

// Reassign any CONTABILIDAD Y CXC category to CUENTAS POR COBRAR
$db->table('kpis')
    ->where('category', 'CONTABILIDAD Y CXC')
    ->update(['category' => 'CUENTAS POR COBRAR']);

echo "Updated all CONTABILIDAD Y CXC KPIs to CUENTAS POR COBRAR!\n";

$categories = $db->table('kpis')
    ->select('category')
    ->distinct()
    ->pluck('category');

echo "Current categories in DB:\n";
print_r($categories);
