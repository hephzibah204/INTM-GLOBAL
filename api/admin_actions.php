<?php
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

$table_map = [
    'contact' => 'contact_submissions',
    'membership' => 'membership_submissions',
    'workshop' => 'workshop_submissions'
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
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
