<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// We need to connect to sqlite explicitly since the default might have changed
$sqliteUsers = DB::connection('sqlite')->table('users')->get();

echo "ID,Email,Name\n";
foreach ($sqliteUsers as $user) {
    echo "{$user->id},{$user->email},{$user->name}\n";
}
