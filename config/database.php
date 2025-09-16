<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'camp_of_coffee');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection using PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to check database connection
function checkDatabaseConnection() {
    global $pdo;
    return $pdo !== null;
}
?>
