<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::connection('sistema_tickets')->hasTable('kpi_results')) {
            if (!Schema::connection('sistema_tickets')->hasColumn('kpi_results', 'semana')) {
                Schema::connection('sistema_tickets')->table('kpi_results', function (Blueprint $table) {
                    $table->integer('semana')->nullable()->after('kpi_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::connection('sistema_tickets')->hasTable('kpi_results')) {
            if (Schema::connection('sistema_tickets')->hasColumn('kpi_results', 'semana')) {
                Schema::connection('sistema_tickets')->table('kpi_results', function (Blueprint $table) {
                    $table->dropColumn('semana');
                });
            }
        }
    }
};
