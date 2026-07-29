<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['events', 'event_user'];
foreach($tables as $table) {
    try {
        $createTable = DB::select("SHOW CREATE TABLE $table")[0]->{'Create Table'};
        echo "$table schema:\n$createTable\n\n";
    } catch(Exception $e) {
        echo "Error on $table: " . $e->getMessage() . "\n";
    }
}
