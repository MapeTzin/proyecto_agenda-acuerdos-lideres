<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;
use Carbon\Carbon;

class AreaAlmacenSeeder extends Seeder
{
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Almacen',
                'tipo_cuadrante' => 1,
                'actividad' => 'Gestiones internas: CAPACITACION DEL EQUIPO DE LOGISTICA',
                'acuerdo' => 'CAPACITACION Y EJERCICIOS SIMULADOS DE LA LOGISTICA',
                'descripcion' => '',
                'responsable' => 'GERENCIA, EMBARQUES',
                'apoyo' => 'Soporte TI',
                'fecha_compromiso' => Carbon::create(2026, 1, 29),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 25,
                'comentarios' => ''
            ],
            [
                'area' => 'Almacen',
                'tipo_cuadrante' => 1,
                'actividad' => 'Gestiones internas: Cierre de servicio Operador logistico',
                'acuerdo' => 'Cruzar entradas, salidas, entregas y devolucion de material; cruzar emision de pedidos vs entregas realizadas.',
                'descripcion' => 'Cerrar el servicio de agosto a diciembre.',
                'responsable' => 'sistemas, Almacen',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 28),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 60,
                'comentarios' => 'Se envia correo electronico a responsable sanitario'
            ],
            [
                'area' => 'Almacen',
                'tipo_cuadrante' => 1,
                'actividad' => 'Gestiones internas: Junta con proveedores de trasporte',
                'acuerdo' => 'Se programan las reuniones con los proveedores mas robustos para el dia jueves y viernes.',
                'descripcion' => 'Retomar administracion de los proveedores de trasporte.',
                'responsable' => 'Direccion, Daniel',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 21),
                'estatus' => 'detenido',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'En espera de confirmacion de fechas'
            ],
            [
                'area' => 'Almacen',
                'tipo_cuadrante' => 2,
                'actividad' => 'Gestiones internas: Cuarentena libre Socios comerciales',
                'acuerdo' => 'Se trabaja en equipo con el área de calidad.',
                'descripcion' => 'Se trabajara producto no conforme para liberar Inventario cuarentena.',
                'responsable' => 'Almacén, Calidad',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 17),
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 50,
                'comentarios' => ''
            ],
            [
                'area' => 'Almacen',
                'tipo_cuadrante' => 2,
                'actividad' => 'Cumplimiento dentro del almacén: Indicadores fijos',
                'acuerdo' => 'Definir indicadores y reportes de avance quincenales.',
                'descripcion' => 'Indicadores: Niveles de inventario, penalizacion < 3%, rechazos < 3%, etiquetado, inventario confiable 99.9%, recepcion 99.9%.',
                'responsable' => 'Todas las áreas del almacén',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 31),
                'estatus' => 'pendiente',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Almacen',
                'tipo_cuadrante' => 1,
                'actividad' => 'Acuerdos internos: con cuentas por cobrar',
                'acuerdo' => 'Concluir temas pendientes.',
                'descripcion' => 'Pendientes que se tienen con el area de cuentas por cobrar: deductivas, parcialidades.',
                'responsable' => 'documental, almacen y logistica',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 28),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 30,
                'comentarios' => ''
            ]
        ];

        foreach ($acuerdos as $acuerdoData) {
            $acuerdo = Acuerdo::create($acuerdoData);

            // Create some sample historical progress
            $currentDate = Carbon::today();
            for ($i = 5; $i >= 0; $i--) {
                $date = (clone $currentDate)->subDays($i);
                // Calculate a progressive advance based on the final percentage
                $progression = floor(($acuerdoData['porcentaje_avance'] / 6) * (6 - $i));

                \App\Models\AvanceDiario::create([
                    'acuerdo_id' => $acuerdo->id,
                    'fecha' => $date,
                    'porcentaje_avance' => $progression
                ]);
            }
        }
    }
}
