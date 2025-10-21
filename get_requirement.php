<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT * FROM admission_requirements WHERE id = $id";
    $result = dbQuery($sql);
    
    if ($result && $result->num_rows > 0) {
        $requirement = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $requirement]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Requirement not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID parameter is required']);
}
?>