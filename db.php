<?php
// INTM Global – Database Helper (SQLite)

$db_path = __DIR__ . '/intm_submissions.db';

try {
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialise Tables if they don't exist
    $db->exec("CREATE TABLE IF NOT EXISTS contact_submissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        subject TEXT,
        message TEXT,
        status TEXT DEFAULT 'new',
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS membership_submissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        tier TEXT,
        role TEXT,
        qualifications TEXT,
        cv_path TEXT,
        status TEXT DEFAULT 'new',
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS workshop_submissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        designation TEXT,
        workshop TEXT,
        message TEXT,
        status TEXT DEFAULT 'new',
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS collaboration_submissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        organisation TEXT NOT NULL,
        contact_name TEXT,
        email TEXT NOT NULL,
        phone TEXT,
        website TEXT,
        collab_type TEXT,
        message TEXT,
        status TEXT DEFAULT 'new',
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS valid_credentials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        credential_id TEXT NOT NULL UNIQUE,
        type TEXT NOT NULL,
        status TEXT DEFAULT 'Active',
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS site_content (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        page_id TEXT NOT NULL,
        section_id TEXT NOT NULL UNIQUE,
        content TEXT NOT NULL,
        type TEXT DEFAULT 'text',
        updated_at TEXT DEFAULT (datetime('now'))
    )");

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

/**
 * Fetch all content for a specific page
 */
function get_page_content($page_id) {
    global $db;
    $stmt = $db->prepare("SELECT section_id, content FROM site_content WHERE page_id = ?");
    $stmt->execute([$page_id]);
    $results = $stmt->fetchAll();
    $content = [];
    foreach ($results as $row) {
        $content[$row['section_id']] = $row['content'];
    }
    return $content;
}

// Global configuration
$admin_user = "admin";
$admin_pass = "intm@2025";
?>
