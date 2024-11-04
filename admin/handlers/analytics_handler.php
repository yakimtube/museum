<?php
session_start();
require_once '../../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/stats.php';

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$stats = new VisitorStats($conn);

$response = [
    'popularExhibits' => $stats->getPopularExhibits(),
    'languageDistribution' => $stats->getLanguageDistribution()
];

header('Content-Type: application/json');
echo json_encode($response);