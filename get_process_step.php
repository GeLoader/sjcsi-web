<?php
// get_process_step.php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
    exit;
}

$id = intval($_GET['id']);

try {
    $result = dbQuery("SELECT * FROM enrollment_process WHERE id = ?", [$id]);
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Process step not found']);
        exit;
    }
    
    $step = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => $step
    ]);
    
} catch (Exception $e) {
    error_log("Get Process Step Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>