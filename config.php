<?php
// Basic configuration file
define('SITE_NAME', 'SJCSI - Saint Joseph College of Sindangan Incorporated');
define('SITE_DESC', 'Official website of Saint Joseph College of Sindangan Incorporated - Empowering minds, building futures through quality education and innovation.');

define('BASE_PATH', __DIR__);
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $protocol . '://' . $host . $base_path);


// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sjcsi_db');

// Function to generate URLs
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

 require_once BASE_PATH . '/database.php';
?>