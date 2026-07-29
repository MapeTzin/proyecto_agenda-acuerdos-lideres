<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class LoadComercialRHAcuerdosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            // COMERCIAL
            [
                'area' => 'COMERCIAL',
                'tipo_cuadrante' => 1,
                'actividad' => 'otros',
                'descripcion' => 'Recuperar firmas t402 revision cxc, segundo mantenimiento preventivo',
                'responsable' => 'Mtra Wendy, CxC, Adquisiciones',
                'acuerdo' => 'Solicitar respaldo para poder asistir a las unidades a recuperar firmas, apoyo en revision documental, asi como realizar el segundo mantenimiento preventivo',
                'fecha_compromiso' => '2026-03-06',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => 'se inicia la actividad en campo el dia lunes para concluir el 06 de marzo el 100% de las unidades'
            ],
            [
                'area' => 'COMERCIAL',
                'tipo_cuadrante' => 1,
                'actividad' => 'Relaciones, problemas socios comerciales e institucionales clave',
                'descripcion' => 'devolucion de producto con corta caducidad',
                'responsable' => 'G de Almacen, Asuntos regulatorios',
                'acuerdo' => 'coordinar la entrega del matrial una vez que se tenga el visto bueno del socio',
                'fecha_compromiso' => '2026-02-19',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            // RECURSOS HUMANOS
            [
                'area' => 'RECURSOS HUMANOS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Reclutamiento',
                'descripcion' => 'Cubrir vacantes de: Cuentas por cobrar, Almacén, Licitaciones, Ventas, Admon',
                'responsable' => 'Dirección, Anabel, Antonio, Marlon',
                'acuerdo' => '1.- Calendarizar entrevistas',
                'fecha_compromiso' => '2026-02-20',
                'porcentaje_avance' => 50,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se hacen acuerdos con los involucrados para agilizar las contrataciones'
            ],
            [
                'area' => 'RECURSOS HUMANOS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Onboarding',
                'descripcion' => 'Nuevos ingresos (Licitaciones, Contraetiquetado, Embarques, Adquisiciones)',
                'responsable' => 'Personal de RRHH, áreas involucradas',
                'acuerdo' => '1.- Responsable del área ejecutar de la mejor manera la incorporación de los nuevos ingresos respetando los espacios de la capacitación',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 50,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            [
                'area' => 'RECURSOS HUMANOS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Información prestaciones',
                'descripcion' => 'La actividad se queda en espera',
                'responsable' => 'Anabel y Marco A. López',
                'acuerdo' => '1.- Se generan nuevos acuerdos',
                'fecha_compromiso' => '2026-02-20',
                'porcentaje_avance' => 10,
                'estatus' => 'en_proceso',
                'comentarios' => 'Paro técnico'
            ],
            [
                'area' => 'RECURSOS HUMANOS',
                'tipo_cuadrante' => 2,
                'actividad' => 'Cultura Organizacional',
                'descripcion' => 'Evento del mes de febrero',
                'responsable' => 'Todo el personal de Mapetzin',
                'acuerdo' => '1.- En este mes se genera un meet con la Psicóloga',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 50,
                'estatus' => 'en_proceso',
                'comentarios' => 'En este mes de febrero no se llevará acabo la actividad presencial. Se comparte invitación el día de hoy.'
            ],
            [
                'area' => 'RECURSOS HUMANOS',
                'tipo_cuadrante' => 1,
                'actividad' => 'Capacitación',
                'descripcion' => 'Seguimiento puntual a las nuevas incorporaciones especialmente en el almacén. Así como en la de los becarios.',
                'responsable' => 'Personal de RRHH, áreas involucradas',
                'acuerdo' => '1.- Cumplir con gantt en tiempo y forma',
                'fecha_compromiso' => '2026-02-23',
                'porcentaje_avance' => 85,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se deberá concluir hoy lunes'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
