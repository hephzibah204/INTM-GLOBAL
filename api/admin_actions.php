<?php
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => $db_error ?: 'Database unavailable']);
    exit;
}

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

$table_map = [
    'contact' => 'contact_submissions',
    'membership' => 'membership_submissions',
    'workshop' => 'workshop_submissions',
    'collaboration' => 'collaboration_submissions',
    'trainer' => 'trainer_submissions'
];

try {
    if ($action === 'status') {
        $table = $table_map[$input['type']];
        $stmt = $db->prepare("UPDATE $table SET status = ? WHERE id = ?");
        $stmt->execute([$input['status'], $input['id']]);
    } 
    elseif ($action === 'delete') {
        $table = $table_map[$input['type']];
        $stmt = $db->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$input['id']]);
    }
    elseif ($action === 'cms') {
        $stmt = $db->prepare("UPDATE site_content SET content = ?, updated_at = datetime('now') WHERE id = ?");
        foreach ($input['updates'] as $upd) {
            $stmt->execute([$upd['content'], $upd['id']]);
        }
    }
    elseif ($action === 'delete_cred') {
        $stmt = $db->prepare("DELETE FROM valid_credentials WHERE id = ?");
        $stmt->execute([$input['id']]);
    }
    elseif ($action === 'add_credential') {
        $name = trim((string)($input['name'] ?? ''));
        $credential_id = strtoupper(trim((string)($input['credential_id'] ?? '')));
        $type_in = trim((string)($input['type'] ?? ''));
        $status_in = trim((string)($input['status'] ?? 'Active'));

        $type_key = strtolower($type_in);
        $type_map = [
            'certificate' => 'Certificate',
            'cert' => 'Certificate',
            'membership' => 'Membership',
            'member' => 'Membership'
        ];
        $type = $type_map[$type_key] ?? $type_in;

        if ($name === '' || $credential_id === '' || $type === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Name, Credential ID and Type are required']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO valid_credentials (name, credential_id, type, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $credential_id, $type, $status_in ?: 'Active']);
    }
    elseif ($action === 'toggle_cred_status') {
        $stmt = $db->prepare("UPDATE valid_credentials SET status = ? WHERE id = ?");
        $stmt->execute([$input['status'], $input['id']]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
