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
        DB::connection('sistema_tickets')->table('kpis')
            ->where(function($q) {
                $q->where('category', 'SISTEMAS Y TI')
                  ->orWhere('category', 'LIKE', '%SISTEMAS%');
            })
            ->where(function($q) {
                $q->where('name', 'Ventas en Línea')
                  ->orWhere('name', 'LIKE', '%Ventas en L%')
                  ->orWhere('code', 'KPI_87_VENTASENLN');
            })
            ->update(['is_active' => 0, 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('sistema_tickets')->table('kpis')
            ->where(function($q) {
                $q->where('category', 'SISTEMAS Y TI')
                  ->orWhere('category', 'LIKE', '%SISTEMAS%');
            })
            ->where(function($q) {
                $q->where('name', 'Ventas en Línea')
                  ->orWhere('name', 'LIKE', '%Ventas en L%')
                  ->orWhere('code', 'KPI_87_VENTASENLN');
            })
            ->update(['is_active' => 1, 'updated_at' => now()]);
    }
};
