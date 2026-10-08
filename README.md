# 🏠 EstateDesk — Role-Based Property Manager (Core PHP CRUD)

A clean, dependency-free **Core PHP + MySQL** application that demonstrates secure **authentication**, **role-based access control (Admin / Agent)** and full **CRUD** on real-estate listings — with a responsive HTML/CSS/JavaScript interface.

> Built with no frameworks and no Composer packages, to show solid PHP fundamentals.

## ✨ Features

- **Authentication** — register, login, logout with `password_hash()` / `password_verify()` and session ID regeneration
- **Two roles**
  | Role | Can do |
  |------|--------|
  | **Admin** | View/edit/delete *all* properties, manage users, promote/demote roles, delete users |
  | **Agent** | Create, view, edit and delete *only their own* properties |
- **Property CRUD** — title, location, type, bedrooms, price, status, description
- **Search & filter** by title/location and status, plus live stats (Total / Available / Booked / Sold)
- **Security** — PDO prepared statements (SQL injection safe), output escaping (XSS safe), CSRF tokens on every POST, server-side ownership checks, public sign-up locked to the `agent` role
- **UI/UX** — responsive layout (table collapses to cards on mobile), delete confirmation dialog, password show/hide, price hint (₹98 L / ₹3.25 Cr), auto-dismiss toasts, keyboard-visible focus

## 🧰 Tech Stack

PHP 8+ (PDO) · MySQL · HTML5 · CSS3 · Vanilla JavaScript

## 📁 Structure

```
estatedesk/
├── admin/users.php          # Admin-only user management
├── assets/css/style.css     # Styling
├── assets/js/app.js         # UI behaviour
├── config/database.php      # DB connection (PDO)
├── includes/                # auth helpers, header, footer
├── dashboard.php            # List + search + stats
├── property_form.php        # Create / Update
├── property_delete.php      # Delete (POST + CSRF)
├── login.php  register.php  logout.php
├── database.sql             # Schema
└── install.php              # Seeds demo users + sample data
```

## 🚀 Setup

1. Copy the folder into your web root (`htdocs` for XAMPP, `www` for WAMP/Laragon).
2. Import the schema:
   ```bash
   mysql -u root -p < database.sql
   ```
   (or import `database.sql` through phpMyAdmin)
3. Check DB credentials in `config/database.php`.
4. Seed demo data **once** (then delete `install.php`):
   ```bash
   php install.php
   ```
   or open `http://localhost/estatedesk/install.php` in the browser.
5. Visit `http://localhost/estatedesk/`

## 🔑 Demo Accounts

| Role  | Email | Password |
|-------|-------|----------|
| Admin | admin@estatedesk.test | Admin@123 |
| Agent | agent@estatedesk.test | Agent@123 |

> Change or delete these accounts before any real deployment.

## 🗄️ Database

`users` (id, name, email, password, role) ⟶ `properties` (id, title, location, type, bedrooms, price, status, description, user_id → users.id, timestamps). Deleting a user cascades to their properties.

## 👩‍💻 Author

**Pulimamidi Shirisha** — Full-Stack Web Developer (PHP · Laravel · WordPress)
[GitHub](https://github.com/shirishirisha) · [LinkedIn](https://linkedin.com/in/pulimamidi-shirisha-351453345)
