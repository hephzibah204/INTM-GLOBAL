<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$cred_id = $input['credential_id'] ?? '';
$type = $input['type'] ?? '';

if (!$cred_id || !$type) {
    echo json_encode(['valid' => false, 'message' => 'Missing ID or type']);
    exit;
}

try {
    $stmt = $db->prepare("SELECT * FROM valid_credentials WHERE credential_id = ? AND type = ?");
    $stmt->execute([$cred_id, $type]);
    $row = $stmt->fetch();

    if ($row) {
        if ($row['status'] === 'Active') {
            echo json_encode(['valid' => true, 'message' => "Credential verified. Issued to " . $row['name'] . "."]);
        } else {
            echo json_encode(['valid' => false, 'message' => "This credential has been marked as " . $row['status'] . "."]);
        }
    } else {
        echo json_encode(['valid' => false, 'message' => "Credential not found in our records."]);
    }
} catch (PDOException $e) {
    echo json_encode(['valid' => false, 'message' => 'System error']);
}
?>
