# Library Management System

> A modern, secure, and elegant library management system for administering books, members, and loans.

---

## ✨ Key Features

* **📊 Dynamic Dashboard:** Instantly view key statistics (total books, registered members, active loans, overdue items).
* **📚 Catalog Management (Books):** Full CRUD to add, edit, search, and delete titles (with automatic copy stock management).
* **👥 Member Management:** Simple member profiles with search, categories (Member, Student, Senior, Child, Other), phone, address, and automatic loan association.
* **📖 Loan & Return Management:** Record loan transactions with automatic overdue detection and visual highlighting.
* **🛡️ Enhanced Security:** Global CSRF protection on all form submissions and POST-only destructive actions.
* **🎨 Elegant Interface:** Modern dark glassmorphism theme with animated p5.js backgrounds (floating particles and books).
* **🔔 Toast Notifications:** Non-intrusive Bootstrap toast notifications for success/warning/error messages.
* **📱 Responsive Design:** Works seamlessly on desktop and mobile.

---

## 🛠️ Tech Stack

* **Server / Logic:** PHP 7.4+ (requires `pdo` and `pdo_mysql` extensions)
* **Database:** MySQL / MariaDB
* **Design & Animations:** Vanilla CSS, Bootstrap 5.3, Font Awesome 6, p5.js
* **Security:** PHP session authentication, password hashing (`password_hash`), unique CSRF tokens per form.

---

## 🚀 Quick Start Guide

### 📋 Prerequisites
* PHP installed (e.g., via Laragon, XAMPP, or CLI).
* Active MySQL database server.

### 📥 1. Import Database
Create the database and import sample data using the provided dump:
```bash
mysql -u root -p < db.sql
```
*(The script automatically creates the `library_senegal` database and inserts tables plus 94 books and sample users).*

### 🔄 2. Run Migration (Optional - for new phone/address fields)
To add phone and address columns to the members table:
```bash
mysql -u root -p < migrate.sql
```

### 🔑 3. Configuration
Open `config.php` and adjust your MySQL connection if needed:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'library_senegal');
define('DB_USER', 'root'); // Your username
define('DB_PASS', '');     // Your password
```

### 💻 4. Start Local Server
Start PHP's built-in server from the project root:
```bash
php -S localhost:8000
```

### 🌐 5. Access Application
Open your browser to:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🔐 Test Credentials

Use the pre-configured admin account:

* **Email:** `admin@library.local`
* **Password:** `admin123`

---

## 📁 Project Structure

```
Projet PHP/
├── assets/
│   ├── css/style.css       # Design system & Glassmorphism
│   └── js/
│       ├── dashboard.js    # Dashboard canvas animation (p5.js)
│       ├── p5-login.js     # Login canvas animation (p5.js)
│       └── toast.js        # Bootstrap toast notifications
├── partials/
│   └── nav.php             # Shared navigation bar
├── config.php              # PDO connection & security functions (CSRF, Auth)
├── index.php               # Login page
├── dashboard.php           # Main dashboard
├── books.php               # Book management (CRUD)
├── students.php            # Member management (CRUD)
├── borrowings.php          # Loan & return management
├── logout.php              # Clean logout
├── db.sql                  # Database import script
├── migrate.sql             # Migration for phone/address columns
└── README.md               # This file
```

---

## 🔄 Recent Improvements

* **Generalized terminology:** "Étudiants" → "Membres", "Programme" → "Catégorie", "Année d'étude" → "Type d'abonnement"
* **New optional fields:** Phone and address for members (run `migrate.sql` to add columns)
* **Member categories dropdown:** Adhérent, Étudiant, Senior, Enfant, Autre
* **Availability badges:** Green (>1 copy), Yellow (1 copy), Red (0 copies)
* **Overdue highlighting:** Red-tinted rows for overdue loans
* **Toast notifications:** Replaced inline alerts with Bootstrap toasts (auto-dismiss 4s)
* **Dashboard:** Added "Retards" (overdue) stat card
* **Better empty states:** Icons and helpful messages
* **Table enhancements:** Striped rows, hover effects

---

## 🤝 Contribution & Git

To clone and push changes to your own GitHub repo:
```bash
# Initialize repo
git init

# Add files
git add .
git commit -m "Library Management System - generalized & enhanced"

# Link remote (replace URL)
git remote add origin https://github.com/your-username/repo-name.git
git branch -M main
git push -u origin main
```