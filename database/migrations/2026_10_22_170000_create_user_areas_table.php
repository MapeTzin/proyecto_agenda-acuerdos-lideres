<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'area_id']);
            $table->index('user_id');
        });

        // Migrar acceso existente de s.arias@mapetzin.com (CULTURA ORGANIZACIONAL)
        try {
            $user = DB::table('auth_center.users')->where('email', 's.arias@mapetzin.com')->first();
            $culturaArea = DB::table('areas')->where('name', 'CULTURA ORGANIZACIONAL')->first();
            if ($user && $culturaArea) {
                DB::table('user_areas')->insertOrIgnore([
                    'user_id' => $user->id,
                    'area_id' => $culturaArea->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Silencioso si auth_center.users no está disponible en este entorno
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_areas');
    }
};
