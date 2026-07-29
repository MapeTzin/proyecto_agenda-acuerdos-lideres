<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = DB::connection('sqlite')->table('acuerdos')->where('responsable', 'like', '%REYES%')->count();
echo "Acuerdos with 'REYES' in responsable: $count\n";

if ($count > 0) {
    $acuerdos = DB::connection('sqlite')->table('acuerdos')->where('responsable', 'like', '%REYES%')->get(['id', 'responsable']);
    foreach ($acuerdos as $a) {
        echo "  ID {$a->id}: {$a->responsable}\n";
    }
}
