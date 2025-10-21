<?php
// ajax/delete_news.php - Delete news article via AJAX
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/config.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$news_id = $input['id'] ?? 0;

// Validate news ID
if (!$news_id || !is_numeric($news_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid news ID']);
    exit;
}

try {
    // Check if news exists
    $checkSql = "SELECT id, title FROM news WHERE id = ?";
    $checkStmt = dbPrepare($checkSql);
    $checkStmt->bind_param('i', $news_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'News article not found']);
        exit;
    }
    
    $news = $result->fetch_assoc();
    
    // Delete the news article
    $deleteSql = "DELETE FROM news WHERE id = ?";
    $deleteStmt = dbPrepare($deleteSql);
    $deleteStmt->bind_param('i', $news_id);
    $deleteStmt->execute();
    
    if ($deleteStmt->affected_rows > 0) {
        // Log the deletion (optional)
        error_log("News article deleted: ID {$news_id}, Title: {$news['title']}, By: {$_SESSION['user']['email']}");
        
        echo json_encode([
            'success' => true, 
            'message' => 'News article deleted successfully',
            'deleted_id' => $news_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete news article']);
    }

} catch (Exception $e) {
    error_log("Delete news error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>