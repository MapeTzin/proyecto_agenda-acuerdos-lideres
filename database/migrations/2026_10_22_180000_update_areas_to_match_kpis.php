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
        // 1. Rename ID 11 'CONTABILIDAD Y CXC' to 'CONTABILIDAD'
        DB::table('areas')
            ->where('id', 11)
            ->update([
                'name' => 'CONTABILIDAD',
                'color' => '#e67c73'
            ]);

        // 2. Insert or update 'CUENTAS POR COBRAR'
        $cxcExists = DB::table('areas')->where('name', 'CUENTAS POR COBRAR')->exists();
        if (!$cxcExists) {
            DB::table('areas')->insert([
                'name' => 'CUENTAS POR COBRAR',
                'color' => '#0284c7',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('areas')
            ->where('id', 11)
            ->update([
                'name' => 'CONTABILIDAD Y CXC',
                'color' => '#e67c73'
            ]);

        DB::table('areas')
            ->where('name', 'CUENTAS POR COBRAR')
            ->delete();
    }
};
