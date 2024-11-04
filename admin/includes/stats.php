<?php
class VisitorStats {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getTodayVisitors() {
        $query = "SELECT COUNT(DISTINCT access_code) as count 
                 FROM visit_logs 
                 WHERE DATE(timestamp) = CURDATE()";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }
    
    public function getActiveAccessCodes() {
        $query = "SELECT COUNT(*) as count 
                 FROM access_codes 
                 WHERE is_active = 1 
                 AND valid_until >= NOW()";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }
    
    public function getTotalExhibits() {
        $query = "SELECT COUNT(*) as count FROM exhibits";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }
    
    public function getExhibits() {
        $query = "SELECT e.id, 
                        GROUP_CONCAT(DISTINCT ec.language_id) as languages,
                        MAX(CASE WHEN ec.language_id = 'en' THEN ec.title END) as title
                 FROM exhibits e
                 LEFT JOIN exhibit_content ec ON e.id = ec.exhibit_id
                 GROUP BY e.id
                 ORDER BY e.created_at DESC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getPopularExhibits($limit = 5) {
        $query = "SELECT e.id, 
                        MAX(CASE WHEN ec.language_id = 'en' THEN ec.title END) as title,
                        COUNT(*) as visits
                 FROM visit_logs v
                 JOIN exhibits e ON v.exhibit_id = e.id
                 LEFT JOIN exhibit_content ec ON e.id = ec.exhibit_id
                 WHERE v.timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 GROUP BY e.id
                 ORDER BY visits DESC
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getLanguageDistribution() {
        $query = "SELECT v.language_id, 
                        l.name,
                        COUNT(*) as usage_count
                 FROM visit_logs v
                 JOIN languages l ON v.language_id = l.id
                 WHERE v.timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                 GROUP BY v.language_id
                 ORDER BY usage_count DESC";
        
        return $this->conn->query($query)->fetch_all(MYSQLI_ASSOC);
    }
}