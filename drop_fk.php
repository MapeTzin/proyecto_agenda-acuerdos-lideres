<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE events DROP FOREIGN KEY events_created_by_foreign');
    echo "Dropped events_created_by_foreign successfully.\n";
} catch(Exception $e) {
    echo "Error dropping events_created_by_foreign: " . $e->getMessage() . "\n";
}

try {
    DB::statement('ALTER TABLE event_user DROP FOREIGN KEY event_user_user_id_foreign');
    echo "Dropped event_user_user_id_foreign successfully.\n";
} catch(Exception $e) {
    echo "Error dropping event_user_user_id_foreign: " . $e->getMessage() . "\n";
}

echo "Done.\n";
