<?php
function isAdminIP() {
    $allowedIPs = ['127.0.0.1', '192.168.1.0/24']; // Add museum network IPs
    $clientIP = $_SERVER['REMOTE_ADDR'];
    
    foreach ($allowedIPs as $ip) {
        if (strpos($ip, '/') !== false) {
            // Handle CIDR notation
            if (cidrMatch($clientIP, $ip)) {
                return true;
            }
        } else {
            if ($clientIP === $ip) {
                return true;
            }
        }
    }
    
    return false;
}

function cidrMatch($ip, $cidr) {
    list($subnet, $mask) = explode('/', $cidr);
    
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $mask);
    
    return ($ip & $mask) === ($subnet & $mask);
}

function verifyAdmin($username, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE username = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        // For debugging purposes, log the provided password and stored hash
        error_log("Verifying password for user: " . $username);
        error_log("Stored hash: " . $row['password_hash']);
        
        return password_verify($password, $row['password_hash']);
    }
    
    return false;
}