<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('avances_diarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acuerdo_id')->constrained()->onDelete('cascade');
            $table->date('fecha');
            $table->integer('porcentaje_avance');
            $table->timestamps();

            $table->unique(['acuerdo_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avances_diarios');
    }
};
