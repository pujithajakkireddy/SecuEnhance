<?php
// This file handles the database connection using PDO.

// Database configuration
$host = 'localhost';
$dbname = 'new_app_db'; // Ensure this matches your database name
$user = 'root'; 
$password = ''; 
$port = 3307; // Use 3306 if you didn't change the default port

// Data Source Name (DSN) for PDO
$dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8mb4";

try {
    // Corrected variable name from $conn to $pdo
    $pdo = new PDO($dsn, $user, $password);

    // Set PDO attributes for better error handling and security
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails, display a user-friendly error message and exit
    die("Database connection failed: " . $e->getMessage());
}

// Make the $pdo variable global so it can be accessed by all files that include this one
global $pdo;

?>