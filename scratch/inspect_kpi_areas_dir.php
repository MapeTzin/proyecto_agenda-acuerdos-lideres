<?php
$dir = 'C:\\inetpub\\wwwroot\\KPI_AREAS';
if (!is_dir($dir)) {
    echo "Directory does not exist\n";
    exit;
}

$files = scandir($dir);
echo "Files in KPI_AREAS:\n";
foreach ($files as $f) {
    if ($f !== '.' && $f !== '..') {
        echo " - " . $f . (is_dir("$dir/$f") ? " [DIR]" : "") . "\n";
    }
}
