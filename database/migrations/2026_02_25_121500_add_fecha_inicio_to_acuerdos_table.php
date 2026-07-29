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
        Schema::table('acuerdos', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('apoyo');
        });
        
        // Populate existing agreements with created_at as fecha_inicio
        \Illuminate\Support\Facades\DB::table('acuerdos')->update([
            'fecha_inicio' => \Illuminate\Support\Facades\DB::raw('DATE(created_at)')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acuerdos', function (Blueprint $table) {
            $table->dropColumn('fecha_inicio');
        });
    }
};
