<?php
session_start();
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'get_message') {
    $messageId = $_POST['id'];
    
    $stmt = dbPrepare("SELECT * FROM department_messages WHERE id = ?");
    $stmt->bind_param('i', $messageId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $message = $result->fetch_assoc();
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Message not found']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>