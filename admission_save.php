<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $level = dbEscape($_POST['level']);
    $requirement = dbEscape($_POST['requirement']);
    $display_order = intval($_POST['display_order']);
    
    if ($id > 0) {
        // Update existing requirement
        $sql = "UPDATE admission_requirements SET level = '$level', requirement = '$requirement', 
                display_order = $display_order, updated_at = NOW() WHERE id = $id";
    } else {
        // Insert new requirement
        $sql = "INSERT INTO admission_requirements (level, requirement, display_order) 
                VALUES ('$level', '$requirement', $display_order)";
    }
    
    $result = dbQuery($sql);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Requirement saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving requirement: ' . $db->error]);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>