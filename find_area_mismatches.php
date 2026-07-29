<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$acuerdos_areas = \Illuminate\Support\Facades\DB::table('acuerdos')->select('area')->distinct()->pluck('area')->map(function($a) { return strtolower(trim($a)); })->toArray();
$users_areas = \Illuminate\Support\Facades\DB::table('users')->select('area')->distinct()->pluck('area');

$mismatches = [];
foreach($users_areas as $ua) {
    if ($ua) {
        $ua_lower = strtolower(trim($ua));
        if(!in_array($ua_lower, $acuerdos_areas)) {
            $mismatches[] = $ua;
        }
    }
}
echo json_encode($mismatches);
