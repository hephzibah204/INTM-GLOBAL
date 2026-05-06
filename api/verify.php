<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!$db) {
    http_response_code(500);
    echo json_encode([
        'valid' => false,
        'message' => 'Verification service is temporarily unavailable.',
        'error' => $db_error ?: 'Database unavailable'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cred_id = ($input['credential_id'] ?? ($_POST['credential_id'] ?? ($_GET['credential_id'] ?? '')));
$type = ($input['type'] ?? ($_POST['type'] ?? ($_GET['type'] ?? '')));

$cred_id = strtoupper(trim((string)$cred_id));
$type = trim((string)$type);

$type_key = strtolower($type);
$type_map = [
    'certificate' => 'Certificate',
    'cert' => 'Certificate',
    'membership' => 'Membership',
    'member' => 'Membership'
];

if (isset($type_map[$type_key])) {
    $type = $type_map[$type_key];
}

if (!$cred_id || !$type) {
    http_response_code(400);
    echo json_encode(['valid' => false, 'message' => 'Missing ID or type']);
    exit;
}

try {
    $stmt = $db->prepare("SELECT * FROM valid_credentials WHERE credential_id = ? COLLATE NOCASE AND type = ? COLLATE NOCASE");
    $stmt->execute([$cred_id, $type]);
    $row = $stmt->fetch();

    if ($row) {
        $payload = [
            'name' => $row['name'],
            'credential_id' => $row['credential_id'],
            'type' => $row['type'],
            'status' => $row['status'],
            'issued_date' => $row['issued_date'] ?? null,
            'qualification' => $row['qualification'] ?? null,
            'grade' => $row['grade'] ?? null,
        ];

        if ($row['status'] === 'Active') {
            echo json_encode([
                'valid' => true,
                'message' => 'Credential verified.',
                'credential' => $payload
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'message' => 'This credential has been marked as ' . $row['status'] . '.',
                'credential' => $payload
            ]);
        }
    } else {
        echo json_encode(['valid' => false, 'message' => 'Credential not found in our records.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['valid' => false, 'message' => 'System error']);
}
?>
