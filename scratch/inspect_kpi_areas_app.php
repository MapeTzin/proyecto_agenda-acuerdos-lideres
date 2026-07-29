<?php
$base = 'C:\\inetpub\\wwwroot\\KPI_AREAS';

echo "=== ROUTES web.php ===\n";
if (file_exists("$base/routes/web.php")) {
    echo file_get_contents("$base/routes/web.php") . "\n";
}

echo "=== LIVEWIRE COMPONENTS ===\n";
$lwDir = "$base/app/Livewire";
if (is_dir($lwDir)) {
    foreach (scandir($lwDir) as $f) {
        if (str_ends_with($f, '.php')) {
            echo "--- $f ---\n";
            echo file_get_contents("$lwDir/$f") . "\n";
        }
    }
}
