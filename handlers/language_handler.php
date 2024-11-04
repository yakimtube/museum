<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'] ?? 'en';
    $accessCode = $_POST['access_code'] ?? '';
    
    // Validate access code
    $stmt = $conn->prepare("SELECT code FROM access_codes 
                           WHERE code = ? 
                           AND is_active = 1 
                           AND valid_from <= NOW() 
                           AND valid_until >= NOW()");
    $stmt->bind_param('s', $accessCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['access_verified'] = true;
        $_SESSION['access_code'] = $accessCode;
        $_SESSION['lang'] = $language;
        header('Location: ../index.php');
    } else {
        $_SESSION['error'] = 'Invalid access code';
        header('Location: ../index.php');
    }
    exit();
}