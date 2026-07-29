<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;
use Carbon\Carbon;

class AcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Sistemas',
                'tipo_cuadrante' => 1,
                'acuerdo' => 'Actualización de Servidores',
                'descripcion' => 'Realizar la actualización crítica de seguridad en los servidores de producción.',
                'responsable' => 'Juan Pérez',
                'apoyo' => 'Soporte TI',
                'fecha_compromiso' => Carbon::now()->addDays(2),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 45,
            ],
            [
                'area' => 'Recursos Humanos',
                'tipo_cuadrante' => 2,
                'acuerdo' => 'Capacitación Livewire',
                'descripcion' => 'Organizar sesión de capacitación sobre componentes dinámicos para el equipo de desarrollo.',
                'responsable' => 'Maria García',
                'apoyo' => 'Capacitación Externa',
                'fecha_compromiso' => Carbon::now()->addDays(7),
                'estatus' => 'pendiente',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
            ],
            [
                'area' => 'Operaciones',
                'tipo_cuadrante' => 1,
                'acuerdo' => 'Optimización de Inventarios',
                'descripcion' => 'Reducir el tiempo de respuesta en el surtido de materiales en un 15%.',
                'responsable' => 'Carlos López',
                'apoyo' => 'Logística',
                'fecha_compromiso' => Carbon::now()->subDays(1),
                'estatus' => 'detenido',
                'prioridad' => 'alta',
                'porcentaje_avance' => 20,
            ],
            [
                'area' => 'Sistemas',
                'tipo_cuadrante' => 1,
                'acuerdo' => 'Migración de Base de Datos',
                'descripcion' => 'Migrar los datos históricos de SQL Server a PostgreSql.',
                'responsable' => 'Juan Pérez',
                'apoyo' => 'DBA',
                'fecha_compromiso' => Carbon::now()->subDays(5),
                'estatus' => 'finalizado',
                'prioridad' => 'alta',
                'porcentaje_avance' => 100,
                'fecha_cierre' => Carbon::now()->subDays(1),
            ],
            [
                'area' => 'Ventas',
                'tipo_cuadrante' => 2,
                'acuerdo' => 'Nuevo CRM Implementation',
                'descripcion' => 'Configuración de módulos básicos para el departamento de ventas.',
                'responsable' => 'Ana Martínez',
                'apoyo' => 'Sistemas',
                'fecha_compromiso' => Carbon::now()->addDays(15),
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 60,
            ],
        ];

        foreach ($acuerdos as $acuerdo) {
            Acuerdo::create($acuerdo);
        }
    }
}
