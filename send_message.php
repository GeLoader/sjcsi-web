<?php
require_once 'config.php';
require_once BASE_PATH . '/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
if (empty($_POST['name']) || empty($_POST['contact_no']) || empty($_POST['message']) || empty($_POST['department'])) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

// Sanitize inputs
$department = trim($_POST['department']);
$name = trim($_POST['name']);
$contact_no = trim($_POST['contact_no']);
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = trim($_POST['message']);

 
// Insert message into database
try {
    $stmt = dbPrepare("INSERT INTO department_messages (department_code, name, contact_no, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $department, $name, $contact_no, $subject, $message);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send your message. Please try again.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}