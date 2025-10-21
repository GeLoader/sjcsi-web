<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Question ID is required']);
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = dbQuery("SELECT * FROM chatbot_suggested_questions WHERE id = ?", [$id]);
    $question = $stmt->fetch_assoc();
    
    if ($question) {
        echo json_encode([
            'success' => true,
            'question' => $question
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Question not found'
        ]);
    }
} catch (Exception $e) {
    error_log("Get Suggested Question Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching question'
    ]);
}
?>