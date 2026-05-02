"""
INTM Global – Form Submission Backend
Flask + SQLite server that:
  - Accepts POST submissions from contact, membership, and collaboration forms
  - Provides a JSON API for the admin panel to query / manage submissions
  - Serves the admin.html page at /admin (protected by a simple password)
"""

import sqlite3
import os
from datetime import datetime
from functools import wraps
from flask import Flask, request, jsonify, send_from_directory, abort, session, render_template
from flask_cors import CORS

SECRET_KEY     = "intm-secret-key-change-in-production"
UPLOAD_FOLDER  = os.path.join(BASE_DIR, "uploads")
ALLOWED_EXTENSIONS = {'pdf', 'doc', 'docx'}

if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

app = Flask(__name__, static_folder=STATIC_DIR, static_url_path="")
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.secret_key = SECRET_KEY
CORS(app, supports_credentials=True)        # allow fetch() from same origin


# ─── Database helpers ─────────────────────────────────────────────────────────
def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_db():
    conn = get_db()
    cur  = conn.cursor()

    cur.execute("""
        CREATE TABLE IF NOT EXISTS contact_submissions (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            name       TEXT NOT NULL,
            email      TEXT NOT NULL,
            phone      TEXT,
            subject    TEXT,
            message    TEXT,
            status     TEXT DEFAULT 'new',
            created_at TEXT DEFAULT (datetime('now'))
        )
    """)

        CREATE TABLE IF NOT EXISTS membership_submissions (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            name       TEXT NOT NULL,
            email      TEXT NOT NULL,
            phone      TEXT,
            tier       TEXT,
            cv_path    TEXT,
            status     TEXT DEFAULT 'new',
            created_at TEXT DEFAULT (datetime('now'))
        )
    """)

    cur.execute("""
        CREATE TABLE IF NOT EXISTS workshop_submissions (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            name         TEXT NOT NULL,
            email        TEXT NOT NULL,
            phone        TEXT,
            workshop     TEXT,
            message      TEXT,
            status       TEXT DEFAULT 'new',
            created_at   TEXT DEFAULT (datetime('now'))
        )
    """)

    cur.execute("""
        CREATE TABLE IF NOT EXISTS collaboration_submissions (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            organisation TEXT NOT NULL,
            contact_name TEXT,
            email        TEXT NOT NULL,
            phone        TEXT,
            website      TEXT,
            collab_type  TEXT,
            message      TEXT,
            status       TEXT DEFAULT 'new',
            created_at   TEXT DEFAULT (datetime('now'))
        )
    """)

    cur.execute("""
        CREATE TABLE IF NOT EXISTS valid_credentials (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            name          TEXT NOT NULL,
            credential_id TEXT NOT NULL UNIQUE,
            type          TEXT NOT NULL,
            status        TEXT DEFAULT 'Active',
            created_at    TEXT DEFAULT (datetime('now'))
        )
    """)

    cur.execute("""
        CREATE TABLE IF NOT EXISTS site_content (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            page_id    TEXT NOT NULL,
            section_id TEXT NOT NULL UNIQUE,
            content    TEXT NOT NULL,
            type       TEXT DEFAULT 'text',
            updated_at TEXT DEFAULT (datetime('now'))
        )
    """)

    # Seed default content if table is empty
    cur.execute("SELECT count(*) FROM site_content")
    if cur.fetchone()[0] == 0:
        defaults = [
            ("home", "home_hero_title", "Nourish Knowledge.<br><em>Transform Lives.</em>", "html"),
            ("home", "home_hero_sub", "The Institute of Nutritional Therapy Management (INTM) is a leading centre for evidence-based nutritional education, professional development, and transformative research across Africa and beyond.", "text"),
            ("about", "about_intro", "The Institute of Nutritional Therapy Management (INTM Global) is a premier professional body dedicated to advancing the field of nutritional science.", "text")
        ]
        cur.executemany("INSERT INTO site_content (page_id, section_id, content, type) VALUES (?, ?, ?, ?)", defaults)

    conn.commit()
    conn.close()


# ─── Auth helpers ─────────────────────────────────────────────────────────────
def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if not session.get("logged_in"):
            return jsonify({"error": "Unauthorised"}), 401
        return f(*args, **kwargs)
    return decorated


# ─── Auth routes ─────────────────────────────────────────────────────────────
@app.route("/api/login", methods=["POST"])
def api_login():
    data = request.get_json(silent=True) or {}
    if (data.get("username") == ADMIN_USERNAME and
            data.get("password") == ADMIN_PASSWORD):
        session["logged_in"] = True
        return jsonify({"success": True})
    return jsonify({"error": "Invalid credentials"}), 401


@app.route("/api/logout", methods=["POST"])
def api_logout():
    session.clear()
    return jsonify({"success": True})


@app.route("/api/me")
def api_me():
    return jsonify({"logged_in": bool(session.get("logged_in"))})


# ─── Public form submission endpoints ─────────────────────────────────────────
@app.route("/api/submit/contact", methods=["POST"])
def submit_contact():
    d = request.get_json(silent=True) or request.form
    required = ["name", "email"]
    for field in required:
        if not d.get(field):
            return jsonify({"error": f"'{field}' is required"}), 400

    conn = get_db()
    conn.execute(
        "INSERT INTO contact_submissions (name,email,phone,subject,message) VALUES (?,?,?,?,?)",
        (d.get("name"), d.get("email"), d.get("phone"),
         d.get("subject"), d.get("message"))
    )
    conn.commit()
    conn.close()
    return jsonify({"success": True, "message": "Contact submission received."}), 201


@app.route("/api/submit/membership", methods=["POST"])
def submit_membership():
    # Handle both JSON and Multipart (for file upload)
    if request.content_type and 'multipart/form-data' in request.content_type:
        d = request.form
        file = request.files.get('cv')
    else:
        d = request.get_json(silent=True) or {}
        file = None

    required = ["name", "email"]
    for field in required:
        if not d.get(field):
            return jsonify({"error": f"'{field}' is required"}), 400

    cv_filename = None
    if file and file.filename:
        ext = file.filename.rsplit('.', 1)[1].lower()
        if ext in ALLOWED_EXTENSIONS:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            cv_filename = f"cv_{timestamp}_{file.filename}"
            file.save(os.path.join(app.config['UPLOAD_FOLDER'], cv_filename))

    conn = get_db()
    conn.execute(
        "INSERT INTO membership_submissions (name,email,phone,tier,cv_path) VALUES (?,?,?,?,?)",
        (d.get("name"), d.get("email"), d.get("phone"), d.get("tier"), cv_filename)
    )
    conn.commit()
    conn.close()
    return jsonify({"success": True, "message": "Membership application received."}), 201


@app.route("/api/submit/workshop", methods=["POST"])
def submit_workshop():
    d = request.get_json(silent=True) or request.form
    required = ["name", "email", "workshop"]
    for field in required:
        if not d.get(field):
            return jsonify({"error": f"'{field}' is required"}), 400

    conn = get_db()
    conn.execute(
        "INSERT INTO workshop_submissions (name,email,phone,workshop,message) VALUES (?,?,?,?,?)",
        (d.get("name"), d.get("email"), d.get("phone"), d.get("workshop"), d.get("message"))
    )
    conn.commit()
    conn.close()
    return jsonify({"success": True, "message": "Workshop registration received."}), 201


@app.route("/api/submit/collaboration", methods=["POST"])
def submit_collaboration():
    d = request.get_json(silent=True) or request.form
    required = ["organisation", "email"]
    for field in required:
        if not d.get(field):
            return jsonify({"error": f"'{field}' is required"}), 400

    conn = get_db()
    conn.execute(
        """INSERT INTO collaboration_submissions
           (organisation,contact_name,email,phone,website,collab_type,message)
           VALUES (?,?,?,?,?,?,?)""",
        (d.get("organisation"), d.get("contact_name"), d.get("email"),
         d.get("phone"), d.get("website"), d.get("collab_type"), d.get("message"))
    )
    conn.commit()
    conn.close()
    return jsonify({"success": True, "message": "Collaboration request received."}), 201


@app.route("/api/verify", methods=["POST"])
def verify_credential():
    d = request.get_json(silent=True) or request.form
    cred_id = d.get("credential_id")
    cred_type = d.get("type") # 'Certificate' or 'Membership'

    if not cred_id or not cred_type:
        return jsonify({"error": "Credential ID and type are required"}), 400

    conn = get_db()
    # Let's do a case-insensitive check and allow partial name match if name is provided, but mostly id and type.
    # Actually, it's safer to just check ID and Type. The UI asks for Name, but we can verify if the Name roughly matches.
    # Let's just check credential_id and type for simplicity and robust verification.
    row = conn.execute(
        "SELECT * FROM valid_credentials WHERE credential_id = ? AND type = ?",
        (cred_id, cred_type)
    ).fetchone()
    conn.close()

    if row:
        if row["status"] == "Active":
            return jsonify({
                "valid": True, 
                "message": f"Credential verified. Issued to {row['name']}."
            })
        else:
            return jsonify({
                "valid": False, 
                "message": f"This credential has been marked as {row['status']}."
            })
    else:
        return jsonify({
            "valid": False, 
            "message": "Credential not found in our records."
        })


# ─── Admin API – list submissions ─────────────────────────────────────────────
def rows_to_list(rows):
    return [dict(r) for r in rows]


@app.route("/api/admin/submissions")
@login_required
def admin_submissions():
    conn   = get_db()
    status = request.args.get("status", "")
    search = request.args.get("search", "").strip()

    def build_query(table, cols):
        sql    = f"SELECT * FROM {table}"
        params = []
        conds  = []
        if status:
            conds.append("status = ?")
            params.append(status)
        if search:
            like = [f"{c} LIKE ?" for c in cols]
            conds.append("(" + " OR ".join(like) + ")")
            params.extend([f"%{search}%"] * len(cols))
        if conds:
            sql += " WHERE " + " AND ".join(conds)
        sql += " ORDER BY created_at DESC"
        return conn.execute(sql, params).fetchall()

    contact  = rows_to_list(build_query("contact_submissions",
                             ["name","email","subject","message"]))
    member   = rows_to_list(build_query("membership_submissions",
                             ["name","email","tier"]))
    collab   = rows_to_list(build_query("collaboration_submissions",
                             ["organisation","contact_name","email","collab_type","message"]))
    workshop = rows_to_list(build_query("workshop_submissions",
                             ["name","email","workshop","message"]))
    creds    = rows_to_list(build_query("valid_credentials",
                             ["name","credential_id","type"]))
    conn.close()

    return jsonify({
        "contact":       contact,
        "membership":    member,
        "collaboration": collab,
        "workshop":      workshop,
        "credentials":   creds,
        "totals": {
            "contact":       len(contact),
            "membership":    len(member),
            "collaboration": len(collab),
            "workshop":      len(workshop),
            "credentials":   len(creds),
            "all":           len(contact) + len(member) + len(collab) + len(workshop),
        }
    })


@app.route("/api/admin/stats")
@login_required
def admin_stats():
    conn = get_db()

    def count(table, status=None):
        if status:
            return conn.execute(f"SELECT COUNT(*) FROM {table} WHERE status=?",
                                (status,)).fetchone()[0]
        return conn.execute(f"SELECT COUNT(*) FROM {table}").fetchone()[0]

    stats = {
        "contact":       {"total": count("contact_submissions"),
                          "new":   count("contact_submissions", "new"),
                          "read":  count("contact_submissions", "read"),
                          "archived": count("contact_submissions", "archived")},
        "membership":    {"total": count("membership_submissions"),
                          "new":   count("membership_submissions", "new"),
                          "read":  count("membership_submissions", "read"),
                          "archived": count("membership_submissions", "archived")},
        "collaboration": {"total": count("collaboration_submissions"),
                          "new":   count("collaboration_submissions", "new"),
                          "read":  count("collaboration_submissions", "read"),
                          "archived": count("collaboration_submissions", "archived")},
        "workshop":      {"total": count("workshop_submissions"),
                          "new":   count("workshop_submissions", "new"),
                          "read":  count("workshop_submissions", "read"),
                          "archived": count("workshop_submissions", "archived")},
    }
    stats["all_new"] = (stats["contact"]["new"] +
                        stats["membership"]["new"] +
                        stats["collaboration"]["new"] +
                        stats["workshop"]["new"])
    
    stats["credentials"] = count("valid_credentials")
    
    conn.close()
    return jsonify(stats)


# ─── Admin API – update status ─────────────────────────────────────────────────
TABLE_MAP = {
    "contact":       "contact_submissions",
    "membership":    "membership_submissions",
    "collaboration": "collaboration_submissions",
    "workshop":      "workshop_submissions",
}

@app.route("/api/admin/submissions/<form_type>/<int:submission_id>/status",
           methods=["PATCH"])
@login_required
def update_status(form_type, submission_id):
    if form_type not in TABLE_MAP:
        return jsonify({"error": "Unknown form type"}), 400
    d      = request.get_json(silent=True) or {}
    status = d.get("status")
    if status not in ("new", "read", "archived"):
        return jsonify({"error": "Invalid status"}), 400

    conn  = get_db()
    table = TABLE_MAP[form_type]
    conn.execute(f"UPDATE {table} SET status=? WHERE id=?", (status, submission_id))
    conn.commit()
    conn.close()
    return jsonify({"success": True})


# ─── Admin API – delete ────────────────────────────────────────────────────────
@app.route("/api/admin/submissions/<form_type>/<int:submission_id>",
           methods=["DELETE"])
@login_required
def delete_submission(form_type, submission_id):
    if form_type not in TABLE_MAP:
        return jsonify({"error": "Unknown form type"}), 400
    conn  = get_db()
    table = TABLE_MAP[form_type]
    conn.execute(f"DELETE FROM {table} WHERE id=?", (submission_id,))
    conn.commit()
    conn.close()
    return jsonify({"success": True})


# ─── Admin API – Credentials ───────────────────────────────────────────────────
@app.route("/api/admin/credentials", methods=["GET"])
@login_required
def admin_list_credentials():
    conn = get_db()
    rows = conn.execute("SELECT * FROM valid_credentials ORDER BY created_at DESC").fetchall()
    conn.close()
    return jsonify({"credentials": rows_to_list(rows)})

@app.route("/api/admin/credentials", methods=["POST"])
@login_required
def admin_add_credential():
    d = request.get_json(silent=True) or {}
    req = ["name", "credential_id", "type"]
    if not all(d.get(f) for f in req):
        return jsonify({"error": "Missing required fields"}), 400
    
    conn = get_db()
    try:
        conn.execute(
            "INSERT INTO valid_credentials (name, credential_id, type, status) VALUES (?, ?, ?, ?)",
            (d["name"], d["credential_id"], d["type"], d.get("status", "Active"))
        )
        conn.commit()
    except sqlite3.IntegrityError:
        return jsonify({"error": "Credential ID already exists"}), 400
    finally:
        conn.close()
    return jsonify({"success": True})

@app.route("/api/admin/credentials/<int:cred_id>", methods=["DELETE"])
@login_required
def admin_delete_credential(cred_id):
    conn = get_db()
    conn.execute("DELETE FROM valid_credentials WHERE id=?", (cred_id,))
    conn.commit()
    conn.close()
    return jsonify({"success": True})

@app.route("/api/admin/credentials/<int:cred_id>/status", methods=["PATCH"])
@login_required
def admin_update_credential_status(cred_id):
    d = request.get_json(silent=True) or {}
    status = d.get("status")
    if status not in ("Active", "Revoked"):
        return jsonify({"error": "Invalid status"}), 400
    conn = get_db()
    conn.execute("UPDATE valid_credentials SET status=? WHERE id=?", (status, cred_id))
    conn.commit()
    conn.close()
    return jsonify({"success": True})


# ─── Admin API – Content Management ──────────────────────────────────────────
@app.route("/api/admin/content", methods=["GET"])
@login_required
def admin_list_content():
    conn = get_db()
    rows = conn.execute("SELECT * FROM site_content ORDER BY page_id, section_id").fetchall()
    conn.close()
    return jsonify({"content": rows_to_list(rows)})

@app.route("/api/admin/content", methods=["PATCH"])
@login_required
def admin_update_content():
    d = request.get_json(silent=True) or {}
    updates = d.get("updates", [])
    if not updates:
        return jsonify({"error": "No updates provided"}), 400
    
    conn = get_db()
    for update in updates:
        conn.execute(
            "UPDATE site_content SET content=?, updated_at=datetime('now') WHERE id=?",
            (update.get("content"), update.get("id"))
        )
    conn.commit()
    conn.close()
    return jsonify({"success": True})


# ─── Serve HTML (Dynamic Rendering) ───────────────────────────────────────────
@app.route("/")
@app.route("/<page>.html")
def render_page(page="index"):
    conn = get_db()
    # Fetch content for this specific page, plus global content if needed
    rows = conn.execute("SELECT section_id, content FROM site_content WHERE page_id=?", (page,)).fetchall()
    conn.close()
    
    # Convert DB rows into a dictionary mapping: section_id -> content
    content_dict = {row["section_id"]: row["content"] for row in rows}
    
    # Render the template and pass the content dictionary
    try:
        return render_template(f"{page}.html", content=content_dict)
    except Exception:
        # If template not found (e.g., they asked for a non-existent page)
        abort(404)

@app.route("/uploads/<filename>")
@login_required
def download_file(filename):
    return send_from_directory(app.config['UPLOAD_FOLDER'], filename)

@app.route("/admin")
def admin_page():
    return render_template("admin.html")


# ─── Entry point ─────────────────────────────────────────────────────────────
if __name__ == "__main__":
    import socket as _sock
    _s = _sock.socket()
    _s.bind(('', 0))
    PORT = _s.getsockname()[1]
    _s.close()
    init_db()
    print("[OK] Database initialised at:", DB_PATH)
    print("[*]  Server running at http://localhost:" + str(PORT))
    print("[*]  Admin panel:  http://localhost:" + str(PORT) + "/admin")
    app.run(debug=False, port=PORT, host='127.0.0.1')
