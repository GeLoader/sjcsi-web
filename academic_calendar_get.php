<?php
// academic_calendar_get.php - Retrieve a single academic calendar event for editing
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
    // Validate input
    if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid event ID');
    }

    $id = intval($_GET['id']);

    // Fetch event from database
    $stmt = dbPrepare("
        SELECT id, title, type, start_date, end_date, description
        FROM academic_calendar 
        WHERE id = ?
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Event not found');
    }

    $event = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'event' => $event
    ]);

    $stmt->close();

} catch (Exception $e) {
    error_log("Academic Calendar Get Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>