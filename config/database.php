<?php

// Database credentials
$host = 'localhost';
$user = 'root';
$pass = 'Sa@123456';
$dbname = 'coach_pro';

// Create MySQLi connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
