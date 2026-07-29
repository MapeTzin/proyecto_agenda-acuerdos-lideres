<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class LoadVentasAcuerdosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'VENTAS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Ventas: revisión total ventanas, vínculos Institutos y distribuidores aliados; ejecución de procesos licitatorios',
                'descripcion' => 'Fallo ICHISAL. Comunicación Efectiva Fallos I110/T2',
                'responsable' => 'Comercial, CXC, Almacen, Documental, Asuntos Regulatorios',
                'acuerdo' => 'Notificación de Fallo. Reunión virtual para comentarios de proyecto',
                'fecha_compromiso' => '2026-03-11',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            [
                'area' => 'VENTAS',
                'tipo_cuadrante' => 2,
                'actividad' => 'Formalización contratos, fianzas y convenios',
                'descripcion' => 'Formalización I187, I177, I218, I110, T2',
                'responsable' => 'CxC, Documental',
                'acuerdo' => 'Entrega en Instituciones de cheques LFCB y envio de documentos para elaboración de contrato',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => 'Entrega de cheques en Institutos y apersonamiento de formalización'
            ],
            [
                'area' => 'VENTAS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Cumplimiento de plan de trabajo, Meta Smart, KPI, Bases de datos, aplicación IA',
                'descripcion' => 'Seguimiento expediente ionolux conciliación y queja.',
                'responsable' => 'Dirección Comercial',
                'acuerdo' => 'Entrega de conciliación ONIPO',
                'fecha_compromiso' => '2026-02-25',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => 'Entrega de petitorios MGK 20/02/2026'
            ],
            [
                'area' => 'VENTAS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Proyectos Especiales',
                'descripcion' => 'Reuniones Socios Comerciales. Avance del proyecto consolidado 2025-2026',
                'responsable' => 'Dirección Comercial, CXC, Almacen, Documental, Asuntos Regulatorios',
                'acuerdo' => 'Presentación en excelencia. Notas Comerciales 2027-2026',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => "Notificación de ligas de acceso 23 de febrero del 2026. Inicio de reuniones 24/02/2026 // Revisar planeador"
            ],
            [
                'area' => 'VENTAS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Otros',
                'descripcion' => 'Consolidado 2027-2028. Respaldos Consolidado 2027-2028. Visita presencial Socios ONIPO/PDM/NEODREN',
                'responsable' => 'Comercial, Dirección',
                'acuerdo' => 'Envio de Notas Comerciales. Confirmación y fecha de reunión',
                'fecha_compromiso' => '2026-03-06',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => "Revisión de Data. Envio de Respaldos a socios comerciales 18/02/2026. Citas presenciales Primera semana de Marzo. En espera de confirmación de fechas"
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
