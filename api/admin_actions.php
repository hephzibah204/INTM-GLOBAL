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
        $type_in = trim((string)($input['type'] ?? ''));
        $status_in = trim((string)($input['status'] ?? 'Active'));

        $issued_date = trim((string)($input['issued_date'] ?? ''));
        $prefix_in = '';
        $qualification = trim((string)($input['qualification'] ?? ''));
        $grade = trim((string)($input['grade'] ?? ''));

        $type_key = strtolower($type_in);
        $type_map = [
            'certificate' => 'Certificate',
            'cert' => 'Certificate',
            'membership' => 'Membership',
            'member' => 'Membership'
        ];
        $type = $type_map[$type_key] ?? $type_in;

        if ($name === '' || $type === '' || $issued_date === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Name, Date Issued and Type are required']);
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
            http_response_code(400);
            echo json_encode(['error' => 'Date Issued must be a valid date (e.g., 2026-07-25).']);
            exit;
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
            $seq = null;
        }

        $stmt = $db->prepare("INSERT INTO valid_credentials (name, credential_id, type, prefix, seq, issued_date, issued_year, qualification, grade, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $credential_id, $type, $prefix, $seq, $issued_date, $year, ($qualification ?: null), ($grade ?: null), ($status_in ?: 'Active')]);

        if ($db->inTransaction()) {
            $db->commit();
        }

        echo json_encode(['success' => true, 'credential_id' => $credential_id]);
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
?>
