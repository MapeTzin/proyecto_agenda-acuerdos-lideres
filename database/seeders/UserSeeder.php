<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = ['Almacen', 'Ventas', 'RH', 'Dirección Comercial', 'Producción', 'Calidad'];

        foreach ($areas as $area) {
            \App\Models\User::create([
                'name' => 'Líder ' . $area,
                'email' => strtolower(str_replace(' ', '', $area)) . '@agenda.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'area' => $area,
            ]);
        }

        // Admin User
        \App\Models\User::create([
            'name' => 'Coordinador TI',
            'email' => 'admin@agenda.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'area' => 'Sistemas',
        ]);
    }
}
