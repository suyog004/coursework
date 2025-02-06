<?php
$host = '127.0.0.1';   // MySQL server address
$port = '3307';         // Port for MySQL
$dbname = 'subscription-system';  // Database name
$username = 'root';     // MySQL username
$password = '';         // MySQL password (change if applicable)

// Data Source Name (DSN)
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

// Try to establish a connection to the database
try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Error mode
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch as associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                    // Disables emulation of prepared statements
    ]);
    echo "Connected successfully!";
} catch (PDOException $e) {
    // In case of error, show the error message and terminate the script
    die("Connection failed: " . $e->getMessage());
}
?>
