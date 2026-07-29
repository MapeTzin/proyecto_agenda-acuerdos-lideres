<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class CxCAcuardoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Cuentas por cobrar',
                'actividad' => 'Equipo Médico Lesli',
                'descripcion' => 'Revisión de incidencias de documentación',
                'tipo_cuadrante' => '1',
                'responsable' => 'CXC, COMERCIAL, ADQUISICIONES',
                'acuerdo' => 'Revisión de incidencias de documentación, recabar sellos',
                'fecha_compromiso' => '2026-02-09',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 20,
                'comentarios' => 'Correo para ingresar a cobro de las unidades liberadas. El día 04 de febrero el lic javier se comunico con Repsis para los mantenimientos y a el le entregaron los mantenimientos. El día 12 de febrero el lic Marlon sussana las incidencias de las unidades'
            ],
            [
                'area' => 'Cuentas por cobrar',
                'actividad' => 'Saldos clientes',
                'descripcion' => 'Tener saldos finales de todos los clientes de 2025',
                'tipo_cuadrante' => '2',
                'responsable' => 'CXC, CONTABILIDAD',
                'acuerdo' => 'Entregar reporte de clientes saldos finales al 2025',
                'fecha_compromiso' => '2026-01-19',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 30,
                'comentarios' => 'Revisión de Data. Atraso 16 días.'
            ],
            [
                'area' => 'Cuentas por cobrar',
                'actividad' => 'Revision facturas Proyecto ISSSTE',
                'descripcion' => 'Relacion de facturacion lesli, cruce con complementos de pago',
                'tipo_cuadrante' => '2',
                'responsable' => 'CXC, SERVICIOS A GOBIERNO, CONTABILIDAD',
                'acuerdo' => 'Revisar facturacion por cancelar del proyecto issste',
                'fecha_compromiso' => '2025-12-31',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se esta revisando con medida de contencion por las áreas involucradas. Atraso 35 días.'
            ],
            [
                'area' => 'Cuentas por cobrar',
                'actividad' => 'Remisiones con incidencia, remisiones arcar',
                'descripcion' => 'Recepcion de remisiones con arcar, retorno de remisiones con incidencia.',
                'tipo_cuadrante' => '2',
                'responsable' => 'CXC, ALMACEN',
                'acuerdo' => 'Fecha compromiso de retorno con incidencias almacen',
                'fecha_compromiso' => '2026-01-31',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 20,
                'comentarios' => 'Seguimiento de guias, recuperación de sellos. Atraso 4 días.'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
