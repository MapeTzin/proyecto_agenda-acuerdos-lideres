<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = DB::connection('sistema_tickets');
$tables = $db->select('SHOW TABLES');
echo "=== TABLES IN sistema_tickets ===\n";
print_r($tables);
