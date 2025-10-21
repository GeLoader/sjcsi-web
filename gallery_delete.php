<?php
require_once __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);

try {
    $result = dbQuery("DELETE FROM gallery WHERE id = $id");
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Gallery item deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete gallery item']);
    }
} catch (Exception $e) {
    error_log("Gallery Delete Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>