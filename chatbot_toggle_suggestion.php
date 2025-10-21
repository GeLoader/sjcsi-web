<?php
// chatbot_toggle_suggestion.php - Toggle chatbot suggestion status

// Start output buffering to prevent any accidental output
ob_start();

require_once 'config.php';
session_start();

// Set header first to ensure JSON response
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    ob_end_flush();
    exit;
}

try {
    // Get POST data
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $is_suggested = isset($_POST['is_suggested']) ? intval($_POST['is_suggested']) : 0;
    
    if ($id <= 0) {
        throw new Exception('Invalid chatbot response ID');
    }
    
    // Use the global $mysqli connection from database.php
    global $mysqli;
    
    if (!$mysqli) {
        throw new Exception('Database connection not available');
    }
    
    // Check if the record exists first
    $checkStmt = $mysqli->prepare("SELECT id FROM chatbot_responses WHERE id = ?");
    if (!$checkStmt) {
        throw new Exception('Prepare check failed: ' . $mysqli->error);
    }
    
    $checkStmt->bind_param("i", $id);
    if (!$checkStmt->execute()) {
        throw new Exception('Execute check failed: ' . $checkStmt->error);
    }
    
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows === 0) {
        $checkStmt->close();
        throw new Exception('Chatbot response not found with ID: ' . $id);
    }
    $checkStmt->close();
    
    // Update the is_suggested status
    $stmt = $mysqli->prepare("UPDATE chatbot_responses SET is_suggested = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Prepare update failed: ' . $mysqli->error);
    }
    
    $stmt->bind_param("ii", $is_suggested, $id);
    
    if ($stmt->execute()) {
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affectedRows > 0) {
            $message = $is_suggested ? 'Added to suggested questions' : 'Removed from suggested questions';
            echo json_encode([
                'success' => true,
                'message' => $message,
                'new_status' => $is_suggested
            ]);
        } else {
            // No rows affected but no error - might be same value
            echo json_encode([
                'success' => true,
                'message' => 'Status unchanged',
                'new_status' => $is_suggested
            ]);
        }
    } else {
        throw new Exception('Execute update failed: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Chatbot Toggle Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

ob_end_flush(); // Send output
exit;
?>