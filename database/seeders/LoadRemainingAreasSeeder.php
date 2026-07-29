<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class LoadRemainingAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            // SERVICIOS A GOBIERNO
            [
                'area' => 'SERVICIOS A GOBIERNO',
                'tipo_cuadrante' => 2,
                'actividad' => 'Cumplimiento KPI presentación de actas al cliente y cobranza',
                'descripcion' => 'Cancelacion facturas',
                'responsable' => 'CxC, Contabilidad',
                'acuerdo' => 'Se solicita fecha compromiso para renovar la solicitud de cancelación',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => 'En espera de confirmación por parte de contabilidad. Se contactará a personal del ISSSTE para dar seguimiento.'
            ],
            [
                'area' => 'SERVICIOS A GOBIERNO',
                'tipo_cuadrante' => 2,
                'actividad' => 'Cumplimiento KPI presentación de actas al cliente y cobranza',
                'descripcion' => 'Seguimiento a pago Dic 2025',
                'responsable' => 'Jefe de proyecto, contabilidad',
                'acuerdo' => 'Se solicita a contabilidad dar seguimiento e informar sobre el depósito de pagos correspondientes',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se considera como periodo probable de depósito a partir del 13 de febrero.'
            ],
            [
                'area' => 'SERVICIOS A GOBIERNO',
                'tipo_cuadrante' => 1,
                'actividad' => 'Redireccionamiento del área',
                'descripcion' => 'Plan de trabajo anual. Revisión de PNO y estrategia de capacitación',
                'responsable' => 'Calidad, RH',
                'acuerdo' => 'Trabajo en conjunto para revisión documental y redireccionamiento de la actividad de capacitación',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 10,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se solicitará participación de todas las áreas para automatizar los procesos de capacitación.'
            ],
            [
                'area' => 'SERVICIOS A GOBIERNO',
                'tipo_cuadrante' => 1,
                'actividad' => 'Redireccionamiento del área',
                'descripcion' => 'Plan de trabajo anual. Área de logística terrestre',
                'responsable' => 'Calidad, Almacén',
                'acuerdo' => 'Revisión conjunta de PNO de Pochtecatl y elaboración de complementarios',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            [
                'area' => 'SERVICIOS A GOBIERNO',
                'tipo_cuadrante' => 1,
                'actividad' => 'Redireccionamiento del área',
                'descripcion' => 'Plan de trabajo anual. BPAD',
                'responsable' => 'Dirección, Almacén',
                'acuerdo' => 'Dirección requiere apoyo puntual (5 semanas)',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            [
                'area' => 'SERVICIOS A GOBIERNO',
                'tipo_cuadrante' => 2,
                'actividad' => 'Licitación IMSS agua CDMX',
                'descripcion' => 'Se presentará la propuesta de participación',
                'responsable' => 'Ventas',
                'acuerdo' => 'Se requiere trabajo en conjunto para preparar y entregar la propuesta de participación',
                'fecha_compromiso' => '2026-03-05',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            // ASIS ADM
            [
                'area' => 'ASIS ADM',
                'tipo_cuadrante' => 2,
                'actividad' => 'Ventas a privado',
                'descripcion' => 'Seguimiento a reportes y liberación de recursos correspondientes al mes de febrero',
                'responsable' => 'Sistemas / Asis Adm',
                'acuerdo' => 'Status de reportes y liberación de fondos — Ventas a privado, febrero',
                'fecha_compromiso' => '2026-02-28',
                'porcentaje_avance' => 25,
                'estatus' => 'en_proceso',
                'comentarios' => 'Reunión para abordar temas pendientes y acordar fechas compromiso.'
            ],
            [
                'area' => 'ASIS ADM',
                'tipo_cuadrante' => 2,
                'actividad' => 'Cita con transportistas',
                'descripcion' => 'Agendar de la mano con dirección cita con los transportistas actuales',
                'responsable' => 'Asistente de dirección, Dirección',
                'acuerdo' => 'Reunion con direccion',
                'fecha_compromiso' => '2026-02-20',
                'porcentaje_avance' => 20,
                'estatus' => 'en_proceso',
                'comentarios' => 'Seguimiento a reuniones realizadas con base en la minuta generada.'
            ],
            [
                'area' => 'ASIS ADM',
                'tipo_cuadrante' => 1,
                'actividad' => 'Seguimiento a tablero de control',
                'descripcion' => 'Asignación y seguimiento de cierre de actividades de las áreas',
                'responsable' => 'Asistente de dirección, todas las áreas',
                'acuerdo' => 'Cierre en tiempo',
                'fecha_compromiso' => '2026-02-20',
                'porcentaje_avance' => 25,
                'estatus' => 'en_proceso',
                'comentarios' => 'Actualizacion de acuerdos con los proveedores.'
            ],
            [
                'area' => 'ASIS ADM',
                'tipo_cuadrante' => 2,
                'actividad' => 'Actualización de afiliación y apertura de cuenta faltante',
                'descripcion' => 'Estatus de actualización de afiliación y apertura de cuenta pendiente',
                'responsable' => 'Asistente de dirección, Dirección',
                'acuerdo' => 'Seguimiento a documentación para apertura y renovación de afiliación',
                'fecha_compromiso' => '2026-02-28',
                'porcentaje_avance' => 25,
                'estatus' => 'en_proceso',
                'comentarios' => 'Seguimiento a documentación para apertura y renovación de afiliación.'
            ],
            // ADQUISICIONES
            [
                'area' => 'ADQUISICIONES',
                'tipo_cuadrante' => 1,
                'actividad' => 'Cumplimiento de KPI atención a clientes internos',
                'descripcion' => 'Entrega de requerimientos a las áreas',
                'responsable' => 'Coordinación de adquisiciones / Clientes internos',
                'acuerdo' => 'Requisiciones de compra en tiempo y forma mensual',
                'fecha_compromiso' => '2026-02-13',
                'porcentaje_avance' => 60,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se autorizo por parte de Direccion el lunes estaremos con las entregas puntuales.'
            ],
            [
                'area' => 'ADQUISICIONES',
                'tipo_cuadrante' => 1,
                'actividad' => 'Cumplimiento garantías equipo médico',
                'descripcion' => 'Servicios preventivos a unidades medicas',
                'responsable' => 'Coordinación de adquisiciones / Ventas',
                'acuerdo' => 'Monitorear y atender los requerimiento y garantías de equipos medicos.',
                'fecha_compromiso' => '2026-02-13',
                'porcentaje_avance' => 70,
                'estatus' => 'en_proceso',
                'comentarios' => 'Por parte del proveedor nos hara llegar la cotizacion para iniciar el día lunes en conjunto con Marlon.'
            ],
            [
                'area' => 'ADQUISICIONES',
                'tipo_cuadrante' => 2,
                'actividad' => 'Renovación de contratos: Mantenimientos, tenencia, predial, rentas, GPS, pólizas, CFE.',
                'descripcion' => 'Pagos correspondientes a las polizas de la compañía',
                'responsable' => 'Coordinación de adquisiciones / Dirección',
                'acuerdo' => 'Realizar todas las atenciones y pagos correspondientes al mes en curso en conjunto con contabilidad',
                'fecha_compromiso' => '2026-02-15',
                'porcentaje_avance' => 70,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se realizan en tiempo y forma para su pronto pago.'
            ],
            // SISTEMAS
            [
                'area' => 'SISTEMAS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Diseño digital',
                'descripcion' => 'Diseño digital en redes y comunicación interna. Imagen institucional y claridad operativa.',
                'responsable' => 'Sistemas',
                'acuerdo' => 'Se realizan pruebas de firma digital',
                'fecha_compromiso' => '2026-02-24',
                'porcentaje_avance' => 60,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se realizan pruebas de firma digital, en nitro.'
            ],
            [
                'area' => 'SISTEMAS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Sistema almacen',
                'descripcion' => 'Se inicia capacitacion del módulo de entradas',
                'responsable' => 'Sistemas y Almacen',
                'acuerdo' => 'Se realizarán acciones de revisión en la captura de inventario. El viernes 27 deberá estar registrado el inventario total en el sistema.',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 60,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se realizarán acciones de revisión en la captura de inventario. El viernes 27 deberá estar registrado el inventario total en el sistema.'
            ],
            [
                'area' => 'SISTEMAS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Programa Dashboards',
                'descripcion' => 'Realizar Gantt de prioridad sobre las áreas para la realización de dashboards',
                'responsable' => 'Sistemas',
                'acuerdo' => 'Realizar Gantt de prioridad sobre las áreas para la realización de dashboards presentarlo a dirección para su VoBo',
                'fecha_compromiso' => '2026-02-25',
                'porcentaje_avance' => 75,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se envió Gantt en espera de VoBo.'
            ],
            [
                'area' => 'SISTEMAS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Suit Documental y Lotificador libre de errores',
                'descripcion' => 'Revisar y ejecutar correcciones en la suit documental',
                'responsable' => 'Sistemas',
                'acuerdo' => 'Se realizaron actualizaciones en el proceso de carga de datos en la suite.',
                'fecha_compromiso' => '2026-02-25',
                'porcentaje_avance' => 50,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se realizaron modificaciones en la carga de información en la suite documental. El sistema permanecerá en observación para validar su correcto funcionamiento.'
            ],
            // CONTABILIDAD
            [
                'area' => 'CONTABILIDAD',
                'tipo_cuadrante' => 1,
                'actividad' => 'Declaración Anual',
                'descripcion' => 'Comenzar con papeles de trabajo, diferencias y armado',
                'responsable' => 'CON',
                'acuerdo' => 'Avanzar un 60% al terminar febrero',
                'fecha_compromiso' => '2026-02-28',
                'porcentaje_avance' => 60,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se identifican diferencias en meses declarados por falta de C.P.'
            ],
            [
                'area' => 'CONTABILIDAD',
                'tipo_cuadrante' => 1,
                'actividad' => 'Cierre mensual',
                'descripcion' => 'Conciliación de egresos e ingresos del periodo',
                'responsable' => 'CON',
                'acuerdo' => 'Cierre en tiempo y forma',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => '1 a 1 con las áreas involucradas.'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
