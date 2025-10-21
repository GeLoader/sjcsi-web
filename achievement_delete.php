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

$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['id']) ? intval($data['id']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid achievement ID']);
    exit;
}

try {
    // First, get the image URL to delete the file
    $stmt = dbPrepare("SELECT image_url FROM achievements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $achievement = $result->fetch_assoc();
        // Delete the image file if it exists
        if (!empty($achievement['image_url']) && file_exists(__DIR__ . '/' . $achievement['image_url'])) {
            unlink(__DIR__ . '/' . $achievement['image_url']);
        }
    }
    
    // Delete the achievement
    $stmt = dbPrepare("DELETE FROM achievements WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Achievement deleted successfully']);
    } else {
        throw new Exception('Failed to delete achievement: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Achievement Delete Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>