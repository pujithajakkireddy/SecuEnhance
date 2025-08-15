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
    // Create a new PDO instance and store it in the $conn variable
    // This variable name is used in other files of the application.
    $conn = new PDO($dsn, $user, $password);

    // Set PDO attributes for better error handling and security
    // This tells PDO to throw exceptions on errors, which makes debugging easier.
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // This tells PDO to fetch results as an associative array by default.
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails, display a user-friendly error message and exit
    // NEVER show the full error in a live application.
    die("Database connection failed: " . $e->getMessage());
}

?>