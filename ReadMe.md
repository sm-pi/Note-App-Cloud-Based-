# Cloud Notes (Intentionally Vulnerable Lab)

A small PHP/MySQL note-taking web app designed for **authorized, local security training**. The app intentionally contains insecure SQL query patterns so you can practice identifying and fixing SQL injection flaws in a safe environment.

## Project Summary (copy-ready)
Cloud Notes is a lightweight note-taking web app built with PHP and MySQL. It is intentionally vulnerable to SQL injection for educational and defensive testing purposes, helping learners recognize insecure query patterns and practice mitigation like prepared statements and input validation. **Use only in a local, isolated lab.**

## Why this exists
- Demonstrate common SQL injection patterns in a simple, readable codebase.
- Provide a hands-on lab for learning how to detect and fix insecure queries.
- Support classroom or self-study exercises on web security basics.

## Features
- Login page with session handling
- Dashboard to view and add notes
- Seeded database with sample users/notes

## Tech Stack
- PHP (server-side)
- MySQL (database)
- HTML/CSS (UI)

## Repository Contents
- `setup_cloud_notes.sql` — database schema and seed data (included in the repo root)

## Setup (local only)
1. Create the database and tables:
   ```bash
   mysql -u <your_user> -p < ./setup_cloud_notes.sql
   ```
   (The `setup_cloud_notes.sql` file is included in the repository root. If you move it elsewhere, update the path in the command.)
2. Update the four arguments passed to `mysqli_connect` in `con.php`: host, username, password, and database name (e.g., `localhost`, `cloud_notes_user`, `<password>`, `cloud_notes`).
3. Run a local PHP server:
   ```bash
   php -S localhost:8000 -t .
   ```
   Keep this **local only**. The `con.php` file contains credentials and should not be exposed to the public internet. If you ever deploy beyond local use, move secrets outside the web root.
4. Open `http://localhost:8000/index.php` in your browser.

## Safe & Ethical Use
- Use this project **only** in an authorized, local lab environment.
- Do **not** deploy to the public internet.
- Do **not** use this against real systems or data you don’t own.

## Vulnerability Notes (high-level)
The codebase intentionally includes insecure SQL string concatenation in the login flow and note creation flow. This is for defensive learning only. For remediation practice, replace vulnerable queries with prepared statements and parameterized inputs.

---

If you need a public project description or presentation blurb, you can use the **Project Summary** section above verbatim.
