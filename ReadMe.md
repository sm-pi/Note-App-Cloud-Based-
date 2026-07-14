# ☁️ Cloud Notes — Vulnerable Web Application (Security Testing Lab)

A deliberately vulnerable PHP/MariaDB note-taking application built for **hands-on web
security training and vulnerability testing**. It reproduces real-world flaws —
SQL Injection, Stored & Reflected XSS, and CSRF — in a small, readable codebase,
with built-in security telemetry logging for detection/analysis experiments.

> ⚠️ **Warning:** This application is intentionally insecure. Run it **only** in an
> isolated, closed lab environment (local VM / localhost). **Never** deploy it on a
> public server or any network reachable by untrusted parties.

---

## 📖 Overview

Cloud Notes is a simple multi-user notes app: users register, log in, create notes,
submit feedback, search a directory, and update their account email. Each feature
intentionally contains one or more classic vulnerabilities so learners can discover,
exploit, and then remediate them.

An integrated telemetry component (`telemetry.php`) writes structured security events
to `security_telemetry.log`, making the app suitable for building and testing
attack-detection tooling (e.g. an RL-based or rule-based monitoring agent).

---

## ✨ Features

- User registration and login
- Personal notes dashboard (create / view notes)
- Feedback submission board
- Search / directory lookup
- Account settings (email update)
- Structured security event logging

---

## 🧩 Tech Stack

| Layer     | Technology              |
|-----------|-------------------------|
| Backend   | PHP                     |
| Database  | MySQL / MariaDB         |
| Frontend  | HTML + CSS              |
| Server    | Apache (XAMPP / LAMP)   |

---

## 🐞 Intentional Vulnerabilities

| # | Vulnerability            | Location                         | Type              |
|---|--------------------------|----------------------------------|-------------------|
| 1 | SQL Injection            | `index.php` (login)              | Auth bypass       |
| 1 | SQL Injection            | `signup.php`, `dashboard.php`    | Data manipulation |
| 1 | SQL Injection            | `settings.php` (email update)    | Data manipulation |
| 2 | Reflected XSS            | `reflected_xss.php` (`q` param)  | Client-side       |
| 2 | Stored XSS               | `comment.php` (feedback board)   | Client-side       |
| 3 | CSRF                     | `settings.php` (email change)    | State change      |
| — | Cookie theft / exfil     | `xss/xss-attacker/steal.php`     | Attack support    |
| — | Plaintext passwords      | `setup_cloud_notes.sql`          | Insecure storage  |

These are **by design** for educational purposes.

---

## 📂 Project Structure

```
Note-App-Cloud-Based-/
├── index.php               # Login (SQL-injectable auth)
├── signup.php              # Registration
├── dashboard.php           # Notes workspace
├── comment.php             # Feedback board (Stored XSS)
├── reflected_xss.php       # Search directory (Reflected XSS)
├── settings.php            # Account settings (CSRF + SQLi)
├── login.php               # Secondary lab login (victim/attacker)
├── logout.php              # Session teardown
├── con.php                 # Database connection
├── telemetry.php           # Security event logger
├── security_telemetry.log  # Logged events output
├── setup_cloud_notes.sql   # Database schema + seed data
├── style.css               # Styling
├── comments.txt            # Feedback storage
└── xss/
    └── xss-attacker/
        └── steal.php       # Cookie-catcher endpoint
```

---

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4+ / 8.x
- MySQL or MariaDB
- Apache (XAMPP, LAMP, or similar)

### Steps

1. **Clone into your web root** (e.g. `htdocs/` or `/var/www/html/`):
   ```bash
   git clone https://github.com/sm-pi/Note-App-Cloud-Based-.git
   ```

2. **Create and import the database:**
   ```bash
   mysql -u root -p -e "CREATE DATABASE cloud_notes;"
   mysql -u root -p cloud_notes < setup_cloud_notes.sql
   ```

3. **Configure the DB connection** in `con.php` (host, user, password, database) to
   match your environment.

4. **Start Apache & MySQL** and browse to:
   ```
   http://localhost/Note-App-Cloud-Based-/index.php
   ```

---

## 🧪 Usage

Register a new account, or use the seeded lab accounts (`victim` / `attacker`) via
`login.php`. Then work through each feature to discover and exploit the vulnerabilities.

Suggested learning flow:
1. Bypass authentication via SQL Injection on the login page.
2. Trigger Reflected XSS through the search parameter.
3. Plant a Stored XSS payload on the feedback board.
4. Chain Stored XSS with the cookie-catcher to demonstrate session theft.
5. Forge a cross-site request to change a victim's email (CSRF → account takeover).

Every notable action is recorded in `security_telemetry.log` for detection analysis.

---

## 🛡️ Remediation Reference

Once each flaw is exploited, practice fixing it:

- **SQL Injection** → use prepared statements / parameterised queries; validate input.
- **XSS** → apply context-aware output encoding (`htmlspecialchars`); set a strict CSP.
- **CSRF** → add per-session anti-CSRF tokens; restrict to POST; use `SameSite` cookies.
- **Passwords** → store salted hashes with `password_hash()`.
- **Errors** → disable verbose DB error messages in responses.

---

## 📜 Disclaimer

This project is provided **for educational and authorised security-testing purposes
only**. The author(s) accept no responsibility for misuse. Do not use these techniques
against systems you do not own or lack explicit permission to test. Deploying this
application on a live/public network is strongly discouraged.

---

## 📄 License

For educational use. Add a license of your choice (e.g. MIT) if distributing.
