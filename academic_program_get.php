<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid program ID']);
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = dbPrepare("
        SELECT ap.*, d.name as dept_name 
        FROM academic_programs ap 
        LEFT JOIN departments d ON ap.department_code = d.code 
        WHERE ap.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $program = $result->fetch_assoc();
        
        // Check if the learn_more_link is a PDF file
        if (!empty($program['learn_more_link']) && strpos($program['learn_more_link'], 'files/program_') === 0) {
            $program['has_pdf'] = true;
            $program['pdf_filename'] = basename($program['learn_more_link']);
        } else {
            $program['has_pdf'] = false;
            $program['pdf_filename'] = null;
        }
        
        echo json_encode(['success' => true, 'program' => $program]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Program not found']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    error_log("Academic Program Get Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>