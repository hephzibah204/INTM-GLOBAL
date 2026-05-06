<?php
// INTM Global – Database Helper (SQLite)

$db = null;
$db_error = null;
$db_path = __DIR__ . '/intm_submissions.db';

try {
    if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        throw new RuntimeException('SQLite (PDO_SQLITE) is not available on this server.');
    }

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

    $db->exec("CREATE TABLE IF NOT EXISTS trainer_submissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        scheme TEXT NOT NULL,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        location TEXT,
        organisation TEXT,
        qualifications TEXT,
        experience_years TEXT,
        message TEXT,
        cv_path TEXT,
        proof_path TEXT,
        status TEXT DEFAULT 'new',
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS valid_credentials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        credential_id TEXT NOT NULL UNIQUE,
        type TEXT NOT NULL,
        prefix TEXT,
        seq INTEGER,
        issued_date TEXT,
        issued_year INTEGER,
        qualification TEXT,
        grade TEXT,
        status TEXT DEFAULT 'Active',
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS app_settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL,
        updated_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS site_content (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        page_id TEXT NOT NULL,
        section_id TEXT NOT NULL UNIQUE,
        content TEXT NOT NULL,
        type TEXT DEFAULT 'text',
        updated_at TEXT DEFAULT (datetime('now'))
    )");

    $ensure_column = function (string $table, string $column, string $definition) use ($db) {
        $cols = $db->query("PRAGMA table_info($table)")->fetchAll();
        foreach ($cols as $c) {
            if (($c['name'] ?? null) === $column) return;
        }
        $db->exec("ALTER TABLE $table ADD COLUMN $column $definition");
    };

    $ensure_column('membership_submissions', 'tier', 'TEXT');
    $ensure_column('membership_submissions', 'role', 'TEXT');
    $ensure_column('membership_submissions', 'qualifications', 'TEXT');
    $ensure_column('membership_submissions', 'cv_path', 'TEXT');
    $ensure_column('membership_submissions', 'status', "TEXT DEFAULT 'new'");
    $ensure_column('membership_submissions', 'created_at', "TEXT DEFAULT (datetime('now'))");

    $ensure_column('trainer_submissions', 'scheme', 'TEXT');
    $ensure_column('trainer_submissions', 'name', 'TEXT');
    $ensure_column('trainer_submissions', 'email', 'TEXT');
    $ensure_column('trainer_submissions', 'phone', 'TEXT');
    $ensure_column('trainer_submissions', 'location', 'TEXT');
    $ensure_column('trainer_submissions', 'organisation', 'TEXT');
    $ensure_column('trainer_submissions', 'qualifications', 'TEXT');
    $ensure_column('trainer_submissions', 'experience_years', 'TEXT');
    $ensure_column('trainer_submissions', 'message', 'TEXT');
    $ensure_column('trainer_submissions', 'cv_path', 'TEXT');
    $ensure_column('trainer_submissions', 'proof_path', 'TEXT');
    $ensure_column('trainer_submissions', 'status', "TEXT DEFAULT 'new'");
    $ensure_column('trainer_submissions', 'created_at', "TEXT DEFAULT (datetime('now'))");

    $ensure_column('valid_credentials', 'prefix', 'TEXT');
    $ensure_column('valid_credentials', 'seq', 'INTEGER');
    $ensure_column('valid_credentials', 'issued_date', 'TEXT');
    $ensure_column('valid_credentials', 'issued_year', 'INTEGER');
    $ensure_column('valid_credentials', 'qualification', 'TEXT');
    $ensure_column('valid_credentials', 'grade', 'TEXT');

} catch (Throwable $e) {
    $db = null;
    $db_error = $e->getMessage();
}

/**
 * Fetch all content for a specific page
 */
function get_page_content($page_id) {
    global $db;
    if (!$db) return [];
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
$cfg = require __DIR__ . '/config.php';
$admin_user = (string)($cfg['admin_user'] ?? 'admin');
$admin_pass = (string)($cfg['admin_pass'] ?? 'intm@2025');
?>
