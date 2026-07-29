<?php
$db = new SQLite3('database/database_backup_20260512.sqlite');
$res = $db->query('PRAGMA table_info(users)');
while($row = $res->fetchArray(SQLITE3_ASSOC)) {
    echo $row['name'] . "\n";
}
echo "--- DATA ---\n";
$res = $db->query('SELECT * FROM users');
while($row = $res->fetchArray(SQLITE3_ASSOC)) {
    print_r($row);
}
