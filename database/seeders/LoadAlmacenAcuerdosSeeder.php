<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;
use Carbon\Carbon;

class LoadAlmacenAcuerdosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'ALMACEN',
                'tipo_cuadrante' => 1,
                'actividad' => 'Inventario Fisico Almacén Xalostoc',
                'descripcion' => 'Ejercicio de Toma de Inventario Enero 2026',
                'responsable' => 'sistemas, Almacen',
                'acuerdo' => 'Se carga información de resultado de Toma de Inventario Fisico a módulo de Inventarios',
                'fecha_compromiso' => '2026-03-06',
                'porcentaje_avance' => 25,
                'estatus' => 'en_proceso',
                'comentarios' => 'Capacitación de Modulo de Almacén OK. Previo a carga de inventario en modulo de inventario aplica ejercicio para validar contra fisico cantidades y fecha de producción y caducidad en un 80% de catalogo fisico.'
            ],
            [
                'area' => 'ALMACEN',
                'tipo_cuadrante' => 1,
                'actividad' => 'Gestiones internas: Cierre de servicio Operador logistico',
                'descripcion' => 'Cerrar el servicio de agosto a diciembre',
                'responsable' => 'sistemas, Almacen',
                'acuerdo' => 'Cruzar entradas, salidas, entregas y devolucion de material. cruzar emision de pedidos vs entregas realizadas',
                'fecha_compromiso' => '2026-02-20',
                'porcentaje_avance' => 50,
                'estatus' => 'en_proceso',
                'comentarios' => 'El área de calidad a traves de Responsable sanitario lleva la gestion de información.'
            ],
            [
                'area' => 'ALMACEN',
                'tipo_cuadrante' => 1,
                'actividad' => 'Gestiones internas: Junta con proveedores de trasporte',
                'descripcion' => 'Retomar administracion de los proveedores de trasporte',
                'responsable' => 'Direccion, Marco M',
                'acuerdo' => 'se programan las reuniones con los proveedores mas robustos para el dia jueves y viernes',
                'fecha_compromiso' => '2026-02-11',
                'fecha_cierre' => '2026-02-13',
                'porcentaje_avance' => 100,
                'estatus' => 'finalizado',
                'comentarios' => 'Las reuniones aplicaron por parte de Dirección Mape, miércoles 11 de Febrero. Segusa Alfa S.A. De C.V. , Luis Alberto Cárdenas, Transportes Rambo. Dirección cuenta con informe de performance de cada Linea'
            ],
            [
                'area' => 'ALMACEN',
                'tipo_cuadrante' => 2,
                'actividad' => 'Gestiones internas: Cuarentena Libre Socios comerciales',
                'descripcion' => 'Se trabajara producto no conforme para liberar Inventario cuarentena',
                'responsable' => 'Almacén, Calidad',
                'acuerdo' => 'Se trabaja en equipo con el área de calidad',
                'fecha_compromiso' => '2026-02-26',
                'porcentaje_avance' => 40,
                'estatus' => 'en_proceso',
                'comentarios' => 'Operaciones Genera Informe de material en cuarentena a nivel clave y tipo de daño. 23 de Febrero aplica revisión de empaque secundario disponible a efecto de aplicar el acondicionado, el resto de material se define plan de acción retorno a proveedor y/o destrucción.'
            ],
            [
                'area' => 'ALMACEN',
                'tipo_cuadrante' => 2,
                'actividad' => 'Cumplimiento dentro del almacén: Indicadores fijos',
                'descripcion' => '1. Niveles de inventario optimos. 2. Indicador de penalizacion <= 3%. 3. Indicador de rechazos <= 3%. 4. Etiquetado de dispositivos. 5. Inventario confiable 99.9%. 6. Recepcion de materiales 99.9%',
                'responsable' => 'Todas las areas del almacen',
                'acuerdo' => 'Definir indicadores y reportes de avance quincenales.',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 50,
                'estatus' => 'en_proceso',
                'comentarios' => 'Metodología Institucional de Trabajo Planeación Estratégica 2026 Alineación 6 de Febrero 2026 Se establece dos sesiones de revisión para garantizar la correcta ejecución : Rituales de Control Diario: 08:15 hrs: Validación de remesas para asegurar la entrega en tiempo y forma. 17:00 hrs: Revisión de estatus final del día, documentación de incidencias y planificación de las actividades de inicio del día siguiente.'
            ],
            [
                'area' => 'ALMACEN',
                'tipo_cuadrante' => 1,
                'actividad' => 'acuerdos internos: con cuentas por cobrar',
                'descripcion' => 'pendientes que se tienen con el area de cuentas por cobrar: deductivas, parcialidades.',
                'responsable' => 'documental, almacen y logistica',
                'acuerdo' => 'concluir temas pendientes.',
                'fecha_compromiso' => '2026-02-10',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => 'Hoy aplica sesión Logística - Documental - Cx C cobrar'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
