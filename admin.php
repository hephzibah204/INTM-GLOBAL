<?php
session_start();
require_once __DIR__ . '/db.php';

if (!$db) {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Error | INTM Global</title><style>body{font-family:DM Sans, sans-serif;background:#f8f9fa;margin:0;padding:40px;color:#2c2c2c} .card{max-width:760px;margin:0 auto;background:white;border:1px solid #eee;border-radius:12px;padding:28px;box-shadow:0 10px 30px rgba(0,0,0,0.06)} h1{margin:0 0 10px;font-size:22px} p{margin:10px 0;line-height:1.6} code{background:#f1f3f5;padding:2px 6px;border-radius:6px} .hint{color:#555}</style></head><body><div class="card"><h1>Database unavailable</h1><p class="hint">The admin dashboard requires SQLite support on the server. Your hosting environment appears to be missing it or cannot write to the database file.</p><p><strong>Server message:</strong> <code>' . htmlspecialchars($db_error ?: 'Unknown error', ENT_QUOTES) . '</code></p><p class="hint">On cPanel, enable <code>PDO_SQLITE</code>/<code>sqlite3</code> (if available) or migrate to MySQL.</p></div></body></html>';
    exit;
}

// Simple Login Check
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $u = trim((string)($_POST['username'] ?? ''));
        $p = trim((string)($_POST['password'] ?? ''));
        if (hash_equals((string)$admin_user, $u) && hash_equals((string)$admin_pass, $p)) {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Invalid credentials";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Admin Login | INTM</title>
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #5a7a5e; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .error { color: #b5874a; font-size: 14px; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 style="text-align:center; color:#2c2c2c;">Admin Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | INTM Global</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --sidebar: #2c2c2c; --bg: #f4f6f8; --primary: #5a7a5e; --accent: #b5874a; --text: #333; --border: #e1e4e8; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar Responsive */
        .sidebar { width: 260px; background: var(--sidebar); color: white; display: flex; flex-direction: column; flex-shrink: 0; transition: transform 0.3s ease; z-index: 1001; }
        .sidebar-header { padding: 30px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { flex-grow: 1; padding: 20px 0; overflow-y: auto; }
        .menu-item { padding: 12px 25px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: 0.2s; color: rgba(255,255,255,0.7); font-size: 14px; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,0.05); color: white; }
        .menu-item.active { border-left: 4px solid var(--primary); }
        
        .main-content { flex-grow: 1; overflow-y: auto; display: flex; flex-direction: column; width: 100%; }
        header { background: white; padding: 15px 25px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1000; }
        
        .mobile-toggle { display: none; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text); padding: 5px; }
        
        .view-section { padding: 25px; display: none; }
        .view-section.active { display: block; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .stat-card h3 { font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 8px; }
        .stat-card .val { font-size: 24px; font-weight: 700; color: var(--primary); }
        
        .table-container { background: white; border-radius: 12px; border: 1px solid var(--border); overflow-x: auto; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { background: #f8f9fa; padding: 12px 15px; text-align: left; font-size: 12px; font-weight: 600; color: #666; border-bottom: 1px solid var(--border); }
        td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        
        /* Modal for Detail View */
        #detailModal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; padding: 15px; }
        #detailModal .content { background:white; width:100%; max-width:600px; border-radius:12px; padding:25px; position:relative; max-height: 90vh; overflow-y: auto; }
        .detail-row { display:flex; flex-direction:column; gap:4px; padding:10px 0; border-bottom:1px solid #f0f0f0; }
        .detail-row:last-child { border-bottom:none; }
        .detail-row label { font-size:11px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:0.5px; }
        .detail-row span { font-size:14px; color:#333; word-break:break-all; }
        .file-link { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; background:var(--primary); color:white; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; margin-top:4px; transition:opacity 0.2s; }
        .file-link:hover { opacity:0.85; }
        .file-link.download { background:#b5874a; }
        
        /* Mobile Breakpoints */
        @media (max-width: 768px) {
            .sidebar { position: fixed; height: 100%; transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .mobile-toggle { display: block; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1000; }
            .sidebar-overlay.active { display: block; }
            .view-section { padding: 15px; }
            header { padding: 15px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:18px; letter-spacing:1px;">INTM ADMIN</h2>
            <button class="mobile-toggle" onclick="toggleSidebar()" style="color:white; font-size:20px;">&times;</button>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item active" onclick="switchView('dashboard', this)">📊 Dashboard</div>
            <div class="menu-item" onclick="switchView('membership', this)">🎓 Membership</div>
            <div class="menu-item" onclick="switchView('trainer', this)">🧑‍🏫 Trainers</div>
            <div class="menu-item" onclick="switchView('workshop', this)">📅 Workshops</div>
            <div class="menu-item" onclick="switchView('collaboration', this)">🤝 Collaborations</div>
            <div class="menu-item" onclick="switchView('contact', this)">✉️ Messages</div>
            <div class="menu-item" onclick="switchView('credentials', this)">🛡️ Credentials</div>
            <div class="menu-item" onclick="switchView('content', this)">📝 Content CMS</div>
            <div class="menu-item" onclick="switchView('update', this)">🔄 System Update</div>
        </div>
        <div style="padding:20px; border-top:1px solid rgba(255,255,255,0.1);">
            <a href="api/admin_logout.php" style="color:rgba(255,255,255,0.5); text-decoration:none; font-size:14px;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <div style="display:flex; align-items:center; gap:15px;">
                <button class="mobile-toggle" onclick="toggleSidebar()">☰</button>
                <h1 id="view-title" style="font-size:18px;">Dashboard Overview</h1>
            </div>
            <div style="color:#666; font-size:13px; display:none;" class="desktop-welcome">Welcome, <strong>Admin</strong></div>
        </header>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            }
        </script>

        <!-- DASHBOARD VIEW -->
        <div id="view-dashboard" class="view-section active">
            <div class="stats-grid" id="stats-summary">
                <!-- Dynamic Stats -->
            </div>
            <div style="background:white; padding:30px; border-radius:12px; border:1px solid var(--border);">
                <h3>Recent Submissions</h3>
                <p style="color:#666; margin-bottom:20px; font-size:14px;">Quick overview of latest interactions.</p>
                <div id="recent-list">Select a category to view data.</div>
            </div>
        </div>

        <!-- LIST VIEWS (Generic Container) -->
        <div id="view-membership" class="view-section">
            <div class="table-container" id="membership-table"></div>
        </div>
        <div id="view-trainer" class="view-section">
            <div class="table-container" id="trainer-table"></div>
        </div>
        <div id="view-workshop" class="view-section">
            <div class="table-container" id="workshop-table"></div>
        </div>
        <div id="view-contact" class="view-section">
            <div class="table-container" id="contact-table"></div>
        </div>
        <div id="view-collaboration" class="view-section">
            <div class="table-container" id="collaboration-table"></div>
        </div>
        
        <!-- CREDENTIALS VIEW -->
        <div id="view-credentials" class="view-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3>Professional Registry</h3>
                <button class="btn-primary" onclick="openAddCredential()" style="padding:8px 16px; background:var(--primary); color:white; border:none; border-radius:6px; cursor:pointer;">+ Add Credential</button>
            </div>
            <div style="background:white; padding:18px; border-radius:12px; border:1px solid var(--border); margin-bottom:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div style="font-weight:700; color:var(--primary);">Prefix Defaults</div>
                        <div style="font-size:13px; color:#666; margin-top:4px;">Set default prefixes used for automatic credential numbering (prefix-year-sequence).</div>
                    </div>
                    <button class="btn-action" id="savePrefixDefaultsBtn" style="padding:8px 14px;">Save Prefixes</button>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top:14px;">
                    <div>
                        <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Certificate Prefix</label>
                        <input type="text" id="prefixCert" placeholder="e.g. INTM-CERT" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Membership Prefix</label>
                        <input type="text" id="prefixMem" placeholder="e.g. INTM-MEM" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                    </div>
                </div>
                <div id="prefixSaveMsg" style="font-size:13px; color:#666; margin-top:10px; display:none;"></div>
            </div>
            <div id="credentials-table"></div>
        </div>

        <!-- CONTENT VIEW -->
        <div id="view-content" class="view-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3>Website Content Manager</h3>
                <button class="btn-primary" onclick="saveCMS()" style="padding:8px 16px; background:var(--primary); color:white; border:none; border-radius:6px; cursor:pointer;">Save All Changes</button>
            </div>
            <div id="cms-editor" style="display:grid; gap:20px;"></div>
        </div>

        <!-- SYSTEM UPDATE VIEW -->
        <div id="view-update" class="view-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                <div>
                    <h3 style="margin:0; font-size:18px;">GitHub System Update</h3>
                    <p style="margin:4px 0 0; font-size:13px; color:#666;">Update INTM Global directly from GitHub without using command-line Git.</p>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="btn-primary" id="btnCheckUpdate" onclick="checkForUpdates()" style="padding:9px 18px; background:var(--primary); color:white; border:none; border-radius:6px; cursor:pointer; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                        <span>🔍</span> Check for Updates
                    </button>
                    <button class="btn-primary" id="btnApplyUpdate" onclick="applySystemUpdate()" style="padding:9px 18px; background:#b5874a; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:600; display:none; align-items:center; gap:6px;">
                        <span>⬇️</span> Update System Now
                    </button>
                </div>
            </div>

            <!-- Version Status Cards -->
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom:24px;">
                <div style="background:white; padding:20px; border-radius:12px; border:1px solid var(--border); box-shadow:0 2px 10px rgba(0,0,0,0.02);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <div style="font-size:12px; text-transform:uppercase; color:#888; font-weight:700; letter-spacing:0.5px;">Current Version</div>
                            <div id="currentVersionSha" style="font-size:20px; font-weight:700; color:var(--primary); margin:6px 0;">Loading...</div>
                        </div>
                        <span id="updateBadge" style="display:inline-block; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:700; background:#eee; color:#666;">Checking...</span>
                    </div>
                    <div id="currentVersionDetails" style="font-size:13px; color:#555; line-height:1.6; margin-top:8px;">
                        Loading version info...
                    </div>
                </div>

                <div style="background:white; padding:20px; border-radius:12px; border:1px solid var(--border); box-shadow:0 2px 10px rgba(0,0,0,0.02);">
                    <div style="font-size:12px; text-transform:uppercase; color:#888; font-weight:700; letter-spacing:0.5px;">Latest on GitHub</div>
                    <div id="latestVersionSha" style="font-size:20px; font-weight:700; color:#333; margin:6px 0;">—</div>
                    <div id="latestVersionDetails" style="font-size:13px; color:#555; line-height:1.6; margin-top:8px;">
                        Click "Check for Updates" to compare with the GitHub repository.
                    </div>
                </div>
            </div>

            <!-- Safety & Data Protection Notice -->
            <div style="background:rgba(90,122,94,0.08); border-left:4px solid var(--primary); padding:16px 20px; border-radius:0 8px 8px 0; margin-bottom:24px;">
                <div style="font-weight:700; color:var(--primary); font-size:14px; margin-bottom:4px;">🔒 Data Protection Guarantee</div>
                <div style="font-size:13px; color:#444; line-height:1.6;">
                    Updating downloads the latest templates, API endpoints, assets, and styling directly from GitHub.
                    Your live database (<code>intm_submissions.db</code>), member registrations, certificates, and uploaded files in <code>uploads/</code> are <strong>strictly protected and never overwritten</strong>.
                </div>
            </div>

            <!-- Console Log Output -->
            <div style="background:#1e1e1e; border-radius:8px; padding:16px; color:#e0e0e0; font-family:Consolas, Monaco, monospace; font-size:13px; max-height:280px; overflow-y:auto;" id="updateConsole">
                <div style="color:#888;">[Ready] System update manager initialized. Ready to check for updates.</div>
            </div>
        </div>
    </div>

    <!-- DETAIL MODAL -->
    <div id="detailModal">
        <div class="content">
            <span class="close-btn" onclick="closeDetail()" style="position:absolute; top:15px; right:20px; cursor:pointer; font-size:24px;">&times;</span>
            <h2 id="detail-title" style="margin-bottom:20px; color:var(--primary);">Submission Detail</h2>
            <div id="detail-body"></div>
            <div style="margin-top:30px; display:flex; gap:10px;">
                <button class="btn-action" id="mark-read-btn">Mark as Read</button>
                <button class="btn-action" id="archive-btn">Archive</button>
                <button class="btn-action" id="delete-btn" style="color:red; margin-left:auto;">Delete</button>
            </div>
        </div>
    </div>

    <!-- ADD CREDENTIAL MODAL -->
    <div id="credModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; padding: 12px;">
        <div class="content" style="background:white; width:100%; max-width:420px; max-height:88vh; overflow:auto; border-radius:12px; padding:18px; position:relative;">
            <span class="close-btn" onclick="closeCredModal()" style="position:absolute; top:15px; right:20px; cursor:pointer; font-size:24px;">&times;</span>
            <h2 style="margin-bottom:20px; color:var(--primary);">Add New Credential</h2>
            <form id="addCredForm">
                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Name (Required)</label>
                    <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Type</label>
                    <select name="type" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                        <option value="Certificate">Certificate</option>
                        <option value="Membership">Membership</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Date Issued (Required)</label>
                    <input type="date" name="issued_date" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Qualification Type</label>
                    <input type="text" name="qualification" placeholder="e.g. Diploma in Nutritional Therapy – Level 1" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Grade</label>
                    <input type="text" name="grade" placeholder="e.g. Distinction" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Credential ID (Optional)</label>
                    <input type="text" name="credential_id" id="credentialIdInput" placeholder="Leave blank to auto-generate" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; text-transform:uppercase;">
                    <div id="credPreview" style="background:#f8f9fa; border:1px solid #e1e4e8; border-radius:8px; padding:8px 12px; font-size:12px; color:#555; margin-top:6px;">Will generate after save</div>
                    <div style="font-size:11px; color:#777; margin-top:4px;">Leave blank to auto-generate by year and sequence, or enter a specific Certificate ID.</div>
                </div>
                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:12px; color:#666; margin-bottom:5px;">Initial Status</label>
                    <select name="status" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Revoked">Revoked</option>
                        <option value="Expired">Expired</option>
                    </select>
                </div>
                <button type="submit" style="width:100%; padding:12px; background:var(--primary); color:white; border:none; border-radius:6px; font-weight:600; cursor:pointer;">Save Credential</button>
            </form>
        </div>
    </div>

    <script>
        let currentData = {};
        let prefixDefaults = { certificate: 'INTM-CERT', membership: 'INTM-MEM' };
        
        async function loadData() {
            try {
                const res = await fetch('api/admin_data.php?_t=' + Date.now(), { cache: 'no-store' });
                if (res.status === 401) {
                    window.location.href = 'admin.php';
                    return;
                }
                const json = await res.json().catch(() => null);
                if (!res.ok) {
                    const msg = (json && json.error) || 'Failed to load admin data.';
                    document.getElementById('stats-summary').innerHTML = `<div style="background:white; border:1px solid var(--border); border-radius:12px; padding:18px; color:#666;">${msg}</div>`;
                    return;
                }

                currentData = json || {};
                if (currentData.prefix_defaults) {
                    prefixDefaults = {
                        certificate: String(currentData.prefix_defaults.certificate || 'INTM-CERT').toUpperCase(),
                        membership: String(currentData.prefix_defaults.membership || 'INTM-MEM').toUpperCase()
                    };
                }

                const prefixCert = document.getElementById('prefixCert');
                const prefixMem = document.getElementById('prefixMem');
                if (prefixCert) prefixCert.value = prefixDefaults.certificate;
                if (prefixMem) prefixMem.value = prefixDefaults.membership;

                const prefixSaveMsg = document.getElementById('prefixSaveMsg');
                if (prefixSaveMsg && currentData.db && currentData.db.file) {
                    prefixSaveMsg.style.display = 'block';
                    prefixSaveMsg.textContent = `DB: ${currentData.db.file} · Credentials: ${currentData.db.credentials_total}`;
                }

                renderStats();
                renderTables();
                renderCMS();
            } catch (e) {
                document.getElementById('stats-summary').innerHTML = `<div style="background:white; border:1px solid var(--border); border-radius:12px; padding:18px; color:#666;">Network error while loading admin data.</div>`;
            }
        }

        function renderStats() {
            const stats = currentData.stats;
            document.getElementById('stats-summary').innerHTML = `
                <div class="stat-card"><h3>Memberships</h3><div class="val">${stats.membership.total}</div></div>
                <div class="stat-card"><h3>Trainer Applications</h3><div class="val">${stats.trainer.total}</div></div>
                <div class="stat-card"><h3>Workshops</h3><div class="val">${stats.workshop.total}</div></div>
                <div class="stat-card"><h3>Partnerships</h3><div class="val">${stats.collaboration.total}</div></div>
                <div class="stat-card"><h3>New Alerts</h3><div class="val" style="color:var(--accent)">${stats.all_new}</div></div>
            `;
        }

        function renderTables() {
            ['membership', 'trainer', 'workshop', 'contact', 'collaboration'].forEach(type => {
                const list = currentData.submissions[type];
                let html = `<table><thead><tr><th>${type === 'collaboration' ? 'Organisation' : 'Name'}</th><th>Email</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>`;
                list.forEach(r => {
                    html += `
                        <tr>
                            <td>${r.name || r.organisation || r.contact_name}</td>
                            <td>${r.email}</td>
                            <td><span class="badge badge-${r.status}">${r.status}</span></td>
                            <td>${r.created_at.split(' ')[0]}</td>
                            <td><button class="btn-action" onclick="viewDetail('${type}', ${r.id})">View</button></td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                document.getElementById(type + '-table').innerHTML = html;
            });

            // Render Credentials
            const creds = currentData.credentials || [];
            let cHtml = `<table><thead><tr><th>Name</th><th>Credential ID</th><th>Type</th><th>Qualification</th><th>Date Issued</th><th>Status</th><th>Action</th></tr></thead><tbody>`;
            if (creds.length === 0) {
                cHtml += `<tr><td colspan="7" style="text-align:center; padding:24px; color:#888;">No credentials found. Click "+ Add Credential" to register one.</td></tr>`;
            } else {
                creds.forEach(c => {
                    const opts = ['Active', 'Inactive', 'Revoked', 'Expired'].map(s => `<option value="${s}" ${c.status === s ? 'selected' : ''}>${s}</option>`).join('');
                    const qual = [c.qualification, c.grade ? `(${c.grade})` : ''].filter(Boolean).join(' ');
                    cHtml += `
                        <tr>
                            <td><strong>${c.name}</strong></td>
                            <td><code>${c.credential_id}</code></td>
                            <td>${c.type}</td>
                            <td style="font-size:13px; color:#555;">${qual || '—'}</td>
                            <td>${c.issued_date || (c.created_at ? c.created_at.split(' ')[0] : '—')}</td>
                            <td>
                                <select onchange="updateCredStatus(${c.id}, this.value)" style="padding:6px 8px; border:1px solid #ddd; border-radius:6px; background:white;">
                                    ${opts}
                                </select>
                            </td>
                            <td><button class="btn-action" onclick="deleteCred(${c.id})">Delete</button></td>
                        </tr>
                    `;
                });
            }
            cHtml += `</tbody></table>`;
            document.getElementById('credentials-table').innerHTML = cHtml;
        }

        function renderCMS() {
            let html = '';
            currentData.content.forEach(c => {
                html += `
                    <div style="background:white; padding:20px; border-radius:8px; border:1px solid var(--border);">
                        <label style="font-size:12px; color:#666; display:block; margin-bottom:8px;">${c.page_id.toUpperCase()} > ${c.section_id}</label>
                        <textarea data-id="${c.id}" style="width:100%; min-height:80px; padding:10px; border:1px solid #ddd; border-radius:4px; font-family:inherit;">${c.content}</textarea>
                    </div>
                `;
            });
            document.getElementById('cms-editor').innerHTML = html;
        }

        // Fields whose values are uploaded file paths (stored as filenames in DB)
        const FILE_FIELDS = ['cv_path', 'proof_path', 'file_path', 'attachment_path'];

        function renderFieldValue(key, value) {
            if (!value) return '<span style="color:#aaa;">—</span>';
            if (FILE_FIELDS.includes(key)) {
                const url = `uploads/${encodeURIComponent(value)}`;
                const ext = (value.split('.').pop() || '').toLowerCase();
                const isViewable = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
                let html = '';
                if (isViewable) {
                    html += `<a class="file-link" href="${url}" target="_blank" rel="noopener">👁 View File</a> `;
                }
                html += `<a class="file-link download" href="${url}" download="${value}">⬇ Download</a>`;
                html += `<div style="font-size:12px; color:#aaa; margin-top:4px;">${value}</div>`;
                return html;
            }
            return `<span>${String(value).replace(/</g,'&lt;').replace(/>/g,'&gt;')}</span>`;
        }

        function viewDetail(type, id) {
            const row = currentData.submissions[type].find(r => r.id == id);
            let body = '';
            for(let key in row) {
                if(['id', 'status', 'created_at'].includes(key)) continue;
                body += `<div class="detail-row"><label>${key.replace(/_/g,' ').toUpperCase()}</label>${renderFieldValue(key, row[key])}</div>`;
            }
            // Show status & date at bottom
            body += `<div class="detail-row"><label>STATUS</label><span>${row.status}</span></div>`;
            body += `<div class="detail-row"><label>DATE</label><span>${row.created_at}</span></div>`;
            document.getElementById('detail-body').innerHTML = body;
            document.getElementById('detailModal').style.display = 'flex';
            
            // Set up action buttons
            document.getElementById('mark-read-btn').onclick = () => updateStatus(type, id, 'read');
            document.getElementById('archive-btn').onclick = () => updateStatus(type, id, 'archived');
            document.getElementById('delete-btn').onclick = () => deleteSub(type, id);
        }

        async function updateStatus(type, id, status) {
            const res = await fetch('api/admin_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'status', type, id, status })
            });
            const json = await res.json().catch(() => null);
            if (!res.ok) {
                alert((json && json.error) || 'Update failed.');
                return;
            }
            closeDetail();
            loadData();
        }

        async function deleteSub(type, id) {
            if(!confirm('Delete this submission?')) return;
            const res = await fetch('api/admin_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', type, id })
            });
            const json = await res.json().catch(() => null);
            if (!res.ok) {
                alert((json && json.error) || 'Delete failed.');
                return;
            }
            closeDetail();
            loadData();
        }

        function closeDetail() { document.getElementById('detailModal').style.display = 'none'; }

        function switchView(view, btn) {
            document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
            const section = document.getElementById('view-' + view);
            if (!section) {
                alert('This section is not available.');
                return;
            }
            section.classList.add('active');
            document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('view-title').innerText = view.charAt(0).toUpperCase() + view.slice(1) + (view === 'dashboard' ? ' Overview' : '');

            const sidebar = document.getElementById('sidebar');
            if (sidebar && sidebar.classList.contains('open') && typeof toggleSidebar === 'function') {
                toggleSidebar();
            }

            if (view === 'update') {
                checkForUpdates();
            }
        }

        async function checkForUpdates() {
            const btn = document.getElementById('btnCheckUpdate');
            const applyBtn = document.getElementById('btnApplyUpdate');
            const badge = document.getElementById('updateBadge');
            const consoleBox = document.getElementById('updateConsole');
            const curSha = document.getElementById('currentVersionSha');
            const curDetails = document.getElementById('currentVersionDetails');
            const latSha = document.getElementById('latestVersionSha');
            const latDetails = document.getElementById('latestVersionDetails');

            const log = (msg, color = '#e0e0e0') => {
                if (!consoleBox) return;
                const d = new Date().toLocaleTimeString();
                consoleBox.innerHTML += `<div style="color:${color}; margin-top:4px;">[${d}] ${msg}</div>`;
                consoleBox.scrollTop = consoleBox.scrollHeight;
            };

            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.7';
            }
            if (badge) {
                badge.textContent = 'Checking...';
                badge.style.background = '#eee';
                badge.style.color = '#666';
            }

            log('Checking repository status with GitHub API...', '#9cdcfe');

            try {
                const res = await fetch('api/admin_update.php?action=check', { cache: 'no-store' });
                const json = await res.json().catch(() => null);

                if (!res.ok || !json || !json.ok) {
                    const err = (json && json.error) || 'Failed to check updates from GitHub.';
                    log(`Error: ${err}`, '#f48771');
                    if (badge) {
                        badge.textContent = 'Check Failed';
                        badge.style.background = 'rgba(244,135,113,0.2)';
                        badge.style.color = '#f48771';
                    }
                    return;
                }

                const cur = json.current || {};
                const lat = json.latest || {};

                if (curSha) curSha.textContent = cur.short_sha || cur.sha?.slice(0, 7) || 'Current';
                if (curDetails) {
                    curDetails.innerHTML = `
                        <div><strong>Message:</strong> ${cur.message || '—'}</div>
                        <div style="font-size:12px; color:#888; margin-top:4px;"><strong>Updated:</strong> ${cur.last_updated || '—'}</div>
                    `;
                }

                if (latSha) latSha.textContent = lat.short_sha || lat.sha?.slice(0, 7) || '—';
                if (latDetails) {
                    latDetails.innerHTML = `
                        <div><strong>Latest Commit:</strong> ${lat.message || '—'}</div>
                        <div style="font-size:12px; color:#888; margin-top:4px;">
                            <strong>By:</strong> ${lat.author || '—'} &bull; 
                            <a href="${lat.url}" target="_blank" rel="noopener" style="color:var(--primary); text-decoration:none;">View on GitHub ↗</a>
                        </div>
                    `;
                }

                if (json.update_available) {
                    if (badge) {
                        badge.textContent = 'Update Available';
                        badge.style.background = 'rgba(245,124,0,0.15)';
                        badge.style.color = '#e65100';
                    }
                    if (applyBtn) applyBtn.style.display = 'inline-flex';
                    log(`Update found! Latest commit: ${lat.short_sha} (${lat.message})`, '#4ec9b0');
                } else {
                    if (badge) {
                        badge.textContent = 'Up to Date';
                        badge.style.background = 'rgba(46,125,50,0.15)';
                        badge.style.color = '#2e7d32';
                    }
                    if (applyBtn) applyBtn.style.display = 'none';
                    log('System is up to date with the main branch on GitHub.', '#4ec9b0');
                }
            } catch (e) {
                log(`Network error while checking updates: ${e.message}`, '#f48771');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            }
        }

        async function applySystemUpdate() {
            if (!confirm('Are you sure you want to update the system from GitHub now?\n\nCode files will be updated to the latest commit. Database and uploads will remain untouched.')) {
                return;
            }

            const applyBtn = document.getElementById('btnApplyUpdate');
            const checkBtn = document.getElementById('btnCheckUpdate');
            const badge = document.getElementById('updateBadge');
            const consoleBox = document.getElementById('updateConsole');

            const log = (msg, color = '#e0e0e0') => {
                if (!consoleBox) return;
                const d = new Date().toLocaleTimeString();
                consoleBox.innerHTML += `<div style="color:${color}; margin-top:4px;">[${d}] ${msg}</div>`;
                consoleBox.scrollTop = consoleBox.scrollHeight;
            };

            if (applyBtn) {
                applyBtn.disabled = true;
                applyBtn.style.opacity = '0.7';
                applyBtn.innerHTML = '<span>⏳</span> Updating...';
            }
            if (checkBtn) checkBtn.disabled = true;
            if (badge) {
                badge.textContent = 'Updating...';
                badge.style.background = 'rgba(30,136,229,0.15)';
                badge.style.color = '#1565c0';
            }

            log('Initiating direct update from GitHub...', '#9cdcfe');
            log('Downloading repository archive and extracting safely...', '#ce9178');

            try {
                const res = await fetch('api/admin_update.php?action=apply', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'apply' })
                });

                const json = await res.json().catch(() => null);

                if (!res.ok || !json || !json.ok) {
                    const err = (json && json.error) || 'Update failed during extraction.';
                    log(`Update failed: ${err}`, '#f48771');
                    alert(`Update Error: ${err}`);
                    if (badge) {
                        badge.textContent = 'Update Failed';
                        badge.style.background = 'rgba(244,135,113,0.2)';
                        badge.style.color = '#f48771';
                    }
                    return;
                }

                log(`Successfully updated ${json.files_updated_count} files!`, '#4ec9b0');
                if (json.version) {
                    log(`Current commit is now: ${json.version.short_sha} (${json.version.message})`, '#4ec9b0');
                }
                log('Update process complete!', '#4ec9b0');

                alert('System successfully updated from GitHub!\nThe page will now refresh.');
                window.location.reload();
            } catch (e) {
                log(`Network error during update: ${e.message}`, '#f48771');
                alert(`Network error: ${e.message}`);
            } finally {
                if (applyBtn) {
                    applyBtn.disabled = false;
                    applyBtn.style.opacity = '1';
                    applyBtn.innerHTML = '<span>⬇️</span> Update System Now';
                }
                if (checkBtn) checkBtn.disabled = false;
            }
        }

        async function saveCMS() {
            const updates = [];
            document.querySelectorAll('#cms-editor textarea').forEach(ta => {
                updates.push({ id: ta.dataset.id, content: ta.value });
            });
            const res = await fetch('api/admin_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'cms', updates })
            });
            const json = await res.json().catch(() => null);
            if (!res.ok) {
                alert((json && json.error) || 'CMS update failed.');
                return;
            }
            alert('CMS Updated!');
            loadData();
        }

        async function deleteCred(id) {
            if(!confirm('Delete this credential?')) return;
            const res = await fetch('api/admin_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_cred', id })
            });
            const json = await res.json().catch(() => null);
            if (!res.ok) {
                alert((json && json.error) || 'Delete failed.');
                return;
            }
            loadData();
        }

        async function updateCredStatus(id, status) {
            const res = await fetch('api/admin_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'toggle_cred_status', id, status })
            });
            const json = await res.json().catch(() => null);
            if (!res.ok) {
                alert((json && json.error) || 'Update failed.');
                return;
            }
            loadData();
        }

        function openAddCredential() {
            document.getElementById('credModal').style.display = 'flex';
            const form = document.getElementById('addCredForm');
            const typeEl = form?.querySelector('[name="type"]');
            const prefixEl = form?.querySelector('[name="prefix"]');
            if (typeEl && prefixEl && (prefixEl.value || '').trim() === '') {
                prefixEl.value = (typeEl.value === 'Membership' ? prefixDefaults.membership : prefixDefaults.certificate);
            }
            updateCredPreview();
        }

        function closeCredModal() {
            document.getElementById('credModal').style.display = 'none';
        }

        document.getElementById('addCredForm').onsubmit = async (e) => {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data.action = 'add_credential';

            try {
                const res = await fetch('api/admin_actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const json = await res.json().catch(() => null);
                if (!res.ok) {
                    alert((json && json.error) || 'Add credential failed.');
                    return;
                }

                closeCredModal();
                e.target.reset();

                // Reload data immediately with fresh cache-busting request
                await loadData();

                // Switch to credentials view
                const credsBtn = document.querySelector('.menu-item[onclick*="credentials"]');
                if (credsBtn) switchView('credentials', credsBtn);

                if (json && json.credential_id) {
                    const dbFile = json.db_file ? ` (Database: ${json.db_file})` : '';
                    alert(`✅ Credential successfully created and saved!\n\nCredential ID: ${json.credential_id}${dbFile}`);
                }
            } catch (err) {
                alert('Network error while saving credential: ' + err.message);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Save Credential';
                }
            }
        };

        function updateCredPreview() {
            const form = document.getElementById('addCredForm');
            const preview = document.getElementById('credPreview');
            if (!form || !preview) return;

            const customId = (form.querySelector('[name="credential_id"]')?.value || '').trim();
            if (customId) {
                preview.innerHTML = `Using manual ID: <strong>${customId.toUpperCase()}</strong>`;
                return;
            }

            const type = (form.querySelector('[name="type"]')?.value || 'Certificate').trim();
            const issuedDate = (form.querySelector('[name="issued_date"]')?.value || '').trim();

            const defaultPrefix = type === 'Membership' ? prefixDefaults.membership : prefixDefaults.certificate;
            const prefix = defaultPrefix.toUpperCase();
            const year = issuedDate ? issuedDate.slice(0, 4) : new Date().getFullYear();
            preview.innerHTML = `Auto-generated preview: <strong>${prefix}-${year}-####</strong>`;
        }

        (function wireCredPreview() {
            const form = document.getElementById('addCredForm');
            if (!form) return;
            const typeEl = form.querySelector('[name="type"]');
            const dateEl = form.querySelector('[name="issued_date"]');
            const idEl = form.querySelector('[name="credential_id"]');

            if (typeEl) typeEl.addEventListener('change', updateCredPreview);
            if (dateEl) dateEl.addEventListener('change', updateCredPreview);
            if (idEl) idEl.addEventListener('input', updateCredPreview);
        })();

        (function wirePrefixDefaults() {
            const btn = document.getElementById('savePrefixDefaultsBtn');
            const certEl = document.getElementById('prefixCert');
            const memEl = document.getElementById('prefixMem');
            const msg = document.getElementById('prefixSaveMsg');
            if (!btn || !certEl || !memEl) return;

            const setMsg = (text) => {
                if (!msg) return;
                msg.textContent = text;
                msg.style.display = 'block';
            };

            btn.addEventListener('click', async () => {
                const cert = String(certEl.value || '').trim().toUpperCase();
                const mem = String(memEl.value || '').trim().toUpperCase();
                if (!cert || !mem) {
                    setMsg('Both Certificate and Membership prefixes are required.');
                    return;
                }

                btn.disabled = true;
                btn.style.opacity = '0.7';
                setMsg('Saving…');

                try {
                    const res = await fetch('api/admin_actions.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'set_prefix_defaults', certificate_prefix: cert, membership_prefix: mem })
                    });
                    const json = await res.json().catch(() => null);
                    if (!res.ok) {
                        setMsg((json && json.error) || 'Failed to save prefixes.');
                        return;
                    }

                    prefixDefaults = { certificate: cert, membership: mem };
                    setMsg('Saved. New defaults will be used for new credentials.');
                    updateCredPreview();
                } catch (_) {
                    setMsg('Network error while saving prefixes.');
                } finally {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            });
        })();

        window.onload = loadData;
    </script>
</body>
</html>
