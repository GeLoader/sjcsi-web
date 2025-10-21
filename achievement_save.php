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

try {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $achievement_date = trim($_POST['achievement_date']);
    $awarded_to = isset($_POST['awarded_to']) ? trim($_POST['awarded_to']) : '';
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    // Validate required fields
    if (empty($title) || empty($description) || empty($category) || empty($achievement_date)) {
        throw new Exception('All required fields must be filled');
    }
    
    // Handle file upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/achievements/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            throw new Exception('File is not an image');
        }
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
            $image_url = 'uploads/achievements/' . $file_name;
        } else {
            throw new Exception('Failed to upload image');
        }
    }
    
    if ($id > 0) {
        // Update existing achievement
        if (!empty($image_url)) {
            $stmt = dbPrepare("UPDATE achievements SET title = ?, description = ?, category = ?, achievement_date = ?, awarded_to = ?, image_url = ?, is_published = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssssssii", $title, $description, $category, $achievement_date, $awarded_to, $image_url, $is_published, $id);
        } else {
            $stmt = dbPrepare("UPDATE achievements SET title = ?, description = ?, category = ?, achievement_date = ?, awarded_to = ?, is_published = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("sssssii", $title, $description, $category, $achievement_date, $awarded_to, $is_published, $id);
        }
    } else {
        // Insert new achievement
        $stmt = dbPrepare("INSERT INTO achievements (title, description, category, achievement_date, awarded_to, image_url, is_published) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $title, $description, $category, $achievement_date, $awarded_to, $image_url, $is_published);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Achievement saved successfully']);
    } else {
        throw new Exception('Failed to save achievement: ' . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Achievement Save Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>