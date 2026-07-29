<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Acuerdo;

class LoadCalidadCXCAcuerdosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $acuerdos = [
            // ASEGURAMIENTO DE CALIDAD
            [
                'area' => 'ASEGURAMIENTO DE CALIDAD',
                'tipo_cuadrante' => 1,
                'actividad' => 'Vaciar el área de cuarentena',
                'descripcion' => 'Verificar que se soliciten canjes a proveedor y se realicen los retrabajos',
                'responsable' => 'Almacén / Calidad / comercial',
                'acuerdo' => 'Gestion de canje hilo dental y papel indicador',
                'fecha_compromiso' => '2026-02-17',
                'porcentaje_avance' => 10,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            [
                'area' => 'ASEGURAMIENTO DE CALIDAD',
                'tipo_cuadrante' => 1,
                'actividad' => 'Cambio de repuestos de Trampa de Luz',
                'descripcion' => 'Cambio de repuesto',
                'responsable' => 'Calidad / Adquisiciones',
                'acuerdo' => 'Compromiso de fecha de entrega de repuestos y colocación 13-Feb-26',
                'fecha_compromiso' => '2026-02-16',
                'porcentaje_avance' => 40,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            [
                'area' => 'ASEGURAMIENTO DE CALIDAD',
                'tipo_cuadrante' => 1,
                'actividad' => 'Canjes de Producto',
                'descripcion' => 'Gestionar canje con PDM Gel ácido',
                'responsable' => 'Compras / Calidad / Almacén',
                'acuerdo' => 'Fecha de entrega de producto 20-Feb-26',
                'fecha_compromiso' => '2026-02-13',
                'porcentaje_avance' => 50,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            [
                'area' => 'ASEGURAMIENTO DE CALIDAD',
                'tipo_cuadrante' => 2,
                'actividad' => 'Dispositivos Médicos para Donación y Destrucción',
                'descripcion' => 'Segregar producto y documentar destinos',
                'responsable' => 'Almacén / Gerencia / Adquisiciones / Contabilidad / Calidad',
                'acuerdo' => 'Revisar los destinos para donación, con la parte de contabilidad se tienen requisitos para la donación. Adquisiciones tiene visibilidad de Proveedor de Destrucción',
                'fecha_compromiso' => '2026-02-23',
                'porcentaje_avance' => 30,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            [
                'area' => 'ASEGURAMIENTO DE CALIDAD',
                'tipo_cuadrante' => 2,
                'actividad' => 'Dar seguimiento Queja ISSSTE por suturas para lograr el cierre a mas tardar 31 de marzo',
                'descripcion' => 'Generar reunión con Q. Arely para presentación del caso y revisar soluciones',
                'responsable' => 'Ventas / Calidad',
                'acuerdo' => 'El Proveedor se comprometio a entregar al Laboratorio de Tercería las muestras para el el dia jueves 26-Feb-26. Gestionar fecha de reunión con Q. Arely, por llamada o presencial',
                'fecha_compromiso' => '2026-02-20',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => ''
            ],
            // CUENTAS POR COBRAR
            [
                'area' => 'CUENTAS POR COBRAR',
                'tipo_cuadrante' => 2,
                'actividad' => 'Registro de Cobro de M+T a socios',
                'descripcion' => 'Desglose de ganancias socios vs M+T',
                'responsable' => 'CXC/ALM/COM',
                'acuerdo' => 'Entregar reporte de clientes saldos finales al 2025',
                'fecha_compromiso' => '2026-02-27',
                'porcentaje_avance' => 85,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se entrega el viernes 13 el deglose del socio Drenovac, se hace planeación para entregar de los 3 socios restantes y entregar en fecha compromiso'
            ],
            [
                'area' => 'CUENTAS POR COBRAR',
                'tipo_cuadrante' => 2,
                'actividad' => 'Revisión facturas Proyecto ISSSTE',
                'descripcion' => 'Relación de facturacion mape, cruce con complementos de pago',
                'responsable' => 'CXC/SG/CON',
                'acuerdo' => 'Revisar facturación por cancelar del proyecto issste',
                'fecha_compromiso' => '2025-12-31',
                'porcentaje_avance' => 0,
                'estatus' => 'en_proceso',
                'comentarios' => 'Se esta revisando con medida de contención por las áreas involucradas'
            ],
            [
                'area' => 'CUENTAS POR COBRAR',
                'tipo_cuadrante' => 1,
                'actividad' => 'Remisiones con incidencia, remisiones arcar',
                'descripcion' => 'Recepcion de remisiones con arcar, retorno de remisiones con incidencia.',
                'responsable' => 'CXC/ALM',
                'acuerdo' => 'Fecha compromiso de retorno con incidencias almacén',
                'fecha_compromiso' => '2026-02-24',
                'porcentaje_avance' => 50,
                'estatus' => 'en_proceso',
                'comentarios' => 'El 17 de febrero se hace cierre de arcar El 20 de febrero se revisa PNO de citas y deductivas de IMSS-BIENESTAR el 24 de febrero, se entrega PNO, cátalogo y proceso de facturación de Institutos'
            ],
        ];

        foreach ($acuerdos as $data) {
            Acuerdo::create($data);
        }
    }
}
