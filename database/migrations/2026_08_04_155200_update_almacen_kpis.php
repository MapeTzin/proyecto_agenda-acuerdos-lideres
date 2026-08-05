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
        // 1. Deactivate old Almacen KPIs
        $oldKpis = [
            'Índice de Cumplimiento de Entregas por Remesa',
            'Kick Off de Pedidos',
            'Índice de Cumplimiento de Entregas Acumulado Mensual',
            'Precisión de Inventario',
            'Precisión del Inventario',
            'Días de Inventario Disponible',
            'Tiempo de Packing',
            'Incumplimiento por Errores de Packing',
            'Incumplimientos por Errores de Packing',
            'Gastos Generados en Contraetiquetado',
            'Tasa de Errores Internos en Documentación',
            'Cumplimiento de Entregas y Logística',
            'Costo por Envío'
        ];

        DB::connection('sistema_tickets')->table('kpis')
            ->where('category', 'ALMACEN')
            ->whereIn('name', $oldKpis)
            ->update(['is_active' => 0, 'updated_at' => now()]);

        // 2. Insert the 6 new Almacen KPIs
        $newKpis = [
            'Documental (tiempo/eficiencia)',
            'Picking (tiempo/eficiencia)',
            'Packing (tiempo/eficiencia)',
            'Embarques / logistica (tiempo / eficiencia)',
            'Precisión del inventario',
            'Registros entregados con Acuse'
        ];

        foreach ($newKpis as $idx => $name) {
            $code = 'KPI_NEW_ALMA_' . time() . '_' . $idx;
            $kpiId = DB::connection('sistema_tickets')->table('kpis')->insertGetId([
                'name' => $name,
                'code' => $code,
                'description' => $name,
                'category' => 'ALMACEN',
                'weight' => 0.00,
                'unit' => 'percentage',
                'target' => 95.00,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create default results for months 1 to 8 for year 2026
            for ($m = 1; $m <= 8; $m++) {
                DB::connection('sistema_tickets')->table('kpi_results')->insert([
                    'kpi_id' => $kpiId,
                    'year' => 2026,
                    'month' => $m,
                    'value' => -1.00,
                    'target_value' => 95.00,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $newKpis = [
            'Documental (tiempo/eficiencia)',
            'Picking (tiempo/eficiencia)',
            'Packing (tiempo/eficiencia)',
            'Embarques / logistica (tiempo / eficiencia)',
            'Precisión del inventario',
            'Registros entregados con Acuse'
        ];

        // Fetch IDs of the new KPIs
        $kpiIds = DB::connection('sistema_tickets')->table('kpis')
            ->where('category', 'ALMACEN')
            ->whereIn('name', $newKpis)
            ->pluck('id');

        // Delete results and the new KPIs
        if ($kpiIds->isNotEmpty()) {
            DB::connection('sistema_tickets')->table('kpi_results')->whereIn('kpi_id', $kpiIds)->delete();
            DB::connection('sistema_tickets')->table('kpis')->whereIn('id', $kpiIds)->delete();
        }

        // Reactivate old Almacen KPIs
        $oldKpis = [
            'Índice de Cumplimiento de Entregas por Remesa',
            'Kick Off de Pedidos',
            'Índice de Cumplimiento de Entregas Acumulado Mensual',
            'Precisión de Inventario',
            'Precisión del Inventario',
            'Días de Inventario Disponible',
            'Tiempo de Packing',
            'Incumplimiento por Errores de Packing',
            'Incumplimientos por Errores de Packing',
            'Gastos Generados en Contraetiquetado',
            'Tasa de Errores Internos en Documentación',
            'Cumplimiento de Entregas y Logística',
            'Costo por Envío'
        ];

        DB::connection('sistema_tickets')->table('kpis')
            ->where('category', 'ALMACEN')
            ->whereIn('name', $oldKpis)
            ->update(['is_active' => 1, 'updated_at' => now()]);
    }
};
