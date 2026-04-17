# UB Student Database System

A PHP MySQL CRUD application with a login system built for the University of Botswana CSI lab assignment. Allows authorised users to register, log in, and manage student records through a clean web interface.

## Features

- **Authentication** — Register and login with hashed passwords using `password_hash()`
- **Session management** — Protected routes, logout terminates session
- **CRUD operations** — Create, read, update and delete student records
- **Search** — Filter students by name or ID
- **Prepared statements** — All queries use PDO prepared statements to prevent SQL injection
- **Separation of concerns** — All styles in `style.css`, logic in PHP files, no inline styles

## Tech Stack

- PHP 8.x
- MySQL 8.x
- PDO (PHP Data Objects)
- Plain HTML & CSS (no frameworks)

## Project Structure

```
ub-crud/
├── config.php          # Database credentials (not committed)
├── config.example.php  # Template — copy and rename to config.php
├── setup.sql           # Creates database, tables and sample data
├── style.css           # All styles
├── login.php           # Login page
├── register.php        # Registration page
├── manage_database.php # Main CRUD dashboard
└── logout.php          # Session termination
```

## Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/Raymacmillan/ub-student-database.git
cd ub-student-database
```

### 2. Set up the database

```bash
sudo mysql -u root < setup.sql
```

This creates the `lab_db` database, both tables, and inserts 10 sample students.

### 3. Configure your credentials

```bash
cp config.example.php config.php
```

Open `config.php` and fill in your database username and password:

```php
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. Start the server

```bash
php -S localhost:8000
```

### 5. Open in browser

```
http://localhost:8000/login.php
```

Register an account, then log in to access the dashboard.

## Security Notes

- Passwords are hashed with `password_hash()` using PHP's `PASSWORD_DEFAULT` algorithm
- All user input is sanitised with `htmlspecialchars()` before output
- All database queries use PDO prepared statements
- `config.php` is excluded from version control via `.gitignore`

## Author

Ray McMillan Gumbo — University of Botswana, BSc Computer Science