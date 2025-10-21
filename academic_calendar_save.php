<?php
// academic_calendar_save.php - Save/update academic calendar events
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
    if (empty($_POST['title']) || empty($_POST['type']) || empty($_POST['start_date'])) {
        throw new Exception('Required fields are missing');
    }

    // Sanitize inputs
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title = trim($_POST['title']);
    $type = trim($_POST['type']);
    $start_date = trim($_POST['start_date']);
    $end_date = !empty($_POST['end_date']) ? trim($_POST['end_date']) : null;
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;

    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
        throw new Exception('Invalid start date format');
    }

    if ($end_date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        throw new Exception('Invalid end date format');
    }

    // Validate event type
    $valid_types = ['enrollment', 'classes', 'exams', 'holiday', 'event', 'break'];
    if (!in_array($type, $valid_types)) {
        throw new Exception('Invalid event type');
    }

    if ($id > 0) {
        // Update existing event
        $stmt = dbPrepare("
            UPDATE academic_calendar 
            SET title = ?, type = ?, start_date = ?, end_date = ?, description = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param('sssssi', $title, $type, $start_date, $end_date, $description, $id);
    } else {
        // Insert new event
        $stmt = dbPrepare("
            INSERT INTO academic_calendar (title, type, start_date, end_date, description)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('sssss', $title, $type, $start_date, $end_date, $description);
    }

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => $id > 0 ? 'Event updated successfully' : 'Event created successfully',
            'id' => $id > 0 ? $id : $stmt->insert_id
        ]);
    } else {
        throw new Exception('Database error: ' . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    error_log("Academic Calendar Save Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>