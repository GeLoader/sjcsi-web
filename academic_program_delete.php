<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Check if ID is provided
if (!isset($input['id']) || !is_numeric($input['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid program ID']);
    exit;
}

$id = intval($input['id']);

try {
    $stmt = dbPrepare("DELETE FROM academic_programs WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Program deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Program not found or already deleted']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting program: ' . $stmt->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log("Academic Program Delete Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>