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
        Schema::create('acuerdos', function (Blueprint $table) {
            $table->id();
            $table->string('area');
            $table->enum('tipo_cuadrante', [1, 2]);
            $table->text('acuerdo');
            $table->text('descripcion')->nullable();
            $table->string('responsable');
            $table->string('apoyo')->nullable();
            $table->date('fecha_compromiso');
            $table->date('fecha_cierre')->nullable();
            $table->enum('estatus', ['pendiente', 'en_proceso', 'detenido', 'finalizado'])->default('pendiente');
            $table->enum('prioridad', ['alta', 'media'])->default('media');
            $table->integer('porcentaje_avance')->default(0);
            $table->text('comentarios')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acuerdos');
    }
};
