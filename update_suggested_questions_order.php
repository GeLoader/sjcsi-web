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
$updates = $data['updates'] ?? [];

if (empty($updates)) {
    echo json_encode(['success' => false, 'message' => 'No updates provided']);
    exit;
}

try {
    // Start transaction
    dbQuery("START TRANSACTION");
    
    $success = true;
    $errorMessage = '';
    
    foreach ($updates as $update) {
        $id = intval($update['id']);
        $display_order = intval($update['display_order']);
        $is_active = intval($update['is_active']);
        
        if ($id < 1 || $display_order < 1) {
            $success = false;
            $errorMessage = 'Invalid data provided';
            break;
        }
        
        $stmt = dbQuery(
            "UPDATE chatbot_suggested_questions SET display_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
            [$display_order, $is_active, $id]
        );
        
        if (!$stmt) {
            $success = false;
            $errorMessage = 'Failed to update question ID: ' . $id;
            break;
        }
    }
    
    if ($success) {
        dbQuery("COMMIT");
        echo json_encode([
            'success' => true,
            'message' => 'Questions updated successfully'
        ]);
    } else {
        dbQuery("ROLLBACK");
        echo json_encode([
            'success' => false,
            'message' => $errorMessage
        ]);
    }
} catch (Exception $e) {
    dbQuery("ROLLBACK");
    error_log("Update Suggested Questions Order Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating questions: ' . $e->getMessage()
    ]);
}
?>