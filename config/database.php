<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'museum_user');
define('DB_PASS', 'your_secure_password');
define('DB_NAME', 'museum_guide');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");