<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class AsisAdmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'ASIS ADM',
                'actividad' => 'Adecuación de formatos políticas de compras y adquisiciones',
                'descripcion' => 'Adecuación de formatos y políticas internas',
                'tipo_cuadrante' => '2',
                'responsable' => 'Adquisiciones / Asis Adm',
                'acuerdo' => 'Renovación de afiliaciones',
                'fecha_compromiso' => '2026-01-30',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Presentación y exposición de la propuesta el día 28 de Enero. Atraso 5 días.'
            ],
            [
                'area' => 'ASIS ADM',
                'actividad' => 'Cita con transportistas',
                'descripcion' => 'Agendar de la mano con dirección cita con los transportistas actuales',
                'tipo_cuadrante' => '2',
                'responsable' => 'Asistente de dirección, Dirección / RH',
                'acuerdo' => 'Reunión con dirección',
                'fecha_compromiso' => '2026-02-05',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 50,
                'comentarios' => 'Se cuenta con las fechas posibles de los transportistas solo falta cuadrarlas con dirección para comenzar esta semana.'
            ],
            [
                'area' => 'ASIS ADM',
                'actividad' => 'Actualización de información NAFIN',
                'descripcion' => 'Seguimiento actualización de información NAFIN',
                'tipo_cuadrante' => '1',
                'responsable' => 'Asistente de dirección, Auditor Administrativo',
                'acuerdo' => 'Cambio de cuentas',
                'fecha_compromiso' => '2026-01-30',
                'estatus' => 'finalizado',
                'prioridad' => 'media',
                'porcentaje_avance' => 100,
                'comentarios' => 'Cerrado.'
            ],
            [
                'area' => 'ASIS ADM',
                'actividad' => 'Entrega del calendario de mantenimientos del mes de febrero',
                'descripcion' => 'Actualización de calendario. Seguimiento a mantenimientos preventivos y correctivos abiertos. Unidades sin técnico asignado.',
                'tipo_cuadrante' => '2',
                'responsable' => 'Javier Ruiz / Gema Servin',
                'acuerdo' => 'Mantenimientos preventivos y correctivos',
                'fecha_compromiso' => '2026-02-28',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 10,
                'comentarios' => 'Se tienen identifiedas fechas de mantenimientos preventivos. Mantenimiento correctivo crítico en Unidad 10 (cerrado el 28 de enero). Mantenimientos sin técnico asignado en atención.'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
