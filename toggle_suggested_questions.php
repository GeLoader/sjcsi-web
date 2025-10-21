<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$enabled = boolval($data['enabled'] ?? false);

try {
    // Store the setting in a settings table or use a simple approach
    // For simplicity, we'll create a settings table if it doesn't exist
    
    // Check if settings table exists, if not create it
    $checkTable = dbQuery("SHOW TABLES LIKE 'settings'");
    if ($checkTable->num_rows === 0) {
        // Create settings table
        dbQuery("CREATE TABLE settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
    }
    
    // Insert or update the setting
    $settingKey = 'chatbot_suggested_questions_enabled';
    $settingValue = $enabled ? '1' : '0';
    
    $checkStmt = dbQuery("SELECT id FROM settings WHERE setting_key = ?", [$settingKey]);
    
    if ($checkStmt->num_rows > 0) {
        // Update existing setting
        $stmt = dbQuery(
            "UPDATE settings SET setting_value = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = ?",
            [$settingValue, $settingKey]
        );
    } else {
        // Insert new setting
        $stmt = dbQuery(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)",
            [$settingKey, $settingValue]
        );
    }
    
    if ($stmt) {
        echo json_encode([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update settings'
        ]);
    }
} catch (Exception $e) {
    error_log("Toggle Suggested Questions Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating settings: ' . $e->getMessage()
    ]);
}
?>