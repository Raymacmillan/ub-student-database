<?php
// ================================================
// Database Configuration — EXAMPLE FILE
// ================================================
// 1. Copy this file: cp config.example.php config.php
// 2. Fill in your actual database credentials
// 3. Never commit config.php to GitHub
// ================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'lab_db');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');

function getDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>