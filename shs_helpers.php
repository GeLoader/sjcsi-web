<?php
// shs_helpers.php - Helper functions for SHS department content management

require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/database.php';

/**
 * Get all SHS page sections
 */
function getSHSSections($active_only = true) {
    $where_clause = $active_only ? "WHERE is_active = 1" : "";
    $stmt = dbPrepare("SELECT * FROM shs_page $where_clause ORDER BY display_order");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[$row['section_key']] = $row;
    }
    return $sections;
}

/**
 * Get a specific SHS section by key
 */
function getSHSSection($section_key) {
    $stmt = dbPrepare("SELECT * FROM shs_page WHERE section_key = ? AND is_active = 1");
    $stmt->bind_param('s', $section_key);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Update SHS section
 */
function updateSHSSection($section_key, $title, $content, $meta_data = null, $user_id = null) {
    $stmt = dbPrepare("UPDATE shs_page SET title = ?, content = ?, meta_data = ?, updated_by = ? WHERE section_key = ?");
    $stmt->bind_param('ssssi', $title, $content, $meta_data, $user_id, $section_key);
    return $stmt->execute();
}

/**
 * Get all SHS faculty members
 */
function getSHSFaculty($active_only = true) {
    $where_clause = $active_only ? "WHERE is_active = 1" : "";
    $stmt = dbPrepare("SELECT * FROM shs_faculty $where_clause ORDER BY display_order");
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Add new faculty member
 */
function addSHSFaculty($name, $position, $email = null, $specialization = null) {
    $stmt = dbPrepare("INSERT INTO shs_faculty (name, position, email, specialization) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $position, $email, $specialization);
    return $stmt->execute();
}

/**
 * Update faculty member
 */
function updateSHSFaculty($id, $name, $position, $email = null, $specialization = null) {
    $stmt = dbPrepare("UPDATE shs_faculty SET name = ?, position = ?, email = ?, specialization = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $name, $position, $email, $specialization, $id);
    return $stmt->execute();
}

/**
 * Delete (deactivate) faculty member
 */
function deleteSHSFaculty($id) {
    $stmt = dbPrepare("UPDATE shs_faculty SET is_active = 0 WHERE id = ?");
    $stmt->bind_param('i', $id);
    return $stmt->execute();
}

/**
 * Get recent SHS updates
 */
function getSHSRecentUpdates($limit = 5) {
    $stmt = dbPrepare("SELECT sp.section_name, sp.updated_at, u.email as updated_by_email 
                       FROM shs_page sp 
                       LEFT JOIN users u ON sp.updated_by = u.id 
                       WHERE sp.updated_at IS NOT NULL 
                       ORDER BY sp.updated_at DESC LIMIT ?");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get SHS dashboard statistics
 */
function getSHSStats() {
    // Get total sections
    $sections_stmt = dbPrepare("SELECT COUNT(*) as total FROM shs_page WHERE is_active = 1");
    $sections_stmt->execute();
    $sections_count = $sections_stmt->get_result()->fetch_assoc()['total'];
    
    // Get faculty count
    $faculty_stmt = dbPrepare("SELECT COUNT(*) as total FROM shs_faculty WHERE is_active = 1");
    $faculty_stmt->execute();
    $faculty_count = $faculty_stmt->get_result()->fetch_assoc()['total'];
    
    // Get last update
    $update_stmt = dbPrepare("SELECT MAX(updated_at) as last_update FROM shs_page WHERE updated_at IS NOT NULL");
    $update_stmt->execute();
    $last_update = $update_stmt->get_result()->fetch_assoc()['last_update'];
    
    return [
        'sections_count' => $sections_count,
        'faculty_count' => $faculty_count,
        'last_update' => $last_update,
        'page_views' => 1247 // This would come from analytics in a real system
    ];
}

/**
 * Log SHS activity (for audit trail)
 */
function logSHSActivity($user_id, $action, $details = null) {
    try {
        // Check if activity log table exists, create if not
        $create_log_table = "
            CREATE TABLE IF NOT EXISTS shs_activity_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                action VARCHAR(255) NOT NULL,
                details TEXT,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )
        ";
        dbQuery($create_log_table);
        
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt = dbPrepare("INSERT INTO shs_activity_log (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $user_id, $action, $details, $ip_address, $user_agent);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("SHS Activity Log Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Search SHS content
 */
function searchSHSContent($query) {
    $search_term = '%' . $query . '%';
    $stmt = dbPrepare("SELECT section_key, section_name, title, content 
                       FROM shs_page 
                       WHERE is_active = 1 
                       AND (title LIKE ? OR content LIKE ? OR section_name LIKE ?)
                       ORDER BY display_order");
    $stmt->bind_param('sss', $search_term, $search_term, $search_term);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Backup SHS data
 */
function backupSHSData() {
    try {
        $backup_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'sections' => getSHSSections(false),
            'faculty' => getSHSFaculty(false)
        ];
        
        $backup_file = 'backups/shs_backup_' . date('Y-m-d_H-i-s') . '.json';
        $backup_dir = dirname($backup_file);
        
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        file_put_contents($backup_file, json_encode($backup_data, JSON_PRETTY_PRINT));
        return $backup_file;
    } catch (Exception $e) {
        error_log("SHS Backup Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate section data
 */
function validateSHSSectionData($title, $content, $meta_data = null) {
    $errors = [];
    
    if (empty(trim($title))) {
        $errors[] = 'Title is required';
    }
    
    if (strlen($title) > 255) {
        $errors[] = 'Title is too long (maximum 255 characters)';
    }
    
    if (!empty($meta_data)) {
        $decoded = json_decode($meta_data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errors[] = 'Invalid JSON format in additional data';
        }
    }
    
    return $errors;
}

/**
 * Validate faculty data
 */
function validateSHSFacultyData($name, $position, $email = null) {
    $errors = [];
    
    if (empty(trim($name))) {
        $errors[] = 'Name is required';
    }
    
    if (empty(trim($position))) {
        $errors[] = 'Position is required';
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    return $errors;
}

/**
 * Get section content safely with fallback
 */
function getSHSSectionContent($sections, $key, $field = 'content', $default = '') {
    if (!isset($sections[$key]) || !isset($sections[$key][$field])) {
        return $default;
    }
    return htmlspecialchars($sections[$key][$field]);
}

/**
 * Parse and get metadata safely
 */
function getSHSMetadata($sections, $key, $default = null) {
    if (!isset($sections[$key]['meta_data'])) {
        return $default;
    }
    
    $metadata = json_decode($sections[$key]['meta_data'], true);
    return $metadata ?: $default;
}
?>