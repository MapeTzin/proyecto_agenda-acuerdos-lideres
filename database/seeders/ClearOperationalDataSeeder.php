<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearOperationalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        Schema::disableForeignKeyConstraints();

        $tables = [
            'acuerdos',
            'bitacoras',
            'comentarios',
            'avances_diarios',
            'events',
            'event_user',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("Table {$table} truncated.");
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
