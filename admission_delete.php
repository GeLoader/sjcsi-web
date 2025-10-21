<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the ID from the query string
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id > 0) {
        $sql = "DELETE FROM admission_requirements WHERE id = $id";
        $result = dbQuery($sql);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Requirement deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting requirement: ' . $db->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>