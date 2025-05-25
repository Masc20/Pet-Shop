<?php
// Database configuration
define('DB_HOST', 'localhost:3333');
define('DB_NAME', 'pawfect_db');
define('DB_USER', 'root');
define('DB_PASS', '');

/*
 * If you need to specify a port, add it to the host, e.g. 'localhost:3333'
 * Example: define('DB_HOST', 'localhost:3333');
 */

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
