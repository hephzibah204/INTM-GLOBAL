<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$name = $input['name'] ?? '';
$email = $input['email'] ?? '';
$phone = $input['phone'] ?? '';
$designation = $input['designation'] ?? '';
$workshop = $input['workshop'] ?? '';
$message = $input['message'] ?? '';

if (!$name || !$email || !$workshop) {
    http_response_code(400);
    echo json_encode(['error' => 'Required fields missing']);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO workshop_submissions (name, email, phone, designation, workshop, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $designation, $workshop, $message]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
