<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!$db) {
    http_response_code(500);
    echo json_encode([
        'valid' => false,
        'message' => 'Verification service is temporarily unavailable.',
        'error' => $db_error ?: 'Database unavailable'
    ]);
    exit;
}

// Support JSON body, POST form-encoded, and GET query parameters
$input = [];
$raw_input = file_get_contents('php://input');
if (!empty($raw_input)) {
    $decoded = json_decode($raw_input, true);
    if (is_array($decoded)) {
        $input = $decoded;
    }
}

$cred_id = $input['credential_id'] 
    ?? $input['id'] 
    ?? $input['cert_id'] 
    ?? $input['cert'] 
    ?? $input['code']
    ?? $_POST['credential_id'] 
    ?? $_POST['id'] 
    ?? $_POST['cert_id'] 
    ?? $_POST['cert'] 
    ?? $_POST['code']
    ?? $_GET['credential_id'] 
    ?? $_GET['id'] 
    ?? $_GET['cert_id'] 
    ?? $_GET['cert'] 
    ?? $_GET['code']
    ?? '';

$type = $input['type'] ?? $_POST['type'] ?? $_GET['type'] ?? '';

$raw_id = trim((string)$cred_id);
if ($raw_id === '') {
    http_response_code(400);
    echo json_encode([
        'valid' => false,
        'message' => 'Please enter a Certificate ID or Member ID.'
    ]);
    exit;
}

// Clean and normalize ID
$clean_id = strtoupper(preg_replace('/\s+/', ' ', $raw_id));
$type = trim((string)$type);

if ($type !== '') {
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
}

// Build list of candidate variations for flexible lookup
$candidates = [];
$candidates[] = $clean_id;

// Variation 1: normalize spaces around hyphens (e.g., "INTM - CERT - 2026 - 0001" -> "INTM-CERT-2026-0001")
$normalized_hyphens = preg_replace('/\s*-\s*/', '-', $clean_id);
if (!in_array($normalized_hyphens, $candidates, true)) {
    $candidates[] = $normalized_hyphens;
}

// Variation 2: convert 1-3 digit sequences to 4-digit padding (e.g., "INTM-CERT-2026-001" -> "INTM-CERT-2026-0001")
if (preg_match('/^(.*)-(\d{4})-(\d{1,3})$/', $normalized_hyphens, $m)) {
    $padded = sprintf('%s-%s-%04d', $m[1], $m[2], (int)$m[3]);
    if (!in_array($padded, $candidates, true)) {
        $candidates[] = $padded;
    }
}

// Variation 3: convert 4-digit sequence to 3-digit sequence if stored that way
if (preg_match('/^(.*)-(\d{4})-(\d{4})$/', $normalized_hyphens, $m)) {
    $unpadded = sprintf('%s-%s-%03d', $m[1], $m[2], (int)$m[3]);
    if (!in_array($unpadded, $candidates, true)) {
        $candidates[] = $unpadded;
    }
}

$row = null;

try {
    // 1. Try matching with explicit type if specified
    if ($type !== '') {
        $stmt = $db->prepare("SELECT * FROM valid_credentials WHERE credential_id = ? COLLATE NOCASE AND type = ? COLLATE NOCASE LIMIT 1");
        foreach ($candidates as $cand) {
            $stmt->execute([$cand, $type]);
            $row = $stmt->fetch();
            if ($row) break;
        }
    }

    // 2. Try matching candidate IDs regardless of type
    if (!$row) {
        $stmt = $db->prepare("SELECT * FROM valid_credentials WHERE credential_id = ? COLLATE NOCASE LIMIT 1");
        foreach ($candidates as $cand) {
            $stmt->execute([$cand]);
            $row = $stmt->fetch();
            if ($row) break;
        }
    }

    // 3. Fallback: match without dashes and spaces (e.g., "INTMCERT20260001")
    if (!$row) {
        $stripped = preg_replace('/[^A-Z0-9]/', '', $clean_id);
        if (strlen($stripped) >= 6) {
            $stmt = $db->prepare("SELECT * FROM valid_credentials WHERE REPLACE(REPLACE(credential_id, '-', ''), ' ', '') = ? COLLATE NOCASE LIMIT 1");
            $stmt->execute([$stripped]);
            $row = $stmt->fetch();
        }
    }

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

        $status = $row['status'] ?: 'Active';

        if ($status === 'Active') {
            $msg = 'Credential verified.';
            if ($type !== '' && strtolower($type) !== strtolower((string)$row['type'])) {
                $msg = 'Credential verified as ' . $row['type'] . '.';
            }
            echo json_encode([
                'valid' => true,
                'message' => $msg,
                'credential' => $payload
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'message' => 'This credential has been marked as ' . $status . '.',
                'credential' => $payload
            ]);
        }
    } else {
        echo json_encode([
            'valid' => false,
            'message' => 'Credential not found in our records. Please verify the ID number on your certificate and try again.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'valid' => false,
        'message' => 'Verification system error. Please try again later.'
    ]);
}
