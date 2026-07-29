<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class AlmacenAcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Almacen',
                'actividad' => 'Gestiones internas: CAPACITACION DEL EQUIPO DE LOGISTICA',
                'descripcion' => 'CAPACITACION Y EJERCICIOS SIMULADOS DE LA LOGISTICA',
                'tipo_cuadrante' => '1',
                'responsable' => 'GERENCIA, EMBARQUES',
                'acuerdo' => 'CAPACITACION Y EJERCICIOS SIMULADOS',
                'fecha_compromiso' => '2026-01-29',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 50,
                'comentarios' => ''
            ],
            [
                'area' => 'Almacen',
                'actividad' => 'Gestiones internas: Cierre de servicio Operador logistico',
                'descripcion' => 'Cerrar el servicio de agosto a diciembre',
                'tipo_cuadrante' => '1',
                'responsable' => 'sistemas, Almacen',
                'acuerdo' => 'Cruzar entradas, salidas, entregas y devolucion de material. cruzar emision de pedidos vs entregas realizadas',
                'fecha_compromiso' => '2026-01-28',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 50,
                'comentarios' => 'Se envia correo electronico a responsable sanitario'
            ],
            [
                'area' => 'Almacen',
                'actividad' => 'Gestiones internas: Junta con proveedores de trasporte',
                'descripcion' => 'Retomar administracion de los proveedores de trasporte',
                'tipo_cuadrante' => '1',
                'responsable' => 'Dirección, Daniel',
                'acuerdo' => 'se programan las reuniones con los proveedores mas robustos para el dia jueves y viernes',
                'fecha_compromiso' => '2026-01-21',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'en espera de confirmacion de fechas'
            ],
            [
                'area' => 'Almacen',
                'actividad' => 'Gestiones internas: Cuarentena libre Socios comerciales',
                'descripcion' => 'Se trabajara producto no conforme para liberar Inventario cuarentena',
                'tipo_cuadrante' => '2',
                'responsable' => 'Almacén, Calidad',
                'acuerdo' => 'Se trabaja en equipo con el área de calidad',
                'fecha_compromiso' => '2026-01-17',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Almacen',
                'actividad' => 'Cumplimiento dentro del almacén: Indicadores fijos',
                'descripcion' => '1. Niveles de inventario optimos, 2. Indicador de penalizacion <= 3%, 3. Indicador de rechazos <= 3%, 4. Etiquetado, 5. Inventario 99.9%, 6. Recepcion 99.9%',
                'tipo_cuadrante' => '2',
                'responsable' => 'Todas las áreas del almacen',
                'acuerdo' => 'Definir indicadores y reportes de avance quincenales.',
                'fecha_compromiso' => '2026-01-31',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Almacen',
                'actividad' => 'acuerdos internos: con cuentas por cobrar',
                'descripcion' => 'pendinetes que se tienen con el area de cuentas por cobrar: deductivas, parcialidades.',
                'tipo_cuadrante' => '1',
                'responsable' => 'documental, almacen y logistica',
                'acuerdo' => 'concluir temas pendientes.',
                'fecha_compromiso' => '2026-01-28',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
