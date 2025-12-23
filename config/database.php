<?php
// start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include autoloader for automatic class loading
require_once __DIR__ . '/autoload.php';

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Sa@123456');
define('DB_NAME', 'coachPro');

