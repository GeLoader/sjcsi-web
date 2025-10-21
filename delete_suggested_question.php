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
$id = intval($data['id'] ?? 0);

if ($id < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid question ID']);
    exit;
}

try {
    // Check if question exists
    $checkStmt = dbQuery("SELECT id FROM chatbot_suggested_questions WHERE id = ?", [$id]);
    if ($checkStmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Question not found']);
        exit;
    }
    
    // Check if question is linked to any responses
    $linkedStmt = dbQuery("SELECT id FROM chatbot_responses WHERE suggested_question_id = ?", [$id]);
    if ($linkedStmt->num_rows > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Cannot delete question. It is linked to one or more chatbot responses. Please unlink the responses first.'
        ]);
        exit;
    }
    
    // Get the display order of the question to be deleted
    $orderStmt = dbQuery("SELECT display_order FROM chatbot_suggested_questions WHERE id = ?", [$id]);
    $orderData = $orderStmt->fetch_assoc();
    $deletedOrder = $orderData['display_order'];
    
    // Delete the question
    $deleteStmt = dbQuery("DELETE FROM chatbot_suggested_questions WHERE id = ?", [$id]);
    
    if ($deleteStmt) {
        // Update display orders of remaining questions
        dbQuery("UPDATE chatbot_suggested_questions SET display_order = display_order - 1 WHERE display_order > ?", [$deletedOrder]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Question deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete question'
        ]);
    }
} catch (Exception $e) {
    error_log("Delete Suggested Question Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting question: ' . $e->getMessage()
    ]);
}
?>