<?php
$host = '192.168.100.3';
$user = 'admindb';
$pass = 'Mape157527*';
$dbName = 'agenda_acuerdos';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `$dbName` created successfully.\n";
    
    // Grant privileges to usuario_siamt
    $pdo->exec("GRANT ALL PRIVILEGES ON `$dbName`.* TO 'usuario_siamt'@'%'");
    $pdo->exec("FLUSH PRIVILEGES");
    echo "Privileges granted to `usuario_siamt`.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
