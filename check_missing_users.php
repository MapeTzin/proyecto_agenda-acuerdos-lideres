<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach ([2, 10, 17] as $id) {
    $events = DB::connection('sqlite')->table('events')->where('created_by', $id)->count();
    $roles = DB::connection('sqlite')->table('model_has_roles')->where('model_id', $id)->count();
    $bitacoras = DB::connection('sqlite')->table('bitacoras')->where('user_id', $id)->count();
    $comentarios = DB::connection('sqlite')->table('comentarios')->where('user_id', $id)->count();
    echo "SQLite ID $id: $events events, $roles roles, $bitacoras bitacoras, $comentarios comentarios\n";
}
