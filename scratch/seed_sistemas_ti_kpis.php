<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');

// Make sure category is SISTEMAS Y TI
$db->table('kpis')->where('category', 'SISTEMAS Y TI')->delete();

$sistemasKpis = [
    [
        'name' => 'Índice de Cumplimiento en Mantenimiento Preventivo',
        'code' => 'STI_MANT_PREV',
        'description' => 'Porcentaje de mantenimientos realizados vs programados',
        'target' => 95.00,
        'target_yellow' => 90.00,
        'values' => [
            1 => ['val' => 95, 'date' => '2026-05-22'],
            2 => ['val' => 0, 'date' => '2026-05-22'],
            3 => ['val' => 0, 'date' => '2026-05-22'],
            4 => ['val' => 0, 'date' => '2026-05-22'],
            5 => ['val' => 95, 'date' => '2026-05-22'],
            6 => ['val' => -1, 'date' => null],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'el mantenimiento se realiza trimestralmente por eso solo se quedan en rojo los meses que no se realiza ninguna actividad de este KPI'
    ],
    [
        'name' => 'Tiempo Promedio de Atención a Servicios Correctivos (TAT)',
        'code' => 'STI_TAT_CORR',
        'description' => 'Eficacia en el tiempo de respuesta a servicios correctivos',
        'target' => 95.00,
        'target_yellow' => 90.00,
        'values' => [
            1 => ['val' => 95, 'date' => '2026-05-22'],
            2 => ['val' => 95, 'date' => '2026-05-22'],
            3 => ['val' => 95, 'date' => '2026-05-22'],
            4 => ['val' => 95, 'date' => '2026-05-22'],
            5 => ['val' => 95, 'date' => '2026-05-22'],
            6 => ['val' => 88, 'date' => '2026-07-06'],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'Este KPI se mide en relación a la atención a tickets abiertos por los usuarios'
    ],
    [
        'name' => 'Nivel de Satisfacción de Usuarios Internos',
        'code' => 'STI_SATI_USER',
        'description' => 'Calificación otorgada por usuarios internos mediante encuestas',
        'target' => 90.00,
        'target_yellow' => 85.00,
        'values' => [
            1 => ['val' => 50, 'date' => '2026-05-22'],
            2 => ['val' => 50, 'date' => '2026-05-22'],
            3 => ['val' => 50, 'date' => '2026-05-22'],
            4 => ['val' => 50, 'date' => '2026-05-22'],
            5 => ['val' => 50, 'date' => '2026-05-22'],
            6 => ['val' => 80.5, 'date' => '2026-07-06'],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'No se ha realizado la encuesta en la semana del 1ra al 5 de junio se envía encuestas de satisfacción'
    ],
    [
        'name' => 'Avance en Proyectos de Innovación Tecnológica',
        'code' => 'STI_PROY_INNO',
        'description' => 'Cumplimiento del roadmap de innovación tecnológica',
        'target' => 95.00,
        'target_yellow' => 90.00,
        'values' => [
            1 => ['val' => 90, 'date' => '2026-06-01'],
            2 => ['val' => 90, 'date' => '2026-05-22'],
            3 => ['val' => 90, 'date' => '2026-05-22'],
            4 => ['val' => 90, 'date' => '2026-05-22'],
            5 => ['val' => 93, 'date' => '2026-06-01'],
            6 => ['val' => 90, 'date' => '2026-07-06'],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'Detalle del avance...'
    ],
    [
        'name' => 'Disponibilidad de Servicios Tecnológicos Críticos',
        'code' => 'STI_DISP_CRIT',
        'description' => 'Porcentaje de tiempo en que los servicios tecnológicos críticos están disponibles y operativos.',
        'target' => 95.00,
        'target_yellow' => 90.00,
        'values' => [
            1 => ['val' => 90, 'date' => '2026-05-22'],
            2 => ['val' => 95, 'date' => '2026-05-22'],
            3 => ['val' => 90, 'date' => '2026-05-22'],
            4 => ['val' => 95, 'date' => '2026-05-22'],
            5 => ['val' => 95, 'date' => '2026-05-22'],
            6 => ['val' => 95, 'date' => '2026-07-06'],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'Detalle del avance...'
    ],
    [
        'name' => 'Cumplimiento de RoadMap de Innovación',
        'code' => 'STI_ROAD_INNO',
        'description' => 'Porcentaje de hitos del RoadMap de innovación tecnológica completadas en tiempo y forma.',
        'target' => 95.00,
        'target_yellow' => 90.00,
        'values' => [
            1 => ['val' => 85, 'date' => '2026-05-22'],
            2 => ['val' => 85, 'date' => '2026-05-22'],
            3 => ['val' => 90, 'date' => '2026-05-22'],
            4 => ['val' => 90, 'date' => '2026-05-22'],
            5 => ['val' => 90, 'date' => '2026-05-22'],
            6 => ['val' => 85, 'date' => '2026-07-06'],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'Detalle del avance...'
    ],
    [
        'name' => 'Ahorros Generados por Tecnología',
        'code' => 'STI_AHOR_TECN',
        'description' => 'Porcentaje de cumplimiento de la meta de ahorros generados mediante iniciativas tecnológicas.',
        'target' => 95.00,
        'target_yellow' => 90.00,
        'values' => [
            1 => ['val' => 50, 'date' => '2026-05-22'],
            2 => ['val' => 50, 'date' => '2026-05-22'],
            3 => ['val' => 50, 'date' => '2026-05-22'],
            4 => ['val' => 50, 'date' => '2026-05-22'],
            5 => ['val' => 50, 'date' => '2026-05-22'],
            6 => ['val' => 75.3, 'date' => '2026-07-06'],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'Detalle del avance...'
    ],
    [
        'name' => 'Ventas en Línea',
        'code' => 'STI_VENT_LINE',
        'description' => 'Porcentaje de cumplimiento de la meta de ventas realizadas a través de canales digitales.',
        'target' => 95.00,
        'target_yellow' => 90.00,
        'values' => [
            1 => ['val' => 50, 'date' => '2026-05-22'],
            2 => ['val' => 50, 'date' => '2026-05-22'],
            3 => ['val' => 50, 'date' => '2026-05-22'],
            4 => ['val' => 65, 'date' => '2026-05-22'],
            5 => ['val' => 65, 'date' => '2026-06-01'],
            6 => ['val' => 0, 'date' => '2026-07-06'],
            7 => ['val' => -1, 'date' => null],
        ],
        'note' => 'Detalle del avance...'
    ],
];

foreach ($sistemasKpis as $kpiData) {
    $db->table('kpis')->updateOrInsert(
        ['code' => $kpiData['code']],
        [
            'name' => $kpiData['name'],
            'description' => $kpiData['description'],
            'category' => 'SISTEMAS Y TI',
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
                'notes' => $vData['val'] != -1 ? $kpiData['note'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}

echo "SISTEMAS Y TI KPIs seeded successfully!\n";
