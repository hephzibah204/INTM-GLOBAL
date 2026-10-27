<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Authentication check
$is_authenticated = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if (!$is_authenticated) {
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    $raw_input = file_get_contents('php://input');
    $body = !empty($raw_input) ? json_decode($raw_input, true) : [];
    $api_pass = $body['admin_pass'] ?? $body['api_key'] ?? $_POST['admin_pass'] ?? $_GET['admin_pass'] ?? '';

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
    echo json_encode(['ok' => false, 'error' => 'Unauthorized. Please login to the admin dashboard.']);
    exit;
}

$repo_owner = 'hephzibah204';
$repo_name = 'INTM-GLOBAL';
$repo_branch = 'main';
$root_dir = realpath(__DIR__ . '/..');
$version_file = $root_dir . '/version.json';

// Helper to fetch JSON from GitHub API with fallback DNS resolution
function github_request(string $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'INTM-Global-Updater/1.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    // Include known IP resolution in case local DNS has issues
    curl_setopt($ch, CURLOPT_RESOLVE, [
        'api.github.com:443:140.82.121.6',
        'codeload.github.com:443:140.82.121.9'
    ]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($res === false) {
        return ['error' => $err ?: 'Network error connecting to GitHub.'];
    }
    if ($code !== 200) {
        return ['error' => "GitHub returned HTTP $code: " . substr($res, 0, 200)];
    }
    $json = json_decode($res, true);
    if (!is_array($json)) {
        return ['error' => 'Invalid JSON received from GitHub.'];
    }
    return ['data' => $json];
}

// Read current installed version
function get_current_version($version_file, $root_dir) {
    if (is_file($version_file)) {
        $content = json_decode(file_get_contents($version_file), true);
        if (is_array($content)) {
            return $content;
        }
    }
    // Fallback: check git if present
    $git_head = $root_dir . '/.git/refs/heads/main';
    if (is_file($git_head)) {
        $sha = trim((string)file_get_contents($git_head));
        return [
            'sha' => $sha,
            'short_sha' => substr($sha, 0, 7),
            'branch' => 'main',
            'message' => 'Initial Git Version',
            'last_updated' => date('Y-m-d H:i:s')
        ];
    }
    return [
        'sha' => 'unknown',
        'short_sha' => 'unknown',
        'branch' => 'main',
        'message' => 'Initial Install',
        'last_updated' => date('Y-m-d H:i:s')
    ];
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'check';
$raw_input = file_get_contents('php://input');
if (!empty($raw_input)) {
    $body = json_decode($raw_input, true);
    if (!empty($body['action'])) {
        $action = $body['action'];
    }
}

// -------------------------------------------------------------
// ACTION: CHECK FOR UPDATES
// -------------------------------------------------------------
if ($action === 'check') {
    $current = get_current_version($version_file, $root_dir);
    $api_url = "https://api.github.com/repos/{$repo_owner}/{$repo_name}/commits/{$repo_branch}";
    $res = github_request($api_url);

    if (isset($res['error'])) {
        echo json_encode([
            'ok' => false,
            'error' => $res['error'],
            'current' => $current
        ]);
        exit;
    }

    $commit = $res['data'];
    $latest_sha = $commit['sha'] ?? '';
    $short_sha = substr($latest_sha, 0, 7);
    $message = $commit['commit']['message'] ?? '';
    $author = $commit['commit']['author']['name'] ?? ($commit['author']['login'] ?? 'GitHub');
    $date = $commit['commit']['committer']['date'] ?? '';

    $is_update_available = false;
    $current_sha = $current['sha'] ?? '';
    if ($current_sha !== 'unknown' && $latest_sha !== '') {
        $is_update_available = strcasecmp($current_sha, $latest_sha) !== 0;
    } elseif ($latest_sha !== '') {
        $is_update_available = true;
    }

    echo json_encode([
        'ok' => true,
        'update_available' => $is_update_available,
        'current' => $current,
        'latest' => [
            'sha' => $latest_sha,
            'short_sha' => $short_sha,
            'message' => $message,
            'author' => $author,
            'date' => $date,
            'url' => "https://github.com/{$repo_owner}/{$repo_name}/commit/{$latest_sha}"
        ],
        'checked_at' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// -------------------------------------------------------------
// ACTION: APPLY UPDATE
// -------------------------------------------------------------
if ($action === 'apply' || $action === 'update') {
    @set_time_limit(300);
    @ini_set('memory_limit', '256M');

    if (!class_exists('ZipArchive')) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'ZipArchive extension is not enabled in PHP on this server.']);
        exit;
    }

    // 1. Fetch latest commit metadata
    $api_url = "https://api.github.com/repos/{$repo_owner}/{$repo_name}/commits/{$repo_branch}";
    $res = github_request($api_url);
    if (isset($res['error'])) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Could not verify latest version: ' . $res['error']]);
        exit;
    }

    $commit = $res['data'];
    $latest_sha = $commit['sha'] ?? '';
    $commit_msg = $commit['commit']['message'] ?? '';
    $commit_author = $commit['commit']['author']['name'] ?? ($commit['author']['login'] ?? 'GitHub');
    $commit_date = $commit['commit']['committer']['date'] ?? '';

    // 2. Download repository archive from GitHub
    $zip_urls = [
        "https://codeload.github.com/{$repo_owner}/{$repo_name}/zip/refs/heads/{$repo_branch}",
        "https://github.com/{$repo_owner}/{$repo_name}/archive/refs/heads/{$repo_branch}.zip"
    ];

    $temp_zip = sys_get_temp_dir() . '/intm_update_' . time() . '_' . mt_rand(1000, 9999) . '.zip';
    $download_success = false;
    $download_error = '';

    foreach ($zip_urls as $url) {
        $ch = curl_init($url);
        $fp = fopen($temp_zip, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'INTM-Global-Updater/1.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_RESOLVE, [
            'api.github.com:443:140.82.121.6',
            'codeload.github.com:443:140.82.121.9'
        ]);
        $exec_res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
        fclose($fp);

        if ($exec_res && $code === 200 && is_file($temp_zip) && filesize($temp_zip) > 1000) {
            $download_success = true;
            break;
        } else {
            $download_error = $err ?: "HTTP $code";
            @unlink($temp_zip);
        }
    }

    if (!$download_success) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Failed to download update archive from GitHub: ' . $download_error]);
        exit;
    }

    // 3. Extract the ZIP archive
    $extract_dir = sys_get_temp_dir() . '/intm_extracted_' . time() . '_' . mt_rand(1000, 9999);
    if (!mkdir($extract_dir, 0755, true)) {
        @unlink($temp_zip);
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Failed to create temporary extraction folder.']);
        exit;
    }

    $zip = new ZipArchive();
    $zip_open = $zip->open($temp_zip);
    if ($zip_open !== true) {
        @unlink($temp_zip);
        @rmdir($extract_dir);
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Failed to unpack update zip archive. Code: ' . $zip_open]);
        exit;
    }

    $zip->extractTo($extract_dir);
    $zip->close();
    @unlink($temp_zip);

    // Locate the root folder inside the unpacked zip (typically INTM-GLOBAL-main)
    $items = scandir($extract_dir);
    $source_folder = null;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        if (is_dir($extract_dir . '/' . $item)) {
            $source_folder = $extract_dir . '/' . $item;
            break;
        }
    }

    if (!$source_folder) {
        $source_folder = $extract_dir;
    }

    // 4. Copy files to project root with STRICT PROTECTIONS for database, uploads, and local configs
    $protected_patterns = [
        '/^intm_submissions\.db/i',
        '/\.db$/i',
        '/\.sqlite$/i',
        '/\.sqlite3$/i',
        '/^uploads(\/|$)/i',
        '/^config\.local\.php$/i',
        '/^\.env$/i',
        '/^\.git(\/|$)/i',
        '/^~\$.*$/i',
        '/\.crdownload$/i'
    ];

    $is_protected = function (string $rel_path) use ($protected_patterns) {
        $normalized = str_replace('\\', '/', $rel_path);
        $normalized = ltrim($normalized, '/');
        foreach ($protected_patterns as $p) {
            if (preg_match($p, $normalized) || preg_match($p, basename($normalized))) {
                return true;
            }
        }
        return false;
    };

    $updated_files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source_folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $source_path = $item->getPathname();
        $rel_path = substr($source_path, strlen($source_folder) + 1);
        $target_path = $root_dir . '/' . $rel_path;

        if ($is_protected($rel_path)) {
            continue;
        }

        if ($item->isDir()) {
            if (!is_dir($target_path)) {
                @mkdir($target_path, 0755, true);
            }
        } else {
            $target_dir = dirname($target_path);
            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0755, true);
            }
            if (@copy($source_path, $target_path)) {
                $updated_files[] = str_replace('\\', '/', $rel_path);
            }
        }
    }

    // 5. Clean up temporary extract folder
    $cleanup = function ($dir) use (&$cleanup) {
        if (!is_dir($dir)) return;
        $files = scandir($dir);
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            $p = $dir . '/' . $f;
            if (is_dir($p)) $cleanup($p);
            else @unlink($p);
        }
        @rmdir($dir);
    };
    $cleanup($extract_dir);

    // 6. Record new version
    $version_data = [
        'sha' => $latest_sha,
        'short_sha' => substr($latest_sha, 0, 7),
        'branch' => $repo_branch,
        'message' => $commit_msg,
        'author' => $commit_author,
        'date' => $commit_date,
        'repository' => "https://github.com/{$repo_owner}/{$repo_name}",
        'last_updated' => date('Y-m-d H:i:s')
    ];
    file_put_contents($version_file, json_encode($version_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo json_encode([
        'ok' => true,
        'message' => 'System successfully updated to latest version from GitHub!',
        'files_updated_count' => count($updated_files),
        'version' => $version_data,
        'updated_files_sample' => array_slice($updated_files, 0, 15)
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['ok' => false, 'error' => 'Unknown action requested.']);
