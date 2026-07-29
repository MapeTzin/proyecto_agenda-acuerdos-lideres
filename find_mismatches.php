<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$responsables = \Illuminate\Support\Facades\DB::table('acuerdos')->pluck('responsable')->unique();
$users = \App\Models\User::pluck('name')->map(function($name) {
    return strtolower(trim($name));
});

$mismatches = [];
foreach($responsables as $r) {
    $r_lower = strtolower(trim($r));
    if(!$users->contains($r_lower)) {
        $mismatches[] = $r;
    }
}
echo json_encode($mismatches);
