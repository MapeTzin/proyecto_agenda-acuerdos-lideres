<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$areasToTest = ['CONTABILIDAD', 'SISTEMAS Y TI', 'CAPITAL HUMANO', 'ALMACEN', 'ADQUISICIONES', 'ASEGURAMIENTO DE CALIDAD', 'VENTAS', 'COMERCIAL', 'CUENTAS POR COBRAR', 'JEFATURA DE STAFF', 'CULTURA ORGANIZACIONAL', 'ASIS ADM'];

foreach ($areasToTest as $area) {
    $kpis = $db->table('kpis')
        ->where('is_active', 1)
        ->where(function($q) use ($area) {
            $q->where('category', $area)
              ->orWhere('category', 'LIKE', '%' . $area . '%');
        })
        ->get();

    echo "Area [{$area}]: " . count($kpis) . " KPIs found.\n";
    foreach ($kpis as $k) {
        $valCount = $db->table('kpi_results')->where('kpi_id', $k->id)->where('value', '!=', -1)->count();
        echo "   - {$k->name} (Meta: {$k->target}%) [{$valCount} registros con valor]\n";
    }
}
