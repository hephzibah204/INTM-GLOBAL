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
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$scheme = $_POST['scheme'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$location = $_POST['location'] ?? '';
$organisation = $_POST['organisation'] ?? '';
$qualifications = $_POST['qualifications'] ?? '';
$experience_years = $_POST['experience_years'] ?? '';
$message = $_POST['message'] ?? '';

if (!$scheme || !$name || !$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Scheme, Name and Email are required']);
    exit;
}

$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$safe_filename = function (string $prefix, string $original) {
    $base = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
    return $prefix . '_' . date('Ymd_His') . '_' . $base;
};

$save_upload = function (string $field, array $allowed_ext, string $prefix) use ($upload_dir, $safe_filename) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext, true)) return null;
    $filename = $safe_filename($prefix, $_FILES[$field]['name']);
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $upload_dir . $filename)) return null;
    return $filename;
};

$cv_path = $save_upload('cv', ['pdf', 'doc', 'docx'], 'trainer_cv');
$proof_path = $save_upload('proof', ['pdf', 'jpg', 'jpeg', 'png'], 'trainer_proof');

try {
    $stmt = $db->prepare("INSERT INTO trainer_submissions (scheme, name, email, phone, location, organisation, qualifications, experience_years, message, cv_path, proof_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$scheme, $name, $email, $phone, $location, $organisation, $qualifications, $experience_years, $message, $cv_path, $proof_path]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
