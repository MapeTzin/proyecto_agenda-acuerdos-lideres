<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class ServiciosGobiernoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Servicios a gobierno',
                'actividad' => 'Cumplimiento KPI presentación de actas al cliente y cobranza',
                'descripcion' => 'Se mantiene al día requisitado de KPI. Verificados pagos hasta octubre. Monitoreo de servicio 70%',
                'tipo_cuadrante' => '2',
                'responsable' => 'CxC',
                'acuerdo' => 'Se solicita apoyo de la Lic. Alejandra, para dar seguimiento en el ISSSTE',
                'fecha_compromiso' => '2025-12-31',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 63,
                'comentarios' => 'Fecha limite 28/01/26. Atraso 35 días.'
            ],
            [
                'area' => 'Servicios a gobierno',
                'actividad' => 'Redireccionamiento del área',
                'descripcion' => 'Plan de trabajo anual',
                'tipo_cuadrante' => '1',
                'responsable' => 'Dirección general, gerente',
                'acuerdo' => 'Elaborar plan de trabajo',
                'fecha_compromiso' => '2026-02-02',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 50,
                'comentarios' => 'Revisión el 28/1/26. Atraso 2 días.'
            ],
            [
                'area' => 'Servicios a gobierno',
                'actividad' => 'Cumplimiento de plan de trabajo: Reportes, Meta Smart, KPI, Bases de datos, aplicación IA',
                'descripcion' => 'Reportes a Dirección: 100%, KPI: 100%, Bases de datos: 98%, Análisis IA: por evento',
                'tipo_cuadrante' => '1',
                'responsable' => 'Gerente, Jefe de proyecto',
                'acuerdo' => 'Se solicita apoyo del Lic. César, para conocer su estrategia de búsqueda de licitaciones (27/1/26)',
                'fecha_compromiso' => '2026-01-30',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 15,
                'comentarios' => 'Análisis plataformas Compras y otras Institucionales para licitaciones. Atraso 5 días.'
            ],
            [
                'area' => 'Servicios a gobierno',
                'actividad' => 'Cambio de ubicación (Archivo)',
                'descripcion' => 'Archivo en resguardo del área',
                'tipo_cuadrante' => '1',
                'responsable' => 'Equipo completo',
                'acuerdo' => 'Se solicita atender las indicaciones enviadas vía correo: (asignar responsable y archivar de manera correcta)',
                'fecha_compromiso' => '2026-01-28',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 5,
                'comentarios' => 'Cajas para embalaje, apoyo para mudanza. Atraso 7 días.'
            ],
            [
                'area' => 'Servicios a gobierno',
                'actividad' => 'Cambio de ubicación (Inmueble)',
                'descripcion' => 'Entrega del inmueble y mudanza',
                'tipo_cuadrante' => '1',
                'responsable' => 'Apoyo de las áreas',
                'acuerdo' => 'Apoyo de personal de las áreas para mudanza: 29 al 31 de enero',
                'fecha_compromiso' => '2026-01-31',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 60,
                'comentarios' => 'Se solicita apoyo de 5 personas para la actividad (Sr. Francisco, Jaziel, Uriel, Miguel). Atraso 4 días.'
            ],
            [
                'area' => 'Servicios a gobierno',
                'actividad' => 'Otro: Lavado de cisternas MAPE',
                'descripcion' => 'Lavado de cisternas para matenimiento de inmuebles propios',
                'tipo_cuadrante' => '2',
                'responsable' => 'Jefe de proyecto, responsables de cada inmueble de MAPE, Coordinadora y Mtto',
                'acuerdo' => 'Se requiere reagendar para dar cumplimiento a las nuevas indicaciones de Dirección, sobre el área',
                'fecha_compromiso' => '2026-02-03',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => '19 ene Jardines de Santa Clara. 03 feb Xalostoc y Lesli. Atraso 1 día.'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
