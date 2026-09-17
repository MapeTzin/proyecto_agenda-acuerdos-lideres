<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $db = DB::connection('sistema_tickets');

        $kpis = [
            [
                'name' => 'MEDISTIK - Entrega de Remesas',
                'code' => 'KPI_MEDISTIK_ENTREGA_REMESAS',
                'description' => 'Porcentaje de cumplimiento en la entrega de remesas gestionadas por Medistik.',
                'category' => 'ALMACEN',
                'default_target' => 95.00,
            ],
            [
                'name' => 'MEDISTIK Exactitud de Inventario',
                'code' => 'KPI_MEDISTIK_EXACTITUD_INVENTARIO',
                'description' => 'Porcentaje de exactitud y concordancia en el inventario administrado por Medistik.',
                'category' => 'ALMACEN',
                'default_target' => 95.00,
            ]
        ];

        foreach ($kpis as $kpiData) {
            $existing = $db->table('kpis')
                ->where('code', $kpiData['code'])
                ->orWhere(function($q) use ($kpiData) {
                    $q->where('name', $kpiData['name'])
                      ->where('category', 'ALMACEN');
                })
                ->first();

            if ($existing) {
                $db->table('kpis')->where('id', $existing->id)->update([
                    'name' => $kpiData['name'],
                    'description' => $kpiData['description'],
                    'category' => 'ALMACEN',
                    'is_active' => 1,
                    'default_target' => $kpiData['default_target'],
                    'updated_at' => now(),
                ]);
                $kpiId = $existing->id;
            } else {
                $kpiId = $db->table('kpis')->insertGetId([
                    'name' => $kpiData['name'],
                    'code' => $kpiData['code'],
                    'description' => $kpiData['description'],
                    'category' => 'ALMACEN',
                    'unit' => '%',
                    'default_target' => $kpiData['default_target'],
                    'default_weight' => 1.00,
                    'formula_type' => 'percentage',
                    'is_area_kpi' => 0,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Crear registros mensuales base para 2026
            for ($m = 1; $m <= 12; $m++) {
                $db->table('kpi_results')->updateOrInsert(
                    [
                        'kpi_id' => $kpiId,
                        'year' => 2026,
                        'month' => $m,
                        'semana' => null,
                    ],
                    [
                        'value' => -1.00,
                        'target_value' => $kpiData['default_target'],
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = DB::connection('sistema_tickets');

        $codes = ['KPI_MEDISTIK_ENTREGA_REMESAS', 'KPI_MEDISTIK_EXACTITUD_INVENTARIO'];
        $db->table('kpis')->whereIn('code', $codes)->update(['is_active' => 0, 'updated_at' => now()]);
    }
};
