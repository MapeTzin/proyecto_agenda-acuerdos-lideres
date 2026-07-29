<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$areas = \App\Models\Area::all();
foreach ($areas as $area) {
    echo "ID: {$area->id} | Name: {$area->name} | Color: {$area->color}\n";
}
