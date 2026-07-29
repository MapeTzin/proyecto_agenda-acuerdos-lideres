<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class RRHHAcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Recursos Humanos',
                'actividad' => 'Atracción, Selección e Integración de Talento (onboarding, capacitación, hab. blandas)',
                'descripcion' => '1.- Reclutamiento',
                'tipo_cuadrante' => '1',
                'responsable' => 'Dirección, Juan Carlos, Anabel, Rocio, Antonio, Cesar',
                'acuerdo' => '1.- Calendarizar entrevistas',
                'fecha_compromiso' => '2026-02-06',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Recursos Humanos',
                'actividad' => 'Cultura organizacional, Relaciones Laborales y Clima Organizacional',
                'descripcion' => '1.- Cuestionarios para medir el clima organizacional',
                'tipo_cuadrante' => '2',
                'responsable' => 'Personal de RRHH y todos los líderes, Gaby, Dirección',
                'acuerdo' => '1.- Presentar gráficas de clima laboral (Meet para diciembre 2025)',
                'fecha_compromiso' => '2026-02-06',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Recursos Humanos',
                'actividad' => 'Expedientes: Del personal, STPS, IMSS, Infonavit, Fonacot',
                'descripcion' => '1.- Registro del Reglamento Interior, 2.- Avance NOM-035-STPS, 3.- Calendario Anual temas normativos',
                'tipo_cuadrante' => '1',
                'responsable' => 'Anabel, Gaby, Rocio, Javier y Dirección',
                'acuerdo' => '1.- Entregar acuse de registro. 2.- Presentar resultados de cuestionarios Ley Silla',
                'fecha_compromiso' => '2026-02-13',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 100,
                'comentarios' => ''
            ],
            [
                'area' => 'Recursos Humanos',
                'actividad' => 'Formación y Desarrollo del Capital Humano',
                'descripcion' => '1.- Capacitación a nuevos ingresos',
                'tipo_cuadrante' => '2',
                'responsable' => 'Personal de RRHH, áreas involucradas',
                'acuerdo' => '1.- Cumplir con Gantt en tiempo y forma',
                'fecha_compromiso' => '2026-02-03',
                'estatus' => 'en_proceso', // Se marca como en_proceso pero con fecha vencida (Feb 3)
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Atraso de 1 día según reporte'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
