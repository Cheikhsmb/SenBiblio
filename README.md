# Senegal Digital Library — Admin Panel

Simple PHP + MySQL admin UI for managing books, students and borrowings.

## Requirements
- PHP 7.4+ with `pdo` and `pdo_mysql` extensions
- MySQL / MariaDB

## Quick setup (local)
1. Import the database:

```powershell
mysql -u root -p < db.sql
```

2. Verify DB credentials in `config.php` or create an `.env` and update accordingly.
3. Start PHP built-in server (from project root):

```powershell
php -S localhost:8000
```

4. Open your browser: http://localhost:8000/index.php

Default seeded admin credentials (change after first login):
- Email: `admin@senlibrary.edu`
- Password: `admin123`

## Git
Initialize a repo, commit, then push to GitHub:

```bash
git init
git add .
git commit -m "Initial project: add app, .gitignore, README"
# create remote then
git push -u origin main
```

## Notes & Improvements
- Move credentials to `.env` using `vlucas/phpdotenv`.
- Add CSRF tokens for forms and stronger server-side validation.
- Add logging instead of dying on DB errors.
- Consider role-based access checks if more roles are needed.
