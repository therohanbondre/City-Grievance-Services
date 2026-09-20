# Contributing to City Grievance Services

Thank you for your interest in contributing. This document explains how to get the project running locally, how to submit changes, and what standards to follow.

---

## Table of Contents

- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Project Conventions](#project-conventions)
- [Submitting Changes](#submitting-changes)
- [Reporting Bugs](#reporting-bugs)
- [Security Issues](#security-issues)
- [Code of Conduct](#code-of-conduct)

---

## Getting Started

Before contributing, please:

1. Read the [README](README.md) fully — especially the **Known Limitations** section so you understand the current state of the codebase.
2. Check [open issues](../../issues) to see if the work you want to do is already being tracked.
3. For significant changes (new features, architectural changes), open an issue first to discuss the approach before writing code.

---

## Development Setup

Follow the [Installation](README.md#installation) steps in the README.

After cloning, the two files excluded from git need to be created manually:

```bash
# 1. Create your local config files from the shared template
copy .env.example .env          # Windows
cp .env.example .env            # Linux / macOS

# 2. Copy config template into both locations
#    (edit with your DB credentials if different from XAMPP defaults)
copy admin\include\config.example.php admin\include\config.php    # Windows
copy users\includes\config.example.php users\includes\config.php  # Windows

cp admin/include/config.example.php admin/include/config.php      # Linux / macOS
cp users/includes/config.example.php users/includes/config.php    # Linux / macOS
```

> If `config.example.php` does not exist, copy the content from `.env.example` comments — the config files only need the four `define()` lines and the `mysqli_connect()` call.

Verify the application loads at `http://localhost/complaint/` before making any changes.

---

## Project Conventions

### PHP

- Match the existing procedural style — do not introduce classes, namespaces, or a framework unless that is the explicit goal of the contribution.
- Every protected page must start with the existing session guard pattern:
  ```php
  session_start();
  if (strlen($_SESSION['login']) == 0) {
      header('location:index.php');
      exit();
  }
  ```
- Include `error_reporting(0);` on all user-facing pages (existing convention).
- Output user-supplied data through `htmlentities()` before echoing it to the page.
- Use `mysqli_*` functions — never the removed `mysql_*` API.

### SQL

- New queries should use **prepared statements** (`mysqli_prepare` / `bind_param`). Existing string-concatenated queries are a known issue; do not introduce new ones.
- Column and table names should match the existing `cms` schema exactly (case-sensitive on Linux MySQL).

### HTML / CSS

- The **user portal** uses Bootstrap 3 — stay within Bootstrap 3 classes and conventions.
- The **admin panel** uses Bootstrap 2 — stay within Bootstrap 2 classes and conventions.
- Do not mix Bootstrap versions within a panel.
- Use Font Awesome 4.x icon classes (e.g. `fa fa-user`) — they are already loaded on every page.

### File Uploads

- Any new upload handler must validate file extension against an explicit whitelist.
- Store uploaded files in the existing directories:
  - Complaint attachments → `users/complaintdocs/`
  - Profile photos → `users/userimages/`
- Neither directory is tracked in git (see `.gitignore`).

### Naming

- PHP files: lowercase hyphen-separated (`complaint-details.php`)
- PHP variables: camelCase (`$fullName`, `$complaintNumber`)
- Database columns: match the existing schema — do not rename columns

---

## Submitting Changes

1. **Fork** the repository and create a feature branch from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes. Keep commits focused — one logical change per commit.

3. Write a clear commit message:
   ```
   Short summary (50 chars or fewer)

   Longer explanation of what changed and why, if needed.
   Reference any related issue: Closes #42
   ```

4. **Do not commit:**
   - `.env` or any file containing real credentials
   - `admin/include/config.php` or `users/includes/config.php` (they are in `.gitignore`)
   - Files from `users/complaintdocs/` or `users/userimages/` (except `noimage.png`)
   - `text.txt` or any file containing plaintext passwords

5. Push your branch and open a **Pull Request** against `main`.

6. In the PR description, explain:
   - What the change does
   - How you tested it
   - Any known side effects or limitations

---

## Reporting Bugs

Open a [GitHub Issue](../../issues/new) with:

- A clear title describing the problem
- Steps to reproduce
- What you expected vs. what actually happened
- PHP version, MySQL version, and OS/XAMPP version
- Relevant error messages (check `php_errorlog` and the Apache `error.log`)

---

## Security Issues

**Do not open a public GitHub Issue for security vulnerabilities.**

The [Known Limitations](README.md#known-limitations) section in the README documents several existing security issues (SQL injection, MD5 passwords, missing CSRF protection, unrestricted file upload). These are acknowledged.

If you discover a new security issue not listed there, contact the repository owner directly before disclosing it publicly.

---

## Code of Conduct

Be respectful and constructive. Contributions of all experience levels are welcome. Focus feedback on the code, not the person.
