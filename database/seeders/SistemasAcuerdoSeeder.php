<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class SistemasAcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Sistemas',
                'actividad' => 'Cumplimiento de tickets atención a clientes internos',
                'descripcion' => 'Atender y cerrar tickets de soporte en máximo 48 hrs; actualizar bitácora diaria de incidencias; monitorear cumplimiento de SLA semanal (meta: 95%).',
                'tipo_cuadrante' => '1',
                'responsable' => 'Sistemas Victor Arochi, Karen Lopez, David Jimenez',
                'acuerdo' => 'Se revisara por area',
                'fecha_compromiso' => '2026-02-28',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'que la atencion a clientes es diaria, y se lleva el control mediante los tickets y evidencias en bitácora diaria.'
            ],
            [
                'area' => 'Sistemas',
                'actividad' => 'Monitores, suits',
                'descripcion' => 'Se entrega ERP de Almacen, en el cual se engloban los Modulos de registros de contratos, empresas almacenes, ubicaciones, entradas, movimientos internos, salidas, Reportes de inventarios, entradas, salidas, kardex.',
                'tipo_cuadrante' => '1',
                'responsable' => 'Sistemas y Almacen',
                'acuerdo' => 'facilite la administración integral de las operaciones, incluyendo control de existencias, registro de entradas y salidas, trazabilidad de lotes y generación de reportes de inventarios, con información confiable y oportuna.',
                'fecha_compromiso' => '2026-02-04',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se esta trabajando en monitor de Contabilidad no se ha recibido por parte de contabilidad proceso de calculo de resultados.'
            ],
            [
                'area' => 'Sistemas',
                'actividad' => 'Sistema Almacen',
                'descripcion' => 'Se termino la parte de entradas y se continua trabajando con lotificaciones',
                'tipo_cuadrante' => '1',
                'responsable' => 'Sistemas y Almacen',
                'acuerdo' => 'Se termino modulo de entradas, se avanza con el modulo de lotificaciones',
                'fecha_compromiso' => '2026-02-03',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se tiene avance se termino el modulo de entradas con los cambios requeridos se trabaja en la adaptacion de los modulos de movimientos y salida. Atraso 1 dia.'
            ],
            [
                'area' => 'Sistemas',
                'actividad' => 'comparativa de precios para servicios de Internet',
                'descripcion' => 'confirmar que el proveedor con el que se esta trabajando tiene el mejor costo, presentar cuadro comparativo',
                'tipo_cuadrante' => '1',
                'responsable' => 'Sistemas / ASISADM',
                'acuerdo' => '15 dias naturales confirme que este proveedor sigue siendo el mejor costo posible, presente de forma enunciativa el cuadro comparativo.',
                'fecha_compromiso' => '2026-02-11',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se queda abierto todo el mes debido a que los servicios trabajan diario. Se documenta en agenda.'
            ],
            [
                'area' => 'Sistemas',
                'actividad' => 'Revisión y optimización de kickoff',
                'descripcion' => 'Se realizaran optimizaciones al kickoff para su mejor lectura y comprension',
                'tipo_cuadrante' => '1',
                'responsable' => 'Sistemas / Documental',
                'acuerdo' => 'El area de sistemas se juntara con el area documental para realizar esta actividad',
                'fecha_compromiso' => '2026-02-04',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se realizo un modelo para pasar el Kickoff a sistema para revisión en cualquier momento y avisos por correo de acuerdos vencidos.'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
