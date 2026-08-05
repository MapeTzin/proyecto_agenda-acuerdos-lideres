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
            if (!Schema::connection('sistema_tickets')->hasColumn('kpi_results', 'inicio_semana')) {
                Schema::connection('sistema_tickets')->table('kpi_results', function (Blueprint $table) {
                    $table->text('inicio_semana')->nullable()->after('notes');
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
            if (Schema::connection('sistema_tickets')->hasColumn('kpi_results', 'inicio_semana')) {
                Schema::connection('sistema_tickets')->table('kpi_results', function (Blueprint $table) {
                    $table->dropColumn('inicio_semana');
                });
            }
        }
    }
};
