<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$tier = $_POST['tier'] ?? '';
$role = $_POST['role'] ?? '';
$qualifications = $_POST['qualifications'] ?? '';

if (!$name || !$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Name and Email are required']);
    exit;
}

// Handle File Upload
$cv_path = null;
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx'];

    if (in_array($ext, $allowed)) {
        $filename = 'cv_' . date('Ymd_His') . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['cv']['name']);
        if (move_uploaded_file($_FILES['cv']['tmp_name'], $upload_dir . $filename)) {
            $cv_path = $filename;
        }
    }
}

try {
    $stmt = $db->prepare("INSERT INTO membership_submissions (name, email, phone, tier, role, qualifications, cv_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $tier, $role, $qualifications, $cv_path]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
