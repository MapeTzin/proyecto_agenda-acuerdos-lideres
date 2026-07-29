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
            $table->timestamp('fecha_cambio_estatus')->nullable()->after('estatus');
            $table->text('motivo_detencion')->nullable()->after('fecha_cambio_estatus');
            $table->text('comentario_cierre')->nullable()->after('motivo_detencion');
            $table->integer('tiempo_total_dias')->default(0)->after('comentario_cierre');
            $table->integer('tiempo_detenido_dias')->default(0)->after('tiempo_total_dias');
            $table->integer('tiempo_efectivo_dias')->default(0)->after('tiempo_detenido_dias');
        });

        // Initialize fecha_cambio_estatus for existing records
        \Illuminate\Support\Facades\DB::table('acuerdos')->update(['fecha_cambio_estatus' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acuerdos', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_cambio_estatus',
                'motivo_detencion',
                'comentario_cierre',
                'tiempo_total_dias',
                'tiempo_detenido_dias',
                'tiempo_efectivo_dias'
            ]);
        });
    }
};
