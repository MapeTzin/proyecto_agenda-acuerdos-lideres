<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;
use Carbon\Carbon;

class MultiAreaSeeder extends Seeder
{
    public function run(): void
    {
        $acuerdos = [
            // VENTAS
            [
                'area' => 'Ventas',
                'tipo_cuadrante' => 1,
                'actividad' => 'Ventas: revisión total ventanas, vínculos Institutos y distribuidores aliados; ejecución de procesos licitatorios',
                'acuerdo' => 'Precios y confirmación de partidas para participar',
                'descripcion' => 'Seguimiento Incidencias BIRMEX // Grabador // Adjudicación Directa',
                'responsable' => 'Dirección Comercial',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 30),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Gramaje Acido Grabador 3.5 jueves 29 de enero 30/01/2026 // Resultado IM'
            ],
            [
                'area' => 'Ventas',
                'tipo_cuadrante' => 2,
                'actividad' => 'Formalización contratos y fianzas, convenios',
                'acuerdo' => 'Notificación de convenios formalizados y pendientes por formalizar.',
                'descripcion' => 'Notificacion de convenios pndientes contrato 127 CM 4,5,6',
                'responsable' => 'CxC Comercial Documental Almacén',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 30),
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Retiro de ingreso a cobro Fecha compromiso'
            ],
            [
                'area' => 'Ventas',
                'tipo_cuadrante' => 1,
                'actividad' => 'Proyectos Especiales',
                'acuerdo' => 'Se difunde liga de acceso',
                'descripcion' => 'Reuniones socios comerciales Enero AMCO 29/01/2026 NEODRE 30/01/2026',
                'responsable' => 'Dirección General Comercial Almacen CxC',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 30),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Confirmación de presentaciones en excelencia'
            ],

            // RECURSOS HUMANOS
            [
                'area' => 'Recursos Humanos',
                'tipo_cuadrante' => 1,
                'actividad' => 'Atraccion, Seleccion e Integracion de Talento',
                'acuerdo' => 'Calendarizar entrevistas',
                'descripcion' => '1.- Reclutamiento',
                'responsable' => 'Dirección, Juan Carlos, Anabel, Rocio, Antonio',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 30),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Recursos Humanos',
                'tipo_cuadrante' => 2,
                'actividad' => 'Cultura organizacional, Relaciones Laborales y CIima Organizacional',
                'acuerdo' => 'Presentar gráficas de clima laboral.',
                'descripcion' => '1.- Cuestionarios para medir el clima organizacional. Se hará un meet para presentar graficas con información completa del mes de diciembre 2025.',
                'responsable' => 'Personal de RRHH y todos los líderes, Gaby, Dirección',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 30),
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],

            // ASEGURAMIENTO DE CALIDAD
            [
                'area' => 'Aseguramiento de Calidad',
                'tipo_cuadrante' => 1,
                'actividad' => 'Terminar recepcion de insumo en un lapso de 24 horas',
                'acuerdo' => 'Entrega de Certificados analiticos',
                'descripcion' => 'Certificados en inglés de la remisión 22 de eyectores. Lote 2540311',
                'responsable' => 'Compras / Calidad / Almacén',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 27),
                'estatus' => 'detenido',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Aseguramiento de Calidad',
                'tipo_cuadrante' => 2,
                'actividad' => 'Inspección de piezas de recibo',
                'acuerdo' => 'Copia en los correos de arribos a Gerencia, Almacén, RS',
                'descripcion' => 'Requerimos tener visible los arribos',
                'responsable' => 'Compras / Almacén',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 30),
                'estatus' => 'finalizado',
                'prioridad' => 'media',
                'porcentaje_avance' => 100,
                'comentarios' => 'Completado'
            ],

            // CUENTAS POR COBRAR
            [
                'area' => 'Cuentas por cobrar',
                'tipo_cuadrante' => 1,
                'actividad' => 'Armado de documentación para ingreso a cobro',
                'acuerdo' => 'Generar toda la documentación para ingresar a cobro',
                'descripcion' => 'Realizar el kit de documentación para ingresar a cobro',
                'responsable' => 'CxC',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 30),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se está revisando la documentación para el ingreso al cobro'
            ],

            // SERVICIOS A GOBIERNO
            [
                'area' => 'Servicios a gobierno',
                'tipo_cuadrante' => 1,
                'actividad' => 'Redireccionamiento del área',
                'acuerdo' => 'Elaborar plan de trabajo',
                'descripcion' => 'Plan de trabajo anual',
                'responsable' => 'Dirección general, gerente',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 2, 2),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 50,
                'comentarios' => 'Revisión el 28/1/26'
            ],

            // SISTEMAS
            [
                'area' => 'Sistemas',
                'tipo_cuadrante' => 1,
                'actividad' => 'Sistema Almacen',
                'acuerdo' => 'Se termino modulo de entradas, se avanza con el modulo de lotificaciones',
                'descripcion' => 'Se termino la parte de entradas y se continua trabajando con lotificaciones',
                'responsable' => 'Sistemas y Almacen',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 2, 3),
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 70,
                'comentarios' => ''
            ],

            // CONTABILIDAD
            [
                'area' => 'Contabilidad',
                'tipo_cuadrante' => 1,
                'actividad' => 'Cancelacion de facturas ISSSTE',
                'acuerdo' => 'Espera acepte la institucion la cancelacion',
                'descripcion' => 'Cancelacion de complemento de pago ISSSTE',
                'responsable' => 'Contabilidad / ISSSTE',
                'apoyo' => '',
                'fecha_compromiso' => Carbon::create(2026, 1, 30),
                'estatus' => 'pendiente',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
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
