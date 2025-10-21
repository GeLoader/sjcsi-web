<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and validate input data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = trim($_POST['name'] ?? '');
$department_code = trim($_POST['department_code'] ?? '');
$level = trim($_POST['level'] ?? '');
$description = trim($_POST['description'] ?? '');
$duration = trim($_POST['duration'] ?? '');
$units = trim($_POST['units'] ?? '');
$tuition_fee = trim($_POST['tuition_fee'] ?? '');
$learn_more_link = trim($_POST['learn_more_link'] ?? '');
$custom_link = trim($_POST['custom_link'] ?? '');

// Use custom link if provided and custom option is selected
if ($learn_more_link === 'custom' && !empty($custom_link)) {
    $learn_more_link = $custom_link;
}

// Validate required fields
if (empty($name) || empty($department_code) || empty($level) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Validate level
$valid_levels = ['college', 'shs', 'jhs'];
if (!in_array($level, $valid_levels)) {
    echo json_encode(['success' => false, 'message' => 'Invalid program level']);
    exit;
}

try {
    // Handle PDF file upload
    $pdfPath = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $pdfFile = $_FILES['pdf_file'];
        
        // Validate file type
        $fileType = mime_content_type($pdfFile['tmp_name']);
        $fileExtension = strtolower(pathinfo($pdfFile['name'], PATHINFO_EXTENSION));
        
        if ($fileType !== 'application/pdf' || $fileExtension !== 'pdf') {
            echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed.']);
            exit;
        }
        
        // Validate file size (max 10MB)
        if ($pdfFile['size'] > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size must be less than 10MB.']);
            exit;
        }
        
        // Create files directory if it doesn't exist
        $filesDir = BASE_PATH . '/files';
        if (!is_dir($filesDir)) {
            mkdir($filesDir, 0755, true);
        }
        
        // Generate unique filename
        $filename = 'program_' . uniqid() . '_' . time() . '.pdf';
        $pdfPath = 'files/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($pdfFile['tmp_name'], BASE_PATH . '/' . $pdfPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload PDF file.']);
            exit;
        }
        
        // If updating and there's an existing PDF, delete the old one
        if ($id > 0) {
            $existingResult = dbQuery("SELECT learn_more_link FROM academic_programs WHERE id = ?", [$id]);
            if ($existingResult && $row = $existingResult->fetch_assoc()) {
                $existingPath = $row['learn_more_link'];
                if ($existingPath && file_exists(BASE_PATH . '/' . $existingPath) && strpos($existingPath, 'files/program_') === 0) {
                    unlink(BASE_PATH . '/' . $existingPath);
                }
            }
        }
        
        // Use the PDF path as the learn more link
        $learn_more_link = $pdfPath;
    } elseif ($id > 0 && empty($learn_more_link)) {
        // Keep existing PDF if no new file uploaded and no link provided
        $existingResult = dbQuery("SELECT learn_more_link FROM academic_programs WHERE id = ?", [$id]);
        if ($existingResult && $row = $existingResult->fetch_assoc()) {
            $learn_more_link = $row['learn_more_link'];
        }
    }

    if ($id > 0) {
        // Update existing program
        $stmt = dbPrepare("
            UPDATE academic_programs 
            SET name = ?, department_code = ?, level = ?, description = ?, 
                duration = ?, units = ?, tuition_fee = ?, learn_more_link = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("ssssssssi", $name, $department_code, $level, $description, 
                         $duration, $units, $tuition_fee, $learn_more_link, $id);
    } else {
        // Insert new program
        $stmt = dbPrepare("
            INSERT INTO academic_programs 
            (name, department_code, level, description, duration, units, tuition_fee, learn_more_link) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssss", $name, $department_code, $level, $description, 
                         $duration, $units, $tuition_fee, $learn_more_link);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Program saved successfully']);
    } else {
        // If there was an error and we uploaded a PDF, delete it
        if (isset($pdfPath) && file_exists(BASE_PATH . '/' . $pdfPath)) {
            unlink(BASE_PATH . '/' . $pdfPath);
        }
        echo json_encode(['success' => false, 'message' => 'Error saving program: ' . $stmt->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    // If there was an exception and we uploaded a PDF, delete it
    if (isset($pdfPath) && file_exists(BASE_PATH . '/' . $pdfPath)) {
        unlink(BASE_PATH . '/' . $pdfPath);
    }
    error_log("Academic Program Save Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>