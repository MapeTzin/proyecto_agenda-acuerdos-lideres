<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class CalidadAcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Aseguramiento de Calidad',
                'actividad' => 'Programa Anual de Capacitación',
                'descripcion' => 'Se entrega observaciones',
                'tipo_cuadrante' => '1',
                'responsable' => 'Calidad / RH',
                'acuerdo' => 'Revisar comentarios de Programa Anual de Capacitación y programar firma',
                'fecha_compromiso' => '2026-01-30',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Aseguramiento de Calidad',
                'actividad' => 'Actualización Mayor del Sistema de Gestión de Calidad',
                'descripcion' => 'Gestionar el riesgo que implica realizar la actualización del SGC',
                'tipo_cuadrante' => '1',
                'responsable' => 'Calidad',
                'acuerdo' => 'Generar Control de Cambios',
                'fecha_compromiso' => '2026-01-30',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => 'Atraso de 5 días según reporte'
            ],
            [
                'area' => 'Aseguramiento de Calidad',
                'actividad' => 'Terminar recepción de insumo en un lapso de 24 horas',
                'descripcion' => 'No se tiene Certificado Analitico del Lote 2540311 Certificados en inglés de la remisión 22 de eyectores',
                'tipo_cuadrante' => '1',
                'responsable' => 'Compras / Calidad / Almacén',
                'acuerdo' => 'Entrega de Certificados analiticos',
                'fecha_compromiso' => '2026-01-27',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Atraso de 8 días según reporte'
            ],
            [
                'area' => 'Aseguramiento de Calidad',
                'actividad' => 'Seguir el proceso de recepción de Calcimol',
                'descripcion' => 'Dar atención a la recepción de Insumo en un lapso de 24 horas',
                'tipo_cuadrante' => '1',
                'responsable' => 'Almacén / Calidad',
                'acuerdo' => 'Participación activa en la recepción del insumo',
                'fecha_compromiso' => '2026-02-04',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Aseguramiento de Calidad',
                'actividad' => 'Dar seguimiento Queja ISSSTE por suturas',
                'descripcion' => 'Verificar que se hayan entregado las muestras al laboratorio tercer autorizado',
                'tipo_cuadrante' => '2',
                'responsable' => 'Ventas / Calidad',
                'acuerdo' => 'Verificar la entrega para dar seguimiento con la institución',
                'fecha_compromiso' => '2026-02-06',
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
