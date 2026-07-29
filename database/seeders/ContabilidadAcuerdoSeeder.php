<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class ContabilidadAcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Contabilidad',
                'actividad' => 'Registros, conciliaciones, duplicidades, complementos.',
                'descripcion' => 'Revisión de facturas recibidas y emitidas, así como complementos de pago, facturas duplicadas y facturas pagadas con transferencia y no hay salida de bancos',
                'tipo_cuadrante' => '2',
                'responsable' => 'C.P Omar Aguilar / C.P Yasmin / todas las áreas',
                'acuerdo' => 'Revisión de facturas, complementos de pago y conciliación de cuentas bancarias',
                'fecha_compromiso' => '2026-02-06',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Contabilidad',
                'actividad' => 'Cancelación de facturas ISSSTE, cancelacion de complemento de pago ISSSTE.',
                'descripcion' => 'Cancelación de facturas',
                'tipo_cuadrante' => '1',
                'responsable' => 'Contabilidad / ISSSTE',
                'acuerdo' => 'Espera acepte la institución la cancelación',
                'fecha_compromiso' => '2026-02-28',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Espera de cancelación'
            ],
            [
                'area' => 'Contabilidad',
                'actividad' => 'Entrega de reporte control de gastos',
                'descripcion' => 'Entrega del reporte detallado Control de gastos correspondiente a Enero 2026',
                'tipo_cuadrante' => '1',
                'responsable' => 'Todas las áreas',
                'acuerdo' => 'Entrega de excel Control de gastos',
                'fecha_compromiso' => '2026-02-05',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Entregan todas las áreas'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
