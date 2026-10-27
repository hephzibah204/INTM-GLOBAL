<?php
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => $db_error ?: 'Database unavailable']);
    exit;
}

// Support JSON input, form POST, or GET
$input = [];
$raw_input = file_get_contents('php://input');
if (!empty($raw_input)) {
    $decoded = json_decode($raw_input, true);
    if (is_array($decoded)) {
        $input = $decoded;
    }
}
if (empty($input)) {
    $input = !empty($_POST) ? $_POST : $_GET;
}

// Authentication Check: Support session OR API key / Bearer / Basic auth
$is_authenticated = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if (!$is_authenticated) {
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    $api_pass = $input['admin_pass'] ?? $input['api_key'] ?? $input['password'] ?? '';
    
    if ($auth_header && preg_match('/Bearer\s+(.*)$/i', $auth_header, $m)) {
        $token = trim($m[1]);
        if (hash_equals($admin_pass, $token)) {
            $is_authenticated = true;
        }
    } elseif ($auth_header && preg_match('/Basic\s+(.*)$/i', $auth_header, $m)) {
        $decoded = base64_decode($m[1]);
        if ($decoded && str_contains($decoded, ':')) {
            [$u, $p] = explode(':', $decoded, 2);
            if (hash_equals($admin_user, $u) && hash_equals($admin_pass, $p)) {
                $is_authenticated = true;
            }
        }
    } elseif ($api_pass && hash_equals($admin_pass, (string)$api_pass)) {
        $is_authenticated = true;
    }
}

if (!$is_authenticated) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Please login to the admin dashboard or provide valid credentials.']);
    exit;
}

$action = $input['action'] ?? '';
if ($action === '' && !empty($input['name'])) {
    $action = 'add_credential';
}

$table_map = [
    'contact' => 'contact_submissions',
    'membership' => 'membership_submissions',
    'workshop' => 'workshop_submissions',
    'collaboration' => 'collaboration_submissions',
    'trainer' => 'trainer_submissions'
];

try {
    if ($action === 'status') {
        $table = $table_map[$input['type']] ?? null;
        if (!$table) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid submission type']);
            exit;
        }
        $stmt = $db->prepare("UPDATE $table SET status = ? WHERE id = ?");
        $stmt->execute([$input['status'], $input['id']]);
    } 
    elseif ($action === 'delete') {
        $table = $table_map[$input['type']] ?? null;
        if (!$table) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid submission type']);
            exit;
        }
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
    elseif ($action === 'set_prefix_defaults') {
        $cert = strtoupper(trim((string)($input['certificate_prefix'] ?? '')));
        $mem = strtoupper(trim((string)($input['membership_prefix'] ?? '')));

        if ($cert === '' || $mem === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Both Certificate and Membership prefixes are required.']);
            exit;
        }

        $stmt = $db->prepare("REPLACE INTO app_settings (key, value, updated_at) VALUES (?, ?, datetime('now'))");
        $stmt->execute(['prefix_certificate', $cert]);
        $stmt->execute(['prefix_membership', $mem]);

        echo json_encode([
            'success' => true,
            'prefix_defaults' => [
                'certificate' => $cert,
                'membership' => $mem
            ]
        ]);
        exit;
    }
    elseif ($action === 'add_credential') {
        $name = trim((string)($input['name'] ?? ''));
        $credential_id = strtoupper(trim((string)($input['credential_id'] ?? '')));
        $type_in = trim((string)($input['type'] ?? 'Certificate'));
        $status_in = trim((string)($input['status'] ?? 'Active'));

        $issued_date = trim((string)($input['issued_date'] ?? ''));
        if ($issued_date === '') {
            $issued_date = date('Y-m-d');
        }

        $qualification = trim((string)($input['qualification'] ?? ''));
        $grade = trim((string)($input['grade'] ?? ''));

        $type_key = strtolower($type_in);
        $type_map = [
            'certificate' => 'Certificate',
            'cert' => 'Certificate',
            'membership' => 'Membership',
            'member' => 'Membership'
        ];
        $type = $type_map[$type_key] ?? 'Certificate';

        if ($name === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Recipient Name is required.']);
            exit;
        }

        $key = $type === 'Membership' ? 'prefix_membership' : 'prefix_certificate';
        $stmt = $db->prepare("SELECT value FROM app_settings WHERE key = ?");
        $stmt->execute([$key]);
        $prefix = (string)($stmt->fetchColumn() ?: ($type === 'Membership' ? 'INTM-MEM' : 'INTM-CERT'));
        $prefix = strtoupper(trim($prefix));

        $year = null;
        $ts = strtotime($issued_date);
        if ($ts !== false) {
            $year = (int)date('Y', $ts);
        } elseif (preg_match('/^\d{4}/', $issued_date, $m)) {
            $year = (int)$m[0];
        }
        if (!$year) {
            $year = (int)date('Y');
        }

        $seq = null;

        if ($credential_id === '') {
            $db->exec('BEGIN IMMEDIATE');
            $stmt = $db->prepare("SELECT COALESCE(MAX(seq), 0) AS max_seq FROM valid_credentials WHERE prefix = ? COLLATE NOCASE AND issued_year = ?");
            $stmt->execute([$prefix, $year]);
            $max = (int)($stmt->fetchColumn() ?: 0);
            $seq = $max + 1;
            $credential_id = sprintf('%s-%04d-%04d', $prefix, $year, $seq);
        } else {
            // Check if credential_id already exists to prevent vague constraint crash
            $check = $db->prepare("SELECT id FROM valid_credentials WHERE credential_id = ? COLLATE NOCASE LIMIT 1");
            $check->execute([$credential_id]);
            if ($check->fetchColumn()) {
                http_response_code(400);
                echo json_encode(['error' => "Credential ID '{$credential_id}' is already registered in the registry."]);
                exit;
            }

            // Extract prefix/seq if ID follows standard pattern
            if (preg_match('/^(.*)-(\d{4})-(\d+)$/', $credential_id, $m)) {
                $prefix = $m[1];
                $year = (int)$m[2];
                $seq = (int)$m[3];
            }
        }

        $stmt = $db->prepare("INSERT INTO valid_credentials (name, credential_id, type, prefix, seq, issued_date, issued_year, qualification, grade, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $credential_id, $type, $prefix, $seq, $issued_date, $year, ($qualification ?: null), ($grade ?: null), ($status_in ?: 'Active')]);

        if ($db->inTransaction()) {
            $db->commit();
        }

        $check = $db->prepare("SELECT * FROM valid_credentials WHERE credential_id = ? COLLATE NOCASE LIMIT 1");
        $check->execute([$credential_id]);
        $row = $check->fetch();
        if (!$row) {
            http_response_code(500);
            echo json_encode(['error' => 'Credential save failed. Please try again.']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'credential_id' => $credential_id,
            'credential' => $row,
            'db_file' => basename($db_path)
        ]);
        exit;
    }
    elseif ($action === 'toggle_cred_status') {
        $stmt = $db->prepare("UPDATE valid_credentials SET status = ? WHERE id = ?");
        $stmt->execute([$input['status'], $input['id']]);
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
