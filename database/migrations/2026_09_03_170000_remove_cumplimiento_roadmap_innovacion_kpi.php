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
                $q->where('name', 'Cumplimiento de RoadMap de Innovación')
                  ->orWhere('name', 'LIKE', '%Cumplimiento de RoadMap de Innovaci%')
                  ->orWhere('code', 'STI_ROAD_INNO');
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
                $q->where('name', 'Cumplimiento de RoadMap de Innovación')
                  ->orWhere('name', 'LIKE', '%Cumplimiento de RoadMap de Innovaci%')
                  ->orWhere('code', 'STI_ROAD_INNO');
            })
            ->update(['is_active' => 1, 'updated_at' => now()]);
    }
};
