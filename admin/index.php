<?php
session_start();
require_once '../config/database.php';
require_once 'includes/auth.php';

if (!isAdminIP()) {
    die("Access denied: Invalid IP address");
}

if (!isset($_SESSION['admin_logged_in'])) {
    include 'views/login.php';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museum Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <nav class="bg-white shadow-md">
            <div class="container mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-bold">Museum Admin</h1>
                    <a href="logout.php" class="text-red-600 hover:text-red-800">Logout</a>
                </div>
            </div>
        </nav>
        
        <div class="container mx-auto px-6 py-8">
            <?php include 'views/dashboard.php'; ?>
        </div>
    </div>
</body>
</html>