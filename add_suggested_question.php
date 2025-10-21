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
$question = trim($_POST['question'] ?? '');
$display_order = intval($_POST['display_order'] ?? 1);
$is_active = isset($_POST['is_active']) ? 1 : 0;

if (empty($question)) {
    echo json_encode(['success' => false, 'message' => 'Question is required']);
    exit;
}

if ($display_order < 1) {
    $display_order = 1;
}

try {
    // Check if display order already exists
    $checkStmt = dbQuery("SELECT id FROM chatbot_suggested_questions WHERE display_order = ?", [$display_order]);
    if ($checkStmt->num_rows > 0) {
        // Shift existing orders
        dbQuery("UPDATE chatbot_suggested_questions SET display_order = display_order + 1 WHERE display_order >= ?", [$display_order]);
    }
    
    // Insert new question
    $stmt = dbQuery(
        "INSERT INTO chatbot_suggested_questions (question, display_order, is_active) VALUES (?, ?, ?)",
        [$question, $display_order, $is_active]
    );
    
    if ($stmt) {
        echo json_encode([
            'success' => true,
            'message' => 'Question added successfully',
            'id' => $stmt->insert_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add question'
        ]);
    }
} catch (Exception $e) {
    error_log("Add Suggested Question Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error adding question: ' . $e->getMessage()
    ]);
}
?>