<?php
$db = new SQLite3('database/database_backup_20260512.sqlite');
$res = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
while($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $table = $row['name'];
    echo "Table: $table\n";
    $info = $db->query("PRAGMA table_info($table)");
    while($i = $info->fetchArray(SQLITE3_ASSOC)) {
        echo "  - " . $i['name'] . "\n";
    }
}
