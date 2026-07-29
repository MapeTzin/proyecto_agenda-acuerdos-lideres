<?php
$base = 'C:\\inetpub\\wwwroot\\KPI_AREAS';

$ctrls = ['AreaController.php', 'KpiDashboardController.php', 'KpiController.php'];
foreach ($ctrls as $c) {
    echo "=== CONTROLLER: $c ===\n";
    $p = "$base/app/Http/Controllers/$c";
    if (file_exists($p)) {
        echo file_get_contents($p) . "\n";
    }
}
