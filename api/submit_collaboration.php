<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => $db_error ?: 'Database unavailable']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$organisation = $input['organisation'] ?? '';
$contact_name = $input['contact_name'] ?? '';
$email = $input['email'] ?? '';
$phone = $input['phone'] ?? '';
$collab_type = $input['collab_type'] ?? '';
$message = $input['message'] ?? '';

if (!$organisation || !$email || !$message) {
    http_response_code(400);
    echo json_encode(['error' => 'Required fields missing']);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO collaboration_submissions (organisation, contact_name, email, phone, collab_type, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$organisation, $contact_name, $email, $phone, $collab_type, $message]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
