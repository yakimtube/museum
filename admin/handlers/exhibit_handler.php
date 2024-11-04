<?php
session_start();
require_once '../../config/database.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Create upload directories if they don't exist
$uploadDirs = [
    '../uploads',
    '../uploads/images',
    '../uploads/audio'
];

foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

function handleFileUpload($file, $targetDir) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Ensure directory exists and is writable
    if (!is_dir($targetDir) || !is_writable($targetDir)) {
        error_log("Upload directory not writable: " . $targetDir);
        return false;
    }
    
    // Sanitize filename and generate unique name
    $fileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($file['name']));
    $targetPath = $targetDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }
    
    error_log("Failed to move uploaded file to: " . $targetPath);
    return false;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
    case 'edit':
        $exhibitId = $_POST['id'];
        $languages = ['en', 'fr', 'es', 'ar'];
        
        $conn->begin_transaction();
        
        try {
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO exhibits (id) VALUES (?)");
                $stmt->bind_param('s', $exhibitId);
                $stmt->execute();
            }
            
            // Handle image upload
            $imagePath = null;
            if (!empty($_FILES['image']['name'])) {
                $imagePath = handleFileUpload($_FILES['image'], '../uploads/images/');
                if (!$imagePath) {
                    throw new Exception('Error uploading image');
                }
            }
            
            foreach ($languages as $lang) {
                $title = $_POST["title_$lang"];
                $description = $_POST["description_$lang"];
                
                // Handle audio upload
                $audioPath = null;
                if (!empty($_FILES["audio_$lang"]['name'])) {
                    $audioPath = handleFileUpload($_FILES["audio_$lang"], '../uploads/audio/');
                    if (!$audioPath) {
                        throw new Exception("Error uploading audio for $lang");
                    }
                }
                
                $stmt = $conn->prepare("INSERT INTO exhibit_content 
                                      (exhibit_id, language_id, title, description, image_path, audio_path)
                                      VALUES (?, ?, ?, ?, ?, ?)
                                      ON DUPLICATE KEY UPDATE
                                      title = VALUES(title),
                                      description = VALUES(description),
                                      image_path = COALESCE(VALUES(image_path), image_path),
                                      audio_path = COALESCE(VALUES(audio_path), audio_path)");
                
                $stmt->bind_param('ssssss', 
                    $exhibitId, $lang, $title, $description, $imagePath, $audioPath
                );
                $stmt->execute();
            }
            
            $conn->commit();
            header('Location: ../index.php?success=1');
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error in exhibit handler: " . $e->getMessage());
            header('Location: ../index.php?error=' . urlencode($e->getMessage()));
        }
        break;
        
    case 'delete':
        $exhibitId = $_POST['id'];
        
        $conn->begin_transaction();
        
        try {
            // Delete associated files first
            $stmt = $conn->prepare("SELECT image_path, audio_path FROM exhibit_content WHERE exhibit_id = ?");
            $stmt->bind_param('s', $exhibitId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                if ($row['image_path']) {
                    $imagePath = '../uploads/images/' . $row['image_path'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                if ($row['audio_path']) {
                    $audioPath = '../uploads/audio/' . $row['audio_path'];
                    if (file_exists($audioPath)) {
                        unlink($audioPath);
                    }
                }
            }
            
            // Delete database records
            $stmt = $conn->prepare("DELETE FROM exhibit_content WHERE exhibit_id = ?");
            $stmt->bind_param('s', $exhibitId);
            $stmt->execute();
            
            $stmt = $conn->prepare("DELETE FROM exhibits WHERE id = ?");
            $stmt->bind_param('s', $exhibitId);
            $stmt->execute();
            
            $conn->commit();
            echo json_encode(['success' => true]);
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error deleting exhibit: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'get':
        $exhibitId = $_GET['id'];
        
        $stmt = $conn->prepare("SELECT language_id, title, description 
                              FROM exhibit_content 
                              WHERE exhibit_id = ?");
        $stmt->bind_param('s', $exhibitId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $content = [];
        while ($row = $result->fetch_assoc()) {
            $content[$row['language_id']] = [
                'title' => $row['title'],
                'description' => $row['description']
            ];
        }
        
        echo json_encode([
            'id' => $exhibitId,
            'content' => $content
        ]);
        break;
}