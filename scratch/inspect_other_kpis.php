<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== INSPECTING MYSQL areasKpi ===\n";
try {
    config(['database.connections.areasKpi_mysql' => [
        'driver' => 'mysql',
        'host' => '192.168.100.12',
        'port' => '3306',
        'database' => 'areasKpi',
        'username' => 'usuariomt',
        'password' => 'mape157527*',
        'charset' => 'utf8mb4',
    ]]);

    $mysqlTables = DB::connection('areasKpi_mysql')->select('SHOW TABLES');
    echo "MySQL tables in areasKpi:\n";
    print_r($mysqlTables);

    $areas = DB::connection('areasKpi_mysql')->table('areas')->get();
    echo "MySQL Areas count: " . count($areas) . "\n";
    foreach ($areas as $a) {
        echo "Area: " . json_encode($a) . "\n";
    }

    $kpis = DB::connection('areasKpi_mysql')->table('kpis')->get();
    echo "MySQL KPIs count: " . count($kpis) . "\n";
    foreach ($kpis as $k) {
        echo "KPI: " . json_encode($k) . "\n";
    }
} catch (\Exception $e) {
    echo "MySQL areasKpi error: " . $e->getMessage() . "\n";
}

echo "\n=== INSPECTING SQLITE database.sqlite ===\n";
try {
    config(['database.connections.areasKpi_sqlite' => [
        'driver' => 'sqlite',
        'database' => 'C:/inetpub/wwwroot/KPI_AREAS/database/database.sqlite',
    ]]);

    $sqliteTables = DB::connection('areasKpi_sqlite')->select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "SQLite tables:\n";
    print_r($sqliteTables);

    $sAreas = DB::connection('areasKpi_sqlite')->table('areas')->get();
    echo "SQLite Areas count: " . count($sAreas) . "\n";
    foreach ($sAreas as $a) {
        echo "Area: " . json_encode($a) . "\n";
    }

    $sKpis = DB::connection('areasKpi_sqlite')->table('kpis')->get();
    echo "SQLite KPIs count: " . count($sKpis) . "\n";
    foreach ($sKpis as $k) {
        echo "KPI: " . json_encode($k) . "\n";
    }
} catch (\Exception $e) {
    echo "SQLite error: " . $e->getMessage() . "\n";
}
