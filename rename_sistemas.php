<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Rename in areas table
$area = App\Models\Area::where('name', 'SISTEMAS')->first();
if ($area) {
    $area->name = 'SISTEMAS Y TI';
    $area->save();
    echo "Area model updated\n";
}

// Rename in acuerdos table
App\Models\Acuerdo::where('area', 'SISTEMAS')->update(['area' => 'SISTEMAS Y TI']);
echo "Acuerdos updated\n";

// Rename in users table if applicable
App\Models\User::where('area', 'SISTEMAS')->update(['area' => 'SISTEMAS Y TI']);
echo "Users updated\n";

echo "Done\n";
