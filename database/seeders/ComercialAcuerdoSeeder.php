<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class ComercialAcuerdoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            [
                'area' => 'Comercial',
                'actividad' => 'Generación puntual y administración de ordenes de compra.',
                'descripcion' => 'Entrega nuevas adjudicaciones LFCB',
                'tipo_cuadrante' => '1',
                'responsable' => 'Gerente de almacen, coordinador de almacen he inventarios, logística, contra etiquetado',
                'acuerdo' => 'Coordinar en excelencia la srecolecciones y citas de las nuevas ordenes',
                'fecha_compromiso' => '2026-01-06',
                'estatus' => 'en_proceso',
                'prioridad' => 'alta',
                'porcentaje_avance' => 0,
                'comentarios' => 'Neodren tiene material: 8,000 de la clave 1865 y 6,000 de la clave 1873 Neodren ya entrega PDM entrega hoy.'
            ],
            [
                'area' => 'Comercial',
                'actividad' => 'otros',
                'descripcion' => 'Recuperar firmas s402 revision cxc',
                'tipo_cuadrante' => '1',
                'responsable' => 'Mtra Wendy, CxC.',
                'acuerdo' => 'Solicitar respaldo para poder asistir a las unidades a recuperar firmas , apoyo en revision documental',
                'fecha_compromiso' => '2026-02-12',
                'estatus' => 'en_proceso',
                'prioridad' => 'media',
                'porcentaje_avance' => 0,
                'comentarios' => ''
            ],
            [
                'area' => 'Comercial',
                'actividad' => 'Relaciones, problemas socios comerciales e institucionales clave',
                'descripcion' => 'Seguimiento general de relaciones comerciales',
                'tipo_cuadrante' => '1',
                'responsable' => 'Gerencia Comercial',
                'acuerdo' => 'Mantener comunicación constante con socios',
                'fecha_compromiso' => '2026-02-28',
                'estatus' => 'pendiente',
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
