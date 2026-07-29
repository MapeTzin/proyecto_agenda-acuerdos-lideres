<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

$kpis = [
    [
        'name' => 'Cumplimiento fiscal oportuno',
        'code' => 'CONT_CUMP_FISC',
        'description' => 'Presentación de obligaciones fiscales en tiempo y forma.',
        'category' => 'CONTABILIDAD',
        'target' => 95.00,
        'values' => [
            1 => ['val' => 100, 'date' => '2026-05-26'],
            2 => ['val' => 91, 'date' => '2026-05-26'],
            3 => ['val' => 100, 'date' => '2026-05-26'],
            4 => ['val' => 100, 'date' => '2026-05-26'],
            5 => ['val' => 95, 'date' => '2026-06-19'],
            6 => ['val' => 95, 'date' => '2026-07-08'],
            7 => ['val' => 0, 'date' => '2026-07-08'],
        ],
        'note' => 'Se mantiene el cumplimiento mínimo requerido por la Metodología 2026, con seguimiento a obligaciones fiscales,'
    ],
    [
        'name' => 'Cierre contable mensual oportuno',
        'code' => 'CONT_CIER_MENS',
        'description' => 'Cumplimiento del cierre contable en los tiempos establecidos.',
        'category' => 'CONTABILIDAD',
        'target' => 95.00,
        'values' => [
            1 => ['val' => 92, 'date' => '2026-05-26'],
            2 => ['val' => 92, 'date' => '2026-05-26'],
            3 => ['val' => 87, 'date' => '2026-05-26'],
            4 => ['val' => 100, 'date' => '2026-05-26'],
            5 => ['val' => 90, 'date' => '2026-06-19'],
            6 => ['val' => 92, 'date' => '2026-07-08'],
            7 => ['val' => 0, 'date' => '2026-07-08'],
        ],
        'note' => 'El cierre de junio muestra mejor control respecto a mayo; sin embargo, aún requiere fortalecer tiempos de entrega,'
    ],
    [
        'name' => 'Deducibilidad documental de gastos',
        'code' => 'CONT_DDUC_GAST',
        'description' => 'Asegurar que los comprobantes cumplen con requisitos fiscales.',
        'category' => 'CONTABILIDAD',
        'target' => 95.00,
        'values' => [
            1 => ['val' => 82, 'date' => '2026-05-26'],
            2 => ['val' => 85, 'date' => '2026-05-26'],
            3 => ['val' => 78, 'date' => '2026-05-26'],
            4 => ['val' => 76, 'date' => '2026-05-26'],
            5 => ['val' => 88, 'date' => '2026-06-19'],
            6 => ['val' => 90, 'date' => '2026-07-08'],
            7 => ['val' => 0, 'date' => '2026-07-08'],
        ],
        'note' => 'Se observa avance en la revisión de CFDI, soportes, método y forma de pago; no obstante, todavía existen áreas de'
    ],
    [
        'name' => 'Comprobación de gastos operativos',
        'code' => 'CONT_COMP_OPER',
        'description' => 'Comprobación correcta de viáticos y gastos de operación.',
        'category' => 'CONTABILIDAD',
        'target' => 95.00,
        'values' => [
            1 => ['val' => 75, 'date' => '2026-05-26'],
            2 => ['val' => 72, 'date' => '2026-05-26'],
            3 => ['val' => 85, 'date' => '2026-05-26'],
            4 => ['val' => 11, 'date' => '2026-05-26'],
            5 => ['val' => 80, 'date' => '2026-06-19'],
            6 => ['val' => 85, 'date' => '2026-07-08'],
            7 => ['val' => 0, 'date' => '2026-07-08'],
        ],
        'note' => 'Mejora frente al mes anterior por mayor seguimiento a comprobaciones; sin embargo, aún permanece por debajo'
    ],
    [
        'name' => 'Precisión en registros contables',
        'code' => 'CONT_PREC_REGI',
        'description' => 'Exactitud en la información registrada en el sistema.',
        'category' => 'CONTABILIDAD',
        'target' => 95.00,
        'values' => [
            1 => ['val' => 88, 'date' => '2026-05-29'],
            2 => ['val' => 90, 'date' => '2026-05-26'],
            3 => ['val' => 87, 'date' => '2026-05-26'],
            4 => ['val' => 92, 'date' => '2026-05-26'],
            5 => ['val' => 100, 'date' => '2026-06-19'],
            6 => ['val' => 95, 'date' => '2026-07-08'],
            7 => ['val' => 0, 'date' => '2026-07-08'],
        ],
        'note' => 'El indicador se mantiene dentro de meta. La variación responde a ajustes normales de cierre, revisión de pólizas.'
    ],
    [
        'name' => 'Conciliación bancaria y depuración de saldos',
        'code' => 'CONT_CONC_BANC',
        'description' => 'Mantenimiento de cuentas bancarias conciliadas al día.',
        'category' => 'CONTABILIDAD',
        'target' => 95.00,
        'values' => [
            1 => ['val' => 95, 'date' => '2026-05-26'],
            2 => ['val' => 96, 'date' => '2026-05-26'],
            3 => ['val' => 100, 'date' => '2026-05-26'],
            4 => ['val' => 76, 'date' => '2026-05-26'],
            5 => ['val' => 100, 'date' => '2026-06-01'],
            6 => ['val' => 96, 'date' => '2026-07-08'],
            7 => ['val' => 0, 'date' => '2026-07-08'],
        ],
        'note' => 'Se conserva un nivel adecuado de conciliación y depuración. El seguimiento debe mantenerse para evitar partidas'
    ],
    [
        'name' => 'Control de obligaciones y evidencias fiscales',
        'code' => 'CONT_CTRL_EVID',
        'description' => 'Manejo adecuado de evidencias para requerimientos.',
        'category' => 'CONTABILIDAD',
        'target' => 95.00,
        'values' => [
            1 => ['val' => 95, 'date' => '2026-05-26'],
            2 => ['val' => 94, 'date' => '2026-05-26'],
            3 => ['val' => 97, 'date' => '2026-05-26'],
            4 => ['val' => 96, 'date' => '2026-05-26'],
            5 => ['val' => 95, 'date' => '2026-06-01'],
            6 => ['val' => 96, 'date' => '2026-07-08'],
            7 => ['val' => 0, 'date' => '2026-07-08'],
        ],
        'note' => 'Se fortalece el control documental de obligaciones fiscales, resguardo de evidencias, papeles de trabajo, acuses, líneas'
    ],
    [
        'name' => 'Tablero ejecutivo de contabilidad',
        'code' => 'CONT_TABL_EJEC',
        'description' => 'Mantenimiento y actualización del tablero contable.',
        'category' => 'CONTABILIDAD',
        'target' => 95.00,
        'values' => [
            1 => ['val' => -1, 'date' => null],
            2 => ['val' => -1, 'date' => null],
            3 => ['val' => -1, 'date' => null],
            4 => ['val' => -1, 'date' => null],
            5 => ['val' => -1, 'date' => null],
            6 => ['val' => -1, 'date' => null],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'Detalle del avance...'
    ],
];

foreach ($kpis as $kpiData) {
    $db->table('kpis')->updateOrInsert(
        ['code' => $kpiData['code']],
        [
            'name' => $kpiData['name'],
            'description' => $kpiData['description'],
            'category' => $kpiData['category'],
            'target' => $kpiData['target'],
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    $kpiObj = $db->table('kpis')->where('code', $kpiData['code'])->first();

    foreach ($kpiData['values'] as $month => $vData) {
        $db->table('kpi_results')->updateOrInsert(
            [
                'kpi_id' => $kpiObj->id,
                'year' => 2026,
                'month' => $month
            ],
            [
                'value' => $vData['val'],
                'target_value' => $kpiData['target'],
                'period_date' => $vData['date'],
                'notes' => ($month == 6 || ($kpiData['code'] == 'CONT_TABL_EJEC' && $month == 7)) ? $kpiData['note'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}

echo "Successfully seeded Contabilidad KPIs data!\n";
