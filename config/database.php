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
    // throw exceptions
    // silent failures
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // return data
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // disable emulation
    // helps prevent
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // if connection
    die("Database connection failed: " . $e->getMessage());
}