<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['access_verified'])) {
    http_response_code(401);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $exhibitId = $_GET['id'] ?? '';
    $language = $_SESSION['lang'] ?? 'en';
    
    // Log the visit
    $stmt = $conn->prepare("INSERT INTO visit_logs (access_code, exhibit_id, language_id) 
                           VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $_SESSION['access_code'], $exhibitId, $language);
    $stmt->execute();
    
    // Get exhibit content
    $stmt = $conn->prepare("SELECT title, description, image_path, audio_path 
                           FROM exhibit_content 
                           WHERE exhibit_id = ? AND language_id = ?");
    $stmt->bind_param('ss', $exhibitId, $language);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'title' => $row['title'],
                'description' => $row['description'],
                'image_url' => $row['image_path'] ? '/uploads/images/' . $row['image_path'] : null,
                'audio_url' => $row['audio_path'] ? '/uploads/audio/' . $row['audio_path'] : null
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Exhibit not found']);
    }
}