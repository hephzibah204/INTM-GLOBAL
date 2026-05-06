<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$sqlite_available = in_array('sqlite', PDO::getAvailableDrivers(), true);

$db_file_exists = is_file($db_path);
$db_is_writable = $db_file_exists ? is_writable($db_path) : is_writable(dirname($db_path));

$out = [
    'ok' => (bool)$db,
    'sqlite_available' => $sqlite_available,
    'db_file_exists' => $db_file_exists,
    'db_is_writable' => $db_is_writable,
    'db_file' => basename($db_path),
];

if (!$db) {
    http_response_code(500);
    $out['error'] = $db_error ?: 'Database unavailable';
    echo json_encode($out);
    exit;
}

try {
    $out['counts'] = [
        'valid_credentials_total' => (int)$db->query("SELECT COUNT(*) FROM valid_credentials")->fetchColumn(),
        'valid_credentials_active' => (int)$db->query("SELECT COUNT(*) FROM valid_credentials WHERE status = 'Active'")->fetchColumn(),
    ];
} catch (Throwable $e) {
    $out['counts_error'] = 'Counts unavailable';
}

echo json_encode($out);

