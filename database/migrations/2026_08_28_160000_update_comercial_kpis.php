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
        $keepKpis = [
            'Nivel de Cumplimiento de Proyección de Pagos',
            'Índice de Órdenes de Compra Procesadas sin Incidencias',
            'KPI: Porcentaje de Entrega a Tiempo de Proveedores',
            'Margen bruto comercial (%)'
        ];

        DB::connection('sistema_tickets')->table('kpis')
            ->where(function($q) {
                $q->where('category', 'COMERCIAL')
                  ->orWhere('category', 'LIKE', '%COMERCIAL%');
            })
            ->whereNotIn('name', $keepKpis)
            ->update(['is_active' => 0, 'updated_at' => now()]);

        DB::connection('sistema_tickets')->table('kpis')
            ->where(function($q) {
                $q->where('category', 'COMERCIAL')
                  ->orWhere('category', 'LIKE', '%COMERCIAL%');
            })
            ->whereIn('name', $keepKpis)
            ->update(['is_active' => 1, 'target' => 95.00, 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('sistema_tickets')->table('kpis')
            ->where(function($q) {
                $q->where('category', 'COMERCIAL')
                  ->orWhere('category', 'LIKE', '%COMERCIAL%');
            })
            ->update(['is_active' => 1, 'updated_at' => now()]);
    }
};
