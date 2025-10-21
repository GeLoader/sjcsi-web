<?php
// academic_calendar_delete.php - Delete academic calendar events
session_start();
require_once __DIR__ . '/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (empty($input['id']) || !is_numeric($input['id'])) {
        throw new Exception('Invalid event ID');
    }

    $id = intval($input['id']);

    // Check if event exists
    $checkStmt = dbPrepare("SELECT id FROM academic_calendar WHERE id = ?");
    $checkStmt->bind_param('i', $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        throw new Exception('Event not found');
    }
    $checkStmt->close();

    // Delete event
    $stmt = dbPrepare("DELETE FROM academic_calendar WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Event deleted successfully'
        ]);
    } else {
        throw new Exception('Database error: ' . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    error_log("Academic Calendar Delete Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>