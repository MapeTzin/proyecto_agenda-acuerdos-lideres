<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class AdquisicionesAcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Adquisiciones',
                'actividad' => 'Cumplimiento de KPI atención a clientes internos',
                'descripcion' => 'Entrega de requerimientos a las áreas',
                'tipo_cuadrante' => '1',
                'responsable' => 'Coordinación de adquisiciones / Clientes',
                'acuerdo' => 'Requisiciones de compra',
                'fecha_compromiso' => '2026-02-06',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se comparten 2 cotizaciones de bolsa impresa / Se cotiza servicio correctivo de Partner 2020'
            ],
            [
                'area' => 'Adquisiciones',
                'actividad' => 'Cumplimiento garantías equipo médico',
                'descripcion' => 'Servicios preventivos a unidades medicas',
                'tipo_cuadrante' => '1',
                'responsable' => 'Coordinación de adquisiciones / Ventas',
                'acuerdo' => 'Monitorear y atender los requerimiento y garantias de equipos medicos.',
                'fecha_compromiso' => '2026-02-05',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se agenda entrega de 2 equipos para el dia 05 de feb a las unidades de Beatriz Velazco-Manuel Dominguez'
            ],
            [
                'area' => 'Adquisiciones',
                'actividad' => 'Cumplimiento programa con calendario: mantenimientos, tenencia, predial, rentas, GPS, pólizas.',
                'descripcion' => 'Póliza de mercancias Lesli',
                'tipo_cuadrante' => '2',
                'responsable' => 'Coordinación de adquisiciones / Dirección',
                'acuerdo' => 'Realizar todas las atenciones y pagos correspondientes al mes en curso',
                'fecha_compromiso' => '2026-02-13',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Póliza de mercancias Lesli Fatima (enero): se solicitan cotizaciones / Arrendamiento calle 10 contrato de arrendamiento / Entrega de Inmueble P. ISSSTE 06 de feb'
            ],
            [
                'area' => 'Adquisiciones',
                'actividad' => 'Cumplimiento de plan de trabajo: Reportes, Meta Smart, KPI, Bases de datos, aplicación IA',
                'descripcion' => 'Meta RESICO',
                'tipo_cuadrante' => '1',
                'responsable' => 'Coordinación de adquisiciones / Contabilidad / P. ISSSTE',
                'acuerdo' => 'Presentación de resultados',
                'fecha_compromiso' => '2026-02-04',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Se entregan cotizaciones al cierre de enero el día 04 de feb'
            ],
            [
                'area' => 'Adquisiciones',
                'actividad' => 'otros:',
                'descripcion' => 'Equipo ultrasonico estomatologico',
                'tipo_cuadrante' => '2',
                'responsable' => 'Coordinación de adquisiciones / Ventas',
                'acuerdo' => 'Envio de carta y video de capacitación',
                'fecha_compromiso' => '2026-01-30',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Ya se trabaja sobre el video, se contemplan ediciones. Atraso 5 dias.'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
