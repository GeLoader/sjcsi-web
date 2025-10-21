<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Achievement ID required']);
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = dbPrepare("SELECT * FROM achievements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $achievement = $result->fetch_assoc();
        echo json_encode(['success' => true, 'achievement' => $achievement]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Achievement not found']);
    }
    
} catch (Exception $e) {
    error_log("Achievement Get Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>