<?php

declare(strict_types=1);

$root = realpath($argv[1] ?? '/var/www/apps/agenda');
if (!$root || !str_starts_with($root, '/var/www/agenda-releases/')) {
    throw new RuntimeException('Unexpected agenda release root: '.($argv[1] ?? 'null'));
}

require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$errors = [];
$manifestPath = $root.'/.release/source-manifest.json';
if (!is_file($manifestPath)) {
    throw new RuntimeException('Missing .release/source-manifest.json');
}

$manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
foreach ($manifest['files'] as $name => $digest) {
    $filePath = $root.'/'.$name;
    if (!is_file($filePath) || !hash_equals($digest, hash_file('sha256', $filePath))) {
        $errors[] = 'Source manifest mismatch: '.$name;
    }
}

if (isset($argv[2]) && ($manifest['source_commit'] ?? '') !== $argv[2]) {
    $errors[] = 'Unexpected source commit';
}
if (!app()->isProduction() || config('app.debug')) {
    $errors[] = 'Production/debug mismatch';
}
if (!config('siamt-sso.enabled')) {
    $errors[] = 'SIAMT SSO is not enabled in runtime configuration';
}
if (realpath($root.'/.env') !== '/etc/agenda/runtime.env') {
    $errors[] = 'Unexpected runtime configuration symlink';
}
if (realpath($root.'/storage') !== '/var/lib/agenda/storage') {
    $errors[] = 'Unexpected storage symlink';
}
if (!is_file($root.'/public/build/manifest.json')) {
    $errors[] = 'Missing compiled Vite manifest';
}

$dbMain = Illuminate\Support\Facades\DB::connection('mysql');
$identity = $dbMain->selectOne('SELECT CURRENT_USER() AS account, DATABASE() AS db');
if (($identity->account ?? '') !== 'user_agenda01@192.168.100.14' || ($identity->db ?? '') !== 'agenda_acuerdos') {
    $errors[] = 'Unexpected mysql runtime identity: '.($identity->account ?? 'unknown').'/'.($identity->db ?? 'unknown');
}

$tlsMain = $dbMain->select("SHOW SESSION STATUS LIKE 'Ssl_cipher'")[0]->Value ?? '';
if ($tlsMain === '') {
    $errors[] = 'MySQL TLS unavailable on mysql';
}

$dbAuth = Illuminate\Support\Facades\DB::connection('mysql_auth');
$tlsAuth = $dbAuth->select("SHOW SESSION STATUS LIKE 'Ssl_cipher'")[0]->Value ?? '';
if ($tlsAuth === '') {
    $errors[] = 'MySQL TLS unavailable on mysql_auth';
}

foreach (['ac_siamt_subjects', 'ac_siamt_sessions', 'ac_siamt_events', 'acuerdos', 'kpis'] as $requiredTable) {
    if (!Illuminate\Support\Facades\Schema::connection('mysql')->hasTable($requiredTable)) {
        $errors[] = 'Missing required table in agenda_acuerdos: '.$requiredTable;
    }
}

echo json_encode([
    'release' => basename($root),
    'source_commit' => $manifest['source_commit'],
    'manifest_files_verified' => count($manifest['files']),
    'runtime_account' => $identity->account ?? null,
    'database' => $identity->db ?? null,
    'kpi_connection' => config('database.kpi_connection'),
    'kpi_unify_ti_with_tickets' => (bool) config('database.kpi_unify_ti_with_tickets'),
    'tls_cipher_main' => $tlsMain,
    'tls_cipher_auth' => $tlsAuth,
    'runtime_env_sha256' => hash_file('sha256', '/etc/agenda/runtime.env'),
    'errors' => $errors,
], JSON_PRETTY_PRINT).PHP_EOL;

exit($errors ? 1 : 0);
