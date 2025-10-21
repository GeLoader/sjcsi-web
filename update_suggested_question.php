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

// Get and validate input
$id = intval($_POST['id'] ?? 0);
$question = trim($_POST['question'] ?? '');

if ($id < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid question ID']);
    exit;
}

if (empty($question)) {
    echo json_encode(['success' => false, 'message' => 'Question is required']);
    exit;
}

try {
    // Check if question exists
    $checkStmt = dbQuery("SELECT id FROM chatbot_suggested_questions WHERE id = ?", [$id]);
    if ($checkStmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Question not found']);
        exit;
    }
    
    // Update question
    $stmt = dbQuery(
        "UPDATE chatbot_suggested_questions SET question = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
        [$question, $id]
    );
    
    if ($stmt) {
        echo json_encode([
            'success' => true,
            'message' => 'Question updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update question'
        ]);
    }
} catch (Exception $e) {
    error_log("Update Suggested Question Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error updating question: ' . $e->getMessage()
    ]);
}
?>