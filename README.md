# City Grievance Services (CGS)

A web-based civic complaint management portal that gives citizens a digital channel to submit, track, and follow up on grievances with their local civic authority. An administrator can view all incoming complaints, update their processing status with remarks, and manage the supporting reference data.

> **Built with:** PHP · MySQL · Bootstrap · jQuery  
> **Environment:** XAMPP (Apache + MySQL)  
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
| Complaint History | Tabular view of all own complaints with colour-coded status badges |
| Complaint Detail | Full complaint view including all admin remarks and status history |
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
| Backend | PHP | 8.x (procedural) |
| Database | MySQL / MariaDB | 10.4+ |
| Web Server | Apache | Via XAMPP |
| Frontend | Bootstrap | 2 (admin panel) · 3 (user portal) |
| Frontend | jQuery | 1.8 / 1.9 |
| Frontend | jQuery DataTables | Client-side sort, search, pagination |
| Frontend | Font Awesome | 4.x — icon fonts |
| Frontend | jQuery Backstretch | Full-screen login background |
| Frontend | Chart.js | Included (dashboard widget) |
| Development | XAMPP | Local Apache + MySQL stack |

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
Apache (XAMPP)
  │
  ├── users/*.php   — citizen-facing pages
  │     └── includes/config.php  ← DB connection (reads from env)
  │
  └── admin/*.php   — administrator pages
        └── include/config.php   ← DB connection (reads from env)
  │
  ▼
MySQL — database: cms
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

Database name: **`cms`**

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
├── cms.sql                       ← Full database schema + seed data
├── .env.example                  ← Environment variable template (copy → .env)
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
│       ├── config.php            ← DB connection (excluded from git)
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
        ├── config.php            ← DB connection (excluded from git)
        ├── header.php
        ├── sidebar.php
        └── footer.php
```

---

## Installation

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP 8.x)
- A modern web browser

### Step-by-step Setup

**1. Clone the repository**

```bash
git clone https://github.com/your-username/city-grievance-services.git
```

**2. Copy files to XAMPP**

Copy the `complaint/` folder into your XAMPP `htdocs` directory:

```
Windows:  C:\xampp\htdocs\complaint\
Linux:    /opt/lampp/htdocs/complaint/
macOS:    /Applications/XAMPP/htdocs/complaint/
```

**3. Start XAMPP services**

Open the XAMPP Control Panel and start both **Apache** and **MySQL**.

**4. Create the database**

- Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin) in your browser
- Click **New** → enter `cms` as the database name → click **Create**
- Select the `cms` database from the left panel
- Click **Import** → choose `complaint/cms.sql` → click **Go**

**5. Configure the environment**

```bash
# From inside the complaint/ directory
copy .env.example .env        # Windows
cp .env.example .env          # Linux / macOS
```

Open `.env` and set your database credentials. For a default XAMPP installation no changes are needed:

```
DB_SERVER=localhost
DB_USER=root
DB_PASS=
DB_NAME=cms
```

> **Important:** The application reads these values via `getenv()`. For Apache/XAMPP to expose them, either set them via `SetEnv` in your virtual host / `.htaccess`, or edit `admin/include/config.php` and `users/includes/config.php` directly with your values after cloning (these files are excluded from git).

**6. Verify upload directory permissions**

Ensure the following directories are writable by the web server:

```
complaint/users/complaintdocs/
complaint/users/userimages/
```

**7. Open the application**

| URL | Purpose |
|-----|---------|
| `http://localhost/complaint/` | Public landing page |
| `http://localhost/complaint/users/` | Citizen login |
| `http://localhost/complaint/users/registration.php` | Citizen registration |
| `http://localhost/complaint/admin/` | Admin login |

---

## Configuration

All configurable values are managed through environment variables (see `.env.example`).

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_SERVER` | `localhost` | MySQL host |
| `DB_USER` | `root` | MySQL username |
| `DB_PASS` | *(empty)* | MySQL password |
| `DB_NAME` | `cms` | Database name |

If environment variables are not available in your setup, edit the two config files directly after cloning:

- `admin/include/config.php`
- `users/includes/config.php`

Both files are excluded from git tracking (see `.gitignore`) so edits to them will not be accidentally committed.

---

## Default Credentials

These credentials are seeded by `cms.sql`. **Change them immediately after installation.**

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Test User | `test@gmail.com` | `123` |

> **Security note:** Passwords are currently stored as unsalted MD5 hashes. This is a known limitation — see [Known Limitations](#known-limitations).

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
   Views full detail + remarks  →  users/complaint-details.php
```

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
- [ ] Complaint search and advanced filtering for citizens
- [ ] Mobile-responsive admin panel (currently Bootstrap 2)
- [ ] Captcha on login and registration forms
- [ ] Citizen feedback / satisfaction rating after complaint closure

---

## License

This project is licensed under the [MIT License](LICENSE).

&copy; 2023 Tejas Bhalekar and Group. All rights reserved.
