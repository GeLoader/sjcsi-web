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
    $step_number = intval($_POST['step_number']);
    $title = dbEscape($_POST['title']);
    $description = dbEscape($_POST['description']);
    $icon_class = dbEscape($_POST['icon_class']);
    $color_class = dbEscape($_POST['color_class']);
    $level = dbEscape($_POST['level']);
    
    if ($id > 0) {
        // Update existing step
        $sql = "UPDATE enrollment_process SET step_number = $step_number, title = '$title', 
                description = '$description', icon_class = '$icon_class', color_class = '$color_class',
                level = '$level', updated_at = NOW() WHERE id = $id";
    } else {
        // Insert new step
        $sql = "INSERT INTO enrollment_process (step_number, title, description, icon_class, color_class, level) 
                VALUES ($step_number, '$title', '$description', '$icon_class', '$color_class', '$level')";
    }
    
    $result = dbQuery($sql);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Process step saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving process step: ' . $db->error]);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>