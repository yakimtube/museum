<?php
session_start();
require_once 'config/database.php';
require_once 'includes/language.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museum Audio Guide</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/audioHandler.js"></script>
</head>
<body class="bg-gray-100">
    <?php
    if (!isset($_SESSION['access_verified'])) {
        include 'views/landing.php';
    } else {
        include 'views/exhibit.php';
    }
    ?>
</body>
</html>