<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$acuerdos = DB::table('acuerdos')->where('responsable', 'LIKE', '%gema%')->select('id', 'area', 'responsable', 'estatus')->get();
echo "Gema's acuerdos by name:\n" . json_encode($acuerdos, JSON_PRETTY_PRINT) . "\n";

$acuerdosDireccion = DB::table('acuerdos')->where('area', 'LIKE', '%direc%')->select('id', 'area', 'responsable', 'estatus')->get();
echo "Acuerdos in Direccion area:\n" . json_encode($acuerdosDireccion, JSON_PRETTY_PRINT) . "\n";

