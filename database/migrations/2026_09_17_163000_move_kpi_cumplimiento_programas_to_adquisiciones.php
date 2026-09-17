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

        // 1. Desactivar el KPI en JEFATURA DE STAFF
        $staffKpi = $db->table('kpis')
            ->where('category', 'LIKE', '%STAFF%')
            ->where(function($q) {
                $q->where('name', 'LIKE', '%cumplimiento de programas de servicios y mantenimiento%')
                  ->orWhere('code', 'KPI_83_NDICEDECUM');
            })
            ->first();

        if ($staffKpi) {
            $db->table('kpis')
                ->where('id', $staffKpi->id)
                ->update(['is_active' => 0, 'updated_at' => now()]);
        }

        // 2. Localizar o actualizar el KPI en ADQUISICIONES
        $adqKpi = $db->table('kpis')
            ->where('category', 'LIKE', '%ADQUISICION%')
            ->where(function($q) {
                $q->where('name', 'LIKE', '%cumplimiento de programas de servicios%')
                  ->orWhere('code', 'KPI_12_NDICEDECUM');
            })
            ->first();

        if ($adqKpi) {
            $db->table('kpis')
                ->where('id', $adqKpi->id)
                ->update([
                    'name' => 'Índice de cumplimiento de programas de servicios y mantenimiento',
                    'description' => 'Promedio de días hábiles para cumplir los programas de servicios y mantenimiento. Meta: ≤ 6 días hábiles.',
                    'default_target' => 95.00,
                    'is_active' => 1,
                    'updated_at' => now(),
                ]);
            $kpiId = $adqKpi->id;
        } else {
            $kpiId = $db->table('kpis')->insertGetId([
                'name' => 'Índice de cumplimiento de programas de servicios y mantenimiento',
                'code' => 'KPI_12_NDICEDECUM',
                'description' => 'Promedio de días hábiles para cumplir los programas de servicios y mantenimiento. Meta: ≤ 6 días hábiles.',
                'category' => 'ADQUISICIONES',
                'unit' => '%',
                'default_target' => 95.00,
                'default_weight' => 1.00,
                'formula_type' => 'percentage',
                'is_area_kpi' => 0,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Actualizar valores de Agosto 2026 (Mes 8)
        $augustWeeks = [
            1 => ['val' => 95.00, 'date' => '2026-08-07'],
            2 => ['val' => 95.00, 'date' => '2026-08-14'],
            3 => ['val' => 95.00, 'date' => '2026-08-21'],
            4 => ['val' => 95.00, 'date' => '2026-08-28'],
            5 => ['val' => 95.00, 'date' => '2026-08-31'],
        ];

        foreach ($augustWeeks as $weekNum => $data) {
            $db->table('kpi_results')->updateOrInsert(
                [
                    'kpi_id' => $kpiId,
                    'year' => 2026,
                    'month' => 8,
                    'semana' => $weekNum,
                ],
                [
                    'value' => $data['val'],
                    'target_value' => 95.00,
                    'period_date' => $data['date'],
                    'notes' => '',
                    'inicio_semana' => '',
                    'updated_at' => now(),
                ]
            );
        }

        $db->table('kpi_results')->updateOrInsert(
            [
                'kpi_id' => $kpiId,
                'year' => 2026,
                'month' => 8,
                'semana' => null,
            ],
            [
                'value' => 95.00,
                'target_value' => 95.00,
                'period_date' => '2026-08-31',
                'inicio_semana' => 'PARO TÉCNICO',
                'notes' => 'Recolección de proyectado',
                'updated_at' => now(),
            ]
        );

        // 4. Actualizar valores de Septiembre 2026 (Mes 9)
        $septWeeks = [
            1 => ['val' => 95.00, 'date' => '2026-09-09'],
            2 => ['val' => 95.00, 'date' => '2026-09-09'],
            3 => ['val' => -1.00, 'date' => null],
            4 => ['val' => -1.00, 'date' => null],
            5 => ['val' => -1.00, 'date' => null],
        ];

        foreach ($septWeeks as $weekNum => $data) {
            $db->table('kpi_results')->updateOrInsert(
                [
                    'kpi_id' => $kpiId,
                    'year' => 2026,
                    'month' => 9,
                    'semana' => $weekNum,
                ],
                [
                    'value' => $data['val'],
                    'target_value' => 95.00,
                    'period_date' => $data['date'],
                    'notes' => '',
                    'inicio_semana' => '',
                    'updated_at' => now(),
                ]
            );
        }

        $db->table('kpi_results')->updateOrInsert(
            [
                'kpi_id' => $kpiId,
                'year' => 2026,
                'month' => 9,
                'semana' => null,
            ],
            [
                'value' => 95.00,
                'target_value' => 95.00,
                'period_date' => '2026-09-11',
                'inicio_semana' => '',
                'notes' => '',
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = DB::connection('sistema_tickets');

        // Reactivar el KPI en JEFATURA DE STAFF
        $db->table('kpis')
            ->where('category', 'LIKE', '%STAFF%')
            ->where(function($q) {
                $q->where('name', 'LIKE', '%cumplimiento de programas de servicios y mantenimiento%')
                  ->orWhere('code', 'KPI_83_NDICEDECUM');
            })
            ->update(['is_active' => 1, 'updated_at' => now()]);
    }
};
