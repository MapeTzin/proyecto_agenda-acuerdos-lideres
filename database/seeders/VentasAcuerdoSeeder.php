<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class VentasAcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Ventas',
                'actividad' => 'Ventas: revisión total ventanas, vínculos Institutos y distribuidores aliados; ejecución de procesos licitatorios',
                'descripcion' => 'Seguimiento Incidencias BIRMEX // Grabador // Adjudicación Directa',
                'tipo_cuadrante' => '1',
                'responsable' => 'Direccion Comercial',
                'acuerdo' => 'Precios y confirmacion de partidas para participar',
                'fecha_compromiso' => '2026-02-03',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Gramaje Acido Grabador 3.5 jueves 29 de enero 30/01/2026//Resultado IM // Cita Marlon'
            ],
            [
                'area' => 'Ventas',
                'actividad' => 'Formalización contratos y fianzas, convenios',
                'descripcion' => 'Formalización I97, I177, I218',
                'tipo_cuadrante' => '2',
                'responsable' => 'CxC, Comercial, Documental, Almacén',
                'acuerdo' => 'Entrega de documentos para elaborar contrato',
                'fecha_compromiso' => '2026-03-06',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 100, // Verde en semáforo pero fecha futura, lo marco avanzado
                'comentarios' => 'Cumplir con el esquema de tiempos establecidos Consolidar garantias'
            ],
            [
                'area' => 'Ventas',
                'actividad' => 'Cumplimiento de plan de trabajo, Meta Smart, KPI, Bases de datos, aplicación IA',
                'descripcion' => 'Seguimiento expediente de cobranza bienestar kit 1088. Seguimiento expediente ionolux conciliación y queja.',
                'tipo_cuadrante' => '1',
                'responsable' => 'Dirección General, Comercial, Almacen',
                'acuerdo' => 'Cartas y convenios comerciales',
                'fecha_compromiso' => '2026-02-04',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Entrega de escritos 06/02/2026 Entrega de conciliación 06/02/2023 Carta terminación anticipada'
            ],
            [
                'area' => 'Ventas',
                'actividad' => 'Proyectos Especiales',
                'descripcion' => 'Reuniones socios comerciales Enero Reunion PDM 9-12 28/01/2026 AMCO 05/02/2026 12:00 NEODREN 04/02/2026 10:00',
                'tipo_cuadrante' => '1',
                'responsable' => 'Dirección General, Comercial, Almacen, CxC',
                'acuerdo' => 'Se difunde liga de acceso',
                'fecha_compromiso' => '2026-02-04',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Presentaciones AMCO Presentaciones NEODREN'
            ],
            [
                'area' => 'Ventas',
                'actividad' => 'Otros',
                'descripcion' => 'Seguimiento Gantt Proyecto consolidado 2027-2028',
                'tipo_cuadrante' => '1',
                'responsable' => 'Ventas',
                'acuerdo' => 'Seguimiento y actualización',
                'fecha_compromiso' => '2026-02-02',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Revisión de Data Envio de Respaldos a socios comerciales'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
