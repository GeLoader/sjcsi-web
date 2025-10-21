<?php
require_once 'config.php';
session_start();
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $keywords = $_POST['keywords'] ?? '';
    $response_text = $_POST['response'] ?? '';
    $is_suggested = isset($_POST['is_suggested']) ? 1 : 0;

    // Validate input
    if (empty($keywords) || empty($response_text)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Keywords and response are required']);
        exit;
    }
    
    // Process keywords (split by comma and trim)
    $keywordArray = array_map('trim', explode(',', $keywords));
    $keywordsJson = json_encode($keywordArray);
    
    try {
        if ($id) {
            // Update existing response
            $stmt = dbPrepare("UPDATE chatbot_responses SET keywords = ?, response = ?, is_suggested = ? WHERE id = ?");
            $stmt->bind_param("ssii", $keywordsJson, $response_text, $is_suggested, $id);
        } else {
            // Insert new response
            $stmt = dbPrepare("INSERT INTO chatbot_responses (keywords, response, is_suggested) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $keywordsJson, $response_text, $is_suggested);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Chatbot response saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        error_log("Chatbot Save Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>