<?php
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit;
}

try {
    $stats = [
        'contact' => ['total' => $db->query("SELECT COUNT(*) FROM contact_submissions")->fetchColumn(), 'new' => $db->query("SELECT COUNT(*) FROM contact_submissions WHERE status='new'")->fetchColumn()],
        'membership' => ['total' => $db->query("SELECT COUNT(*) FROM membership_submissions")->fetchColumn(), 'new' => $db->query("SELECT COUNT(*) FROM membership_submissions WHERE status='new'")->fetchColumn()],
        'workshop' => ['total' => $db->query("SELECT COUNT(*) FROM workshop_submissions")->fetchColumn(), 'new' => $db->query("SELECT COUNT(*) FROM workshop_submissions WHERE status='new'")->fetchColumn()],
    ];
    $stats['all_new'] = $stats['contact']['new'] + $stats['membership']['new'] + $stats['workshop']['new'];

    $submissions = [
        'membership' => $db->query("SELECT * FROM membership_submissions ORDER BY created_at DESC")->fetchAll(),
        'workshop' => $db->query("SELECT * FROM workshop_submissions ORDER BY created_at DESC")->fetchAll(),
        'contact' => $db->query("SELECT * FROM contact_submissions ORDER BY created_at DESC")->fetchAll(),
    ];

    $credentials = $db->query("SELECT * FROM valid_credentials ORDER BY created_at DESC")->fetchAll();
    $content = $db->query("SELECT * FROM site_content ORDER BY page_id, section_id")->fetchAll();

    echo json_encode([
        'stats' => $stats,
        'submissions' => $submissions,
        'credentials' => $credentials,
        'content' => $content
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
