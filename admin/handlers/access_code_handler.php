<?php
session_start();
require_once '../../config/database.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    exit('Unauthorized');
}

function generateAccessCode($length = 8) {
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'generate':
        $validFrom = $_POST['valid_from'];
        $validUntil = $_POST['valid_until'];
        $quantity = (int)$_POST['quantity'];
        
        $codes = [];
        $stmt = $conn->prepare("INSERT INTO access_codes (code, valid_from, valid_until) VALUES (?, ?, ?)");
        
        for ($i = 0; $i < $quantity; $i++) {
            do {
                $code = generateAccessCode();
                $checkStmt = $conn->prepare("SELECT code FROM access_codes WHERE code = ?");
                $checkStmt->bind_param('s', $code);
                $checkStmt->execute();
                $exists = $checkStmt->get_result()->num_rows > 0;
            } while ($exists);
            
            $stmt->bind_param('sss', $code, $validFrom, $validUntil);
            if ($stmt->execute()) {
                $codes[] = $code;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'codes' => $codes]);
        break;
        
    case 'deactivate':
        $code = $_POST['code'];
        
        $stmt = $conn->prepare("UPDATE access_codes SET is_active = 0 WHERE code = ?");
        $stmt->bind_param('s', $code);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $stmt->execute()]);
        break;
        
    case 'list':
        $stmt = $conn->prepare("SELECT code, valid_from, valid_until, is_active 
                              FROM access_codes 
                              ORDER BY valid_from DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $codes = [];
        while ($row = $result->fetch_assoc()) {
            $codes[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($codes);
        break;
}