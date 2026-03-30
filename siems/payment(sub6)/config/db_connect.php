<?php
// Database Configuration - UPDATE THESE CREDENTIALS
define('DB_HOST', 'localhost');
define('DB_NAME', 'siems_unified');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP: empty

// Build the project base URL from the current request path.
// Admin and student pages live in subfolders, so we normalize back to the
// application root instead of using the current script directory.
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/payment(sub6)/index.php');
$basePath = preg_replace('#/(admin|student)/[^/]+$#', '', $scriptName);
if ($basePath === null || $basePath === $scriptName) {
    $basePath = dirname($scriptName);
}
$basePath = rtrim(str_replace('\\', '/', $basePath), '/');
define('SITE_URL', 'http://localhost' . ($basePath === '' ? '/' : $basePath . '/'));

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session automatically for web requests.
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

