<?php
/**
 * Shared Database Connection
 * Requirements: PDO/mysqli + prepared statements 
 */

$host    = 'localhost';
$db      = 'food_ordering_system'; // Must be identical for all teammates
$user    = 'root';               // Default XAMPP username
$pass    = '';                   // Default XAMPP password (empty)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    // Throw exceptions on SQL errors for easier debugging
    // and to avoid silent failures during development.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Return data as associative arrays (e.g., $row['name'])
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Disable emulation to use real prepared statements
    // this helps prevent SQL injection and ensures native parameter handling.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If the connection fails, stop the script and show the error
    die("Database connection failed: " . $e->getMessage());
}