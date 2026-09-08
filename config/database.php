<?php
/**
 * Database Configuration & PDO Connection
 * 
 * This file establishes a secure PDO connection to the MySQL database.
 * Uses prepared statements for all queries to prevent SQL injection.
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'jollywood_buzz');
define('DB_PORT', 3306);

// PDO options
$pdo_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

// Create PDO connection
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASSWORD,
        $pdo_options
    );
} catch (PDOException $e) {
    // Log error securely (do not expose database details to user)
    error_log('Database Connection Error: ' . $e->getMessage());
    
    // Show user-friendly error
    die('Database connection failed. Please contact the administrator.');
}

?>
