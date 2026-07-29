<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\User;
use App\Models\Event;
use Spatie\Permission\Models\Role;

class CalendarSetupSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'ALMACEN',
            'VENTAS',
            'COMERCIAL',
            'RECURSOS HUMANOS',
            'ASEGURAMIENTO DE CALIDAD',
            'CUENTAS POR COBRAR',
            'SERVICIOS A GOBIERNO',
            'ASIS ADM',
            'ADQUISICIONES',
            'SISTEMAS',
            'CONTABILIDAD',
            'CULTURA ORGANIZACIONAL'
        ];

        foreach ($areas as $areaName) {
            $color = null;
            if ($areaName === 'CULTURA ORGANIZACIONAL') {
                $color = '#f43f5e'; // Rose color
            }
            Area::updateOrCreate(['name' => $areaName], ['color' => $color]);
        }

        // Create Roles
        $adminRole = Role::updateOrCreate(['name' => 'Administrador']);
        Role::updateOrCreate(['name' => 'Usuario']);

        // Assign Admin Role to specific users
        $adminEmails = ['v.arochi@mapetzin.com', 'direccion@mapetzin.com'];
        foreach ($adminEmails as $email) {
            $adminUser = User::where('email', $email)->first();
            if ($adminUser) {
                $adminUser->assignRole($adminRole);
            }
        }

        // Add some demo events
        $sistemasArea = Area::where('name', 'SISTEMAS')->first();
        if ($adminUser && $sistemasArea) {
            Event::updateOrCreate([
                'title' => 'Mantenimiento de Servidores',
                'start' => now()->addDays(2)->setHour(10)->setMinute(0),
                'end' => now()->addDays(2)->setHour(14)->setMinute(0),
                'area_id' => $sistemasArea->id,
                'created_by' => $adminUser->id,
                'color' => '#f59e0b',
                'is_public' => true
            ]);

            Event::updateOrCreate([
                'title' => 'Reunión de Avance Semanal',
                'start' => now()->addDays(1)->setHour(9)->setMinute(0),
                'end' => now()->addDays(1)->setHour(10)->setMinute(30),
                'area_id' => $sistemasArea->id,
                'created_by' => $adminUser->id,
                'color' => '#3b82f6',
                'is_public' => true
            ]);
        }
    }
}
