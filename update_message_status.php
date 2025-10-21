<?php
session_start();
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $messageId = $_POST['id'];
    $status = $_POST['status'];
    
    $stmt = dbPrepare("UPDATE department_messages SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $messageId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>