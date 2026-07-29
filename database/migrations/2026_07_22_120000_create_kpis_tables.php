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
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->string('area');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->double('meta_green')->default(95);
            $table->double('meta_yellow')->default(90);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('kpi_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpis')->onDelete('cascade');
            $table->integer('anio')->default(2026);
            $table->integer('mes'); // 1 = Enero, 2 = Febrero, ..., 12 = Diciembre
            $table->double('valor')->nullable();
            $table->date('fecha_actualizacion')->nullable();
            $table->text('justificacion')->nullable();
            $table->timestamps();

            $table->unique(['kpi_id', 'anio', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_valores');
        Schema::dropIfExists('kpis');
    }
};
