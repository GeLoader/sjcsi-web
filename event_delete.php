<?php
// event_delete.php - Delete an event
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

// Get event ID from POST data
$data = json_decode(file_get_contents('php://input'), true);
$event_id = $data['id'] ?? 0;

if (!$event_id || !is_numeric($event_id)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
    exit;
}

try {
    // First get image path to delete the file
    $sql = "SELECT id,title FROM events WHERE id = ?";
    $stmt = dbPrepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
      if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Event not found']);
        exit;
    }
    
     $events = $result->fetch_assoc();
    // Now delete the event
    $sql = "DELETE FROM events WHERE id = ?";
    $stmt = dbPrepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();

 if ($stmt->affected_rows > 0) {
        // Log the deletion (optional)
        error_log("Event deleted: ID {$event_id}, Title: {$events['title']}, By: {$_SESSION['user']['email']}");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Event deleted successfully',
            'deleted_id' => $event_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete event']);
    }
 
} catch (Exception $e) {
    error_log("Delete event error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}