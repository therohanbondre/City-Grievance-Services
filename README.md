# City Grievance Services (CGS)

A web-based civic complaint management portal that gives citizens a digital channel to submit, track, and follow up on grievances with their local civic authority. An administrator can view all incoming complaints, update their processing status with remarks, and manage the supporting reference data.

> **Built with:** PHP · PostgreSQL · Bootstrap · jQuery<br>
> **Hosting:** Railway (PHP app) · Supabase (PostgreSQL)
> **Status:** Functional prototype

---

## Table of Contents

- [Features](#features)
- [Screenshots](#screenshots)
- [Technology Stack](#technology-stack)
- [Project Architecture](#project-architecture)
- [Database Design](#database-design)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Default Credentials](#default-credentials)
- [Application Workflow](#application-workflow)
- [Known Limitations](#known-limitations)
- [Future Enhancements](#future-enhancements)
- [License](#license)

---

## Features

### Citizen Portal (`/users`)

| Feature | Details |
|---------|---------|
| Registration | Self-registration with full name, email, password, and contact number |
| Login / Logout | Session-based authentication with login audit logging |
| Forgot Password | Password reset by matching registered email and contact number |
| Lodge Complaint | 2-level category selection, complaint type, ward/area, description, optional file attachment |
| Complaint Tracking | Unique complaint number assigned on submission |
| Complaint History | Newest-first list of own complaints with live search, status filtering, result counts, and an empty state |
| Complaint Detail | Full complaint view including all admin remarks, copyable complaint number, and print-friendly details |
| Profile Management | Update name, contact, address, state, country, and pincode |
| Profile Photo | Upload a profile photo (JPG / PNG / GIF) |
| Change Password | Change password after verifying the current one |

### Admin Panel (`/admin`)

| Feature | Details |
|---------|---------|
| Secure Login | Separate admin session, independent of citizen sessions |
| Complaint Management | Three filtered views — Not Processed, In Process, Closed |
| Status Updates | Set complaint status to *In Process* or *Closed* with a written remark |
| Remark History | Full history of all admin remarks per complaint preserved |
| User Management | View all registered citizens, inspect profiles, delete accounts |
| Login Audit Log | Full record of all login events (successful and failed) with IP and timestamp |
| Category CRUD | Create, edit, and delete complaint categories |
| Subcategory CRUD | Create, edit, and delete subcategories linked to a parent category |
| Ward / Area CRUD | Create, edit, and delete ward/area names used in the complaint form |
| Change Password | Admin can change their own password after verifying the current one |

---

## Screenshots

> Add screenshots to a `/screenshots` folder and update the paths below.

| Page | Preview |
|------|---------|
| Landing Page | *(add screenshot)* |
| User Registration | *(add screenshot)* |
| User Login | *(add screenshot)* |
| User Dashboard | *(add screenshot)* |
| Lodge Complaint Form | *(add screenshot)* |
| Complaint History | *(add screenshot)* |
| Complaint Detail & Remarks | *(add screenshot)* |
| Admin Complaint List | *(add screenshot)* |
| Admin Update Complaint | *(add screenshot)* |
| Admin Manage Users | *(add screenshot)* |
| Admin Category Management | *(add screenshot)* |

---

## Technology Stack

| Layer | Technology | Version / Notes |
|-------|-----------|-----------------|
| Backend | PHP | 8.3 (procedural, PDO PostgreSQL) |
| Database | Supabase PostgreSQL | PostgreSQL |
| Web Server | Apache | Railway Docker service |
| Frontend | Bootstrap | 2 (admin panel) · 3 (user portal) |
| Frontend | jQuery | 1.8 / 1.9 |
| Frontend | jQuery DataTables | Client-side sort, search, pagination |
| Frontend | Font Awesome | 4.x — icon fonts |
| Frontend | jQuery Backstretch | Full-screen login background |
| Frontend | Chart.js | Included (dashboard widget) |
| Hosting | Railway | PHP frontend and backend in one service |
| Development | PHP + PostgreSQL | Local PHP server or Docker |

There is no Composer or Node dependency manifest, application build step, migration runner, or application-specific automated test suite. The Bootstrap, jQuery, and other frontend libraries are checked into the repository. `Dockerfile` provides the Railway runtime, and `supabase/schema.sql` creates the PostgreSQL tables.

---

## Project Architecture

```
Browser
  │
  ├── User Portal (Bootstrap 3 · jQuery)
  │     AJAX: email check, subcategory dropdown
  │
  └── Admin Panel (Bootstrap 2 · jQuery DataTables)
        Popup windows: update complaint, view user profile
  │
  ▼
Apache (Railway)
  │
  ├── users/*.php   — citizen-facing pages
   │     └── includes/config.php  ← PDO connection via database.php
  │
  └── admin/*.php   — administrator pages
            └── include/config.php   ← PDO connection via database.php
  │
   ▼
Supabase PostgreSQL — database: postgres
  │
  ├── users              (citizen accounts)
  ├── admin              (single admin account)
  ├── tblcomplaints      (all filed complaints)
  ├── complaintremark    (admin remarks per complaint)
  ├── category           (complaint categories)
  ├── subcategory        (sub-categories linked to category)
  ├── state              (ward / area names)
  └── userlog            (login audit trail)
```

---

## Database Design

Database name: **`postgres`** (Supabase default)

The deployable PostgreSQL schema is [`supabase/schema.sql`](supabase/schema.sql). `cms.sql` is the legacy MySQL/MariaDB dump for local reference; it cannot be imported directly into Supabase. The Supabase schema creates tables only and does not migrate existing MySQL records or seed demo accounts.

### Entity Relationships

```
category (id)
    │
    │ 1 : N
    ▼
subcategory (categoryid → category.id)


users (id)
    │
    │ 1 : N
    ▼
tblcomplaints (userId → users.id)
    │
    │ 1 : N
    ▼
complaintremark (complaintNumber → tblcomplaints.complaintNumber)
```

> Note: Relationships are application-enforced. No foreign key constraints are defined at the database level.

### Table Summary

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `admin` | Administrator account | `id`, `username`, `password` |
| `users` | Citizen accounts | `id`, `fullName`, `userEmail`, `password`, `contactNo`, `status` |
| `tblcomplaints` | Filed complaints | `complaintNumber` (PK), `userId`, `category`, `subcategory`, `status` |
| `complaintremark` | Admin remarks history | `id`, `complaintNumber`, `status`, `remark`, `remarkDate` |
| `category` | Complaint categories | `id`, `categoryName`, `categoryDescription` |
| `subcategory` | Sub-categories | `id`, `categoryid`, `subcategory` |
| `state` | Ward / area names | `id`, `stateName` |
| `userlog` | Login audit log | `id`, `uid`, `username`, `userip`, `loginTime`, `logout`, `status` |

### Complaint Status Lifecycle

```
NULL  ──►  "in process"  ──►  "closed"
 │                              │
 │   (set by admin via          │
 │    updatecomplaint.php)       │
 │                              │
 └──────── visible to ─────────►│
           citizen at all times
```

---

## Project Structure

```
complaint/
├── index.html                    ← Public landing page (Bootstrap carousel)
├── cms.sql                       ← Legacy MySQL schema + demo seed data
├── supabase/schema.sql           ← Supabase PostgreSQL schema
├── database.php                  ← Shared PDO PostgreSQL connection and query helpers
├── Dockerfile                    ← Railway PHP/Apache runtime
├── railway.toml                  ← Railway build and health-check settings
├── .env.example                  ← Supabase environment-variable template
├── .gitignore
├── README.md
├── CONTRIBUTING.md
├── LICENSE
│
├── css/                          ← Landing page styles (Bootstrap 3)
├── js/                           ← Landing page scripts (jQuery, Bootstrap)
├── fonts/                        ← Glyphicon web fonts
├── img/                          ← Landing page carousel images
│
├── users/                        ← Citizen portal
│   ├── index.php                 ← Login + forgot-password
│   ├── registration.php          ← Self-registration
│   ├── dashboard.php             ← Complaint count summary
│   ├── register-complaint.php    ← File a new complaint
│   ├── complaint-history.php     ← List all own complaints
│   ├── complaint-details.php     ← Single complaint + admin remarks
│   ├── profile.php               ← View / edit profile
│   ├── update-image.php          ← Upload profile photo
│   ├── change-password.php       ← Change password
│   ├── logout.php                ← Clear session + update audit log
│   ├── check_availability.php    ← AJAX: email uniqueness check
│   ├── getsubcat.php             ← AJAX: subcategories by category
│   ├── complaintdocs/            ← Uploaded complaint attachments (git-ignored)
│   ├── userimages/               ← Uploaded profile photos (git-ignored)
│   ├── assets/                   ← CSS, JS, font-awesome
│   └── includes/
│       ├── config.php            ← Runtime config (created from example in Docker)
│       ├── config.example.php    ← PostgreSQL config template
│       ├── header.php
│       ├── sidebar.php
│       └── footer.php
│
└── admin/                        ← Administrator panel
    ├── index.php                 ← Admin login
    ├── change-password.php       ← Change admin password
    ├── logout.php                ← Clear admin session
    ├── notprocess-complaint.php  ← Complaints not yet actioned
    ├── inprocess-complaint.php   ← Complaints in progress
    ├── closed-complaint.php      ← Resolved complaints
    ├── complaint-details.php     ← Full complaint view + action
    ├── updatecomplaint.php       ← Popup: set status + add remark
    ├── manage-users.php          ← List / delete citizen accounts
    ├── userprofile.php           ← Popup: view citizen profile
    ├── user-logs.php             ← Login audit log
    ├── category.php              ← Add / list / delete categories
    ├── edit-category.php         ← Edit a category
    ├── subcategory.php           ← Add / list / delete subcategories
    ├── edit-subcategory.php      ← Edit a subcategory
    ├── state.php                 ← Add / list / delete wards
    ├── edit-state.php            ← Edit a ward
    ├── get_subcat.php            ← AJAX: subcategory options
    ├── bootstrap/                ← Bootstrap 2 CSS + JS
    ├── css/                      ← Admin theme CSS
    ├── images/icons/             ← Font Awesome icons
    ├── scripts/                  ← jQuery, jQuery UI, DataTables, Flot
    └── include/
      ├── config.php            ← Runtime config (created from example in Docker)
      ├── config.example.php    ← PostgreSQL config template
        ├── header.php
        ├── sidebar.php
        └── footer.php
```

---

## Installation

### Prerequisites

- PHP 8.3 with `pdo_pgsql` and sessions enabled
- A Supabase project, or another PostgreSQL 14+ server for local development
- Apache with PHP support, or another PHP-capable web server
- A modern web browser

Railway builds the included Dockerfile. The application has no Composer/npm install or frontend build command.

### Local setup

1. Create a Supabase project and run `supabase/schema.sql` in its SQL Editor. This creates the tables but does not copy records from the legacy MySQL database.
2. Copy `admin/include/config.example.php` to `admin/include/config.php` and `users/includes/config.example.php` to `users/includes/config.php`.
3. Copy `.env.example` to `.env` and fill in the PostgreSQL connection URL or connection parts from Supabase Database Settings. `database.php` loads this local file; process environment variables take precedence. Set `SUPABASE_DB_SSLMODE=require` for hosted databases.
4. `.env` is git-ignored and blocked from Apache requests. Railway should receive these values through its private service-variable settings; `.env` is excluded from the Docker image. The Supabase API keys are not currently used by the PHP app's database integration.
5. Create the initial administrator in Supabase. The current login code stores MD5 password hashes; this legacy behavior should be replaced before public production use.
6. Ensure `users/complaintdocs/` and `users/userimages/` are writable. For local development, use Apache or another PHP server with the project root as its document root.
7. Open the application at `/`, `/users/`, or `/admin/` on your local server.

### Railway deployment

1. Create a Supabase project and run `supabase/schema.sql` in the Supabase SQL Editor. The schema is empty by design; export and import existing MySQL data separately after reviewing it. `cms.sql` is MySQL-specific and cannot be imported directly.
2. Create the initial admin account and add any required categories/areas. The current admin and citizen login flows use MD5 passwords; resolve this security limitation before making the portal public.
3. Create a Railway project from this GitHub repository. Railway uses `Dockerfile` and `railway.toml` to build the PHP/Apache service and bind Apache to Railway's `PORT`.
4. Add `SUPABASE_DB_URL` to the Railway service variables using the PostgreSQL connection string from Supabase. Prefer the Supabase connection pooler if the direct database host is not reachable from Railway. Do not commit the URL or password.
5. Attach persistent Railway volumes at `/var/www/html/users/complaintdocs` and `/var/www/html/users/userimages`; otherwise complaint files and profile photos will be lost on redeploy/restart. Back up these volumes separately from the Supabase database.
6. Enable a Railway public domain and HTTPS, then verify the citizen and admin sign-in, complaint creation, status updates, remark history, uploads, and logs.

The project does not currently provide automated migration tooling for data, schema upgrades, or file storage. Configure encrypted backups and test restoration before relying on production data.

This is still a prototype application. Before exposing it publicly, address the documented MD5 password storage, SQL injection, CSRF, and upload-validation limitations. The Railway image blocks PHP endpoints under bundled admin plugin assets and excludes SQL dumps, but that does not replace application-level security work.

Before public release, note that the repository documents significant prototype limitations: MD5 password hashes, SQL built by string concatenation, missing CSRF protections, unsafe complaint upload validation, and an unverified password-reset flow. A strong hosting configuration alone does not resolve these application issues; address them before exposing the application to untrusted internet traffic.

### Vercel

This application is **not deployable to Vercel as-is**. It is a multi-page PHP/PostgreSQL application; Vercel does not list PHP as an official runtime. Its runtimes documentation lists PHP through the community-maintained `vercel-php` runtime. Deploying this app there would require an adaptation and validation project, not just connecting this folder to Vercel:

1. First resolve the production security issues described above. In particular, remove or deny access to PHP demos and upload handlers shipped under `admin/assets/plugins/`; never publish that plugin tree as executable endpoints.
2. Use Supabase PostgreSQL or another managed PostgreSQL provider reachable from Vercel Functions. Apply `supabase/schema.sql`, migrate data separately, remove demo accounts/data, and configure a least-privilege database role. Vercel does not provide this app's database.
3. Adapt PHP request routing to the `vercel-php` community runtime and verify every existing route, relative include, asset URL, session/auth flow, and redirect in preview deployments. The community runtime's example expects PHP handlers under `api/`; this app currently has independent scripts in `users/` and `admin/`, and has no Vercel adapter.
4. Replace filesystem-backed PHP sessions with a shared, durable session store compatible with the chosen deployment. Vercel Function instances are not a durable session filesystem.
5. Move complaint attachments and profile images to persistent object storage and change upload/download code to use it. The current app writes uploads under `users/complaintdocs/` and `users/userimages/`; Vercel deployment filesystems are not a persistent upload store.
6. Configure `SUPABASE_DB_URL`, plus credentials for any new session/object-storage services, as encrypted Vercel environment variables for Preview and Production. Do not deploy until those integrations and production safeguards are implemented.
7. Deploy to a Vercel Preview URL first and run the complete production smoke checklist below before promoting to Production. Confirm database connectivity, persistent sessions across separate function invocations, upload/download behavior, auth isolation, and error logging.

Vercel references: [supported runtimes](https://vercel.com/docs/functions/runtimes) (PHP is listed as a community runtime) and the [`vercel-php` community runtime](https://github.com/vercel-community/php). Runtime behavior and compatibility should be checked against their current documentation before implementation. Do not point a static Vercel deployment at the unmodified `complaint/` directory: static hosting does not execute these PHP pages, and publishing PHP source or bundled plugin demo endpoints is unsafe. If Vercel is mandatory, budget for the adaptations above; otherwise, deploy the current application to a conventional PHP-capable host using the preceding production steps.

---

## Configuration

Both `admin/include/config.php` and `users/includes/config.php` use `database.php` to read PostgreSQL settings from process environment variables or the local `.env` file. Process variables take precedence. Railway should use its private service-variable settings; the Docker image excludes `.env`. Railway creates the runtime config files from the tracked templates. For local development, copy the templates to the ignored `config.php` paths:

| Variable | Default | Description |
|----------|---------|-------------|
| `SUPABASE_DB_URL` | *(unset)* | Preferred PostgreSQL connection URL; Railway should set this |
| `SUPABASE_DB_HOST` | `localhost` | PostgreSQL host when no URL is set |
| `SUPABASE_DB_PORT` | `5432` | PostgreSQL port (Supabase pooler may use a different port) |
| `SUPABASE_DB_NAME` | `postgres` | PostgreSQL database name |
| `SUPABASE_DB_USER` | `postgres` | PostgreSQL username |
| `SUPABASE_DB_PASSWORD` | *(empty)* | PostgreSQL password |
| `SUPABASE_DB_SSLMODE` | `require` | TLS mode for the PostgreSQL connection |

`DATABASE_URL` is accepted as a fallback for `SUPABASE_DB_URL`. PHP must have `pdo_pgsql` enabled. Keep environment values consistent for the admin and citizen portals because both use the same database. Production must set a real connection URL; do not rely on local defaults.

---

## Default Credentials

These are credentials from the legacy MySQL `cms.sql` demo seed only. The Supabase schema does not seed accounts.

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Test User | `test@gmail.com` | `123` |

> **Legacy demo only:** `cms.sql` seeds the admin (`admin` / `admin123`) and test citizen (`test@gmail.com` / `123`). These weak accounts are not created by `supabase/schema.sql` and must never remain enabled on a public deployment. Current login code still expects unsalted MD5 hashes; changing the password through the UI does not upgrade that storage scheme. See [Known Limitations](#known-limitations).

---

## Application Workflow

```
1. Citizen visits landing page  →  index.html

2. New citizen registers        →  users/registration.php
   (name, email, password, contact)

3. Citizen logs in              →  users/index.php
   Session set, login event logged in userlog

4. Citizen files a complaint    →  users/register-complaint.php
   Selects category + subcategory (dynamic AJAX dropdown)
   Chooses type, ward/area, enters location + details
   Optionally attaches a file
   → complaint saved with status = NULL
   → unique complaint number shown via alert

5. Admin logs in                →  admin/index.php
   Separate session (alogin)

6. Admin reviews complaints     →  admin/notprocess-complaint.php
   Views full detail            →  admin/complaint-details.php

7. Admin takes action           →  admin/updatecomplaint.php (popup)
   Selects: "in process" or "closed"
   Writes a remark
   → remark saved to complaintremark table
   → complaint status updated in tblcomplaints

8. Citizen checks status        →  users/complaint-history.php
   Searches / filters complaints
   Views full detail + remarks  →  users/complaint-details.php
   Copies complaint number or prints details
```

### Production smoke checks

- Confirm `SUPABASE_DB_URL` is visible to the PHP runtime and both portals connect to the intended database; do not expose `phpinfo()` or credentials.
- Register a non-demo citizen, sign in/out, and verify the session redirects without downgrading an HTTPS connection.
- Submit a complaint with and without an attachment; confirm a unique complaint number is assigned and the upload is stored.
- Sign in as the administrator, update the complaint to *In Process* and then *Closed* with remarks, and verify the citizen sees the status and complete remark history.
- On the citizen complaint history page, test text search, each status filter, clear filters, no-result behavior, and the narrow/mobile layout. On details, test copy (HTTPS is required by most browsers for clipboard access) and browser printing.
- Check error logs, verify uploads cannot execute server-side code, and restore a backup into a separate test database before relying on production backups.

---

## Known Limitations

These are existing architectural decisions in the current codebase. They do not affect local/demo usage but should be addressed before any production deployment.

| Area | Issue |
|------|-------|
| Password security | Passwords stored as unsalted MD5 hashes. Should be replaced with `password_hash()` / `password_verify()` (bcrypt) |
| SQL injection | All database queries use string concatenation. Should be replaced with prepared statements |
| CSRF protection | No CSRF tokens on forms. All state-changing requests are vulnerable to cross-site forgery |
| File upload validation | Complaint attachments have no file-type restriction. Any file including PHP scripts can be uploaded |
| Password reset | Forgot-password resets without email verification — only requires email + contact number |
| Single admin | Only one admin account is supported. No department routing or multi-admin support |
| No notifications | No email notifications are sent at any stage of the complaint lifecycle |
| Session security | Sessions are not regenerated on login (`session_regenerate_id()` not called) |

---

## Future Enhancements

- [ ] Replace MD5 with bcrypt (`password_hash` / `password_verify`)
- [ ] Rewrite all queries with prepared statements (eliminate SQL injection)
- [ ] Add CSRF token validation on all forms
- [ ] Email notifications to citizen on complaint status change
- [ ] Token-based password reset via email link
- [ ] Admin dashboard with complaint analytics and charts
- [ ] Department assignment — route complaints to responsible departments
- [ ] Multi-admin support with role-based access control
- [ ] Report generation and CSV/PDF export
- [x] Citizen complaint search and status filtering
- [ ] Mobile-responsive admin panel (currently Bootstrap 2)
- [ ] Captcha on login and registration forms
- [ ] Citizen feedback / satisfaction rating after complaint closure

---

## License

This project is licensed under the [MIT License](LICENSE).

&copy; 2023 Tejas Bhalekar and Group. All rights reserved.
