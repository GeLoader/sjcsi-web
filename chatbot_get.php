<?php
// chatbot_get.php - Get single chatbot response for editing
require_once 'config.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

try {
    // Get chatbot response ID
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        throw new Exception('Invalid chatbot response ID');
    }
    
    // Fetch chatbot response
    $stmt = $conn->prepare("SELECT * FROM chatbot_responses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Chatbot response not found');
    }
    
    $chatbot = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'chatbot' => $chatbot
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Get Chatbot Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>