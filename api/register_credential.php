<?php
// Dedicated API endpoint for registering a certificate or credential
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'POST';

// Forward request to admin_actions.php with action='add_credential'
$raw_input = file_get_contents('php://input');
$data = [];
if (!empty($raw_input)) {
    $decoded = json_decode($raw_input, true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}
if (empty($data)) {
    $data = !empty($_POST) ? $_POST : $_GET;
}

$data['action'] = 'add_credential';

// Re-encode and pass through
require_once __DIR__ . '/admin_actions.php';
