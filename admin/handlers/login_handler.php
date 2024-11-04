<?php
session_start();
require_once '../../config/database.php';
require_once '../includes/auth.php';

if (!isAdminIP()) {
    die("Access denied: Invalid IP address");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Add debug logging
    error_log("Login attempt for username: " . $username);
    
    if (verifyAdmin($username, $password)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: ../index.php');
        exit();
    } else {
        error_log("Login failed for username: " . $username);
        $_SESSION['login_error'] = 'Invalid username or password';
        header('Location: ../index.php');
        exit();
    }
}