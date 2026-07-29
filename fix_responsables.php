<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updates = [
    'GEORGINA WENDTI GONZÁLEZ OROZCO' => 'Georgina Wendti',
    'Anabel Velasco Flores' => 'Anabel',
    'Alejandro Mendoza Chavarría' => 'Alejandro Mendoza',
    'GEMA IVON SERVIN CRUZ' => 'Gema Ivonn',
    'VICTOR MANUEL AROCHI DIAZ' => 'Victor Arochi',
    'MARCO ANTONIO MERCADO' => 'MARCO MERCADO',
    'MARLON LOPEZ ARELLANO' => 'Marlon',
    'ABIGAIL CHÁVEZ RAMÍREZ' => 'Abigail',
    'OMAR AGUILAR VAZQUEZ' => 'Omar',
    'CESAR OSBALDO NOGUEIRA ESPINOZA' => 'Cesar Osbaldo'
];

$count = 0;
foreach($updates as $old => $new) {
    $affected = \Illuminate\Support\Facades\DB::table('acuerdos')
        ->where('responsable', $old)
        ->update(['responsable' => $new]);
    $count += $affected;
}

echo "Updated $count agreements.";
