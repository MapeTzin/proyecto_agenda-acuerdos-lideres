<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test Admin User
$adminUser = \App\Models\User::where('email', 'soporte@mapetzin.com')->first();
auth()->login($adminUser);
$compAdmin = new \App\Livewire\KpisAreas();
$compAdmin->mount();
$dataAdmin = $compAdmin->render()->getData();

echo "=== ADMIN USER (soporte@mapetzin.com) ===\n";
echo "isAdminUser: " . ($dataAdmin['isAdminUser'] ? 'TRUE' : 'FALSE') . "\n";
echo "selectedArea: '" . $compAdmin->selectedArea . "' (Default: Corporate Monitor)\n\n";

// Test Regular Area User (e.g. Ventas)
$ventasUser = \App\Models\User::where('email', 'c.ortiz@mapetzin.com')->first(); // Carlos Vasquez (Ventas)
auth()->login($ventasUser);
$compVentas = new \App\Livewire\KpisAreas();
$compVentas->mount();
$dataVentas = $compVentas->render()->getData();

echo "=== REGULAR USER (c.ortiz@mapetzin.com) ===\n";
echo "isAdminUser: " . ($dataVentas['isAdminUser'] ? 'TRUE' : 'FALSE') . "\n";
echo "userAreaName: '" . $dataVentas['userAreaName'] . "'\n";
echo "selectedArea: '" . $compVentas->selectedArea . "' (Default: User Area Workspace)\n";
