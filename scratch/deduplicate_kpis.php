<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

// Normalize lowercase category names
$db->table('kpis')->where('category', 'ti')->update(['category' => 'SISTEMAS Y TI']);
$db->table('kpis')->where('category', 'ventas')->update(['category' => 'VENTAS']);
$db->table('kpis')->where('category', 'coordinador')->update(['category' => 'JEFATURA DE STAFF']);

// Deduplicate KPIs by name and category
$allKpis = $db->table('kpis')->orderBy('id', 'asc')->get();

$seen = [];
$deleted = 0;

foreach ($allKpis as $kpi) {
    $key = trim(strtoupper($kpi->category)) . '||' . trim(strtoupper($kpi->name));
    
    if (isset($seen[$key])) {
        // Transfer results from this duplicate to the original master KPI
        $masterId = $seen[$key];
        
        $duplicateResults = $db->table('kpi_results')->where('kpi_id', $kpi->id)->get();
        foreach ($duplicateResults as $r) {
            $masterRes = $db->table('kpi_results')
                ->where('kpi_id', $masterId)
                ->where('year', $r->year)
                ->where('month', $r->month)
                ->first();

            if ($masterRes) {
                // If master result has no valid value (-1) but duplicate has a valid value, update master
                if (($masterRes->value == -1 || $masterRes->value === null) && ($r->value != -1 && $r->value !== null)) {
                    $db->table('kpi_results')->where('id', $masterRes->id)->update([
                        'value' => $r->value,
                        'period_date' => $r->period_date ?? $masterRes->period_date,
                        'notes' => $r->notes ?? $masterRes->notes,
                    ]);
                }
            } else {
                $db->table('kpi_results')->where('id', $r->id)->update(['kpi_id' => $masterId]);
            }
        }
        
        // Delete duplicate KPI record
        $db->table('kpis')->where('id', $kpi->id)->delete();
        $deleted++;
    } else {
        $seen[$key] = $kpi->id;
    }
}

echo "Deduplication completed. Deleted {$deleted} duplicate KPIs.\n";
