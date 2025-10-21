<?php
require_once __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Process form data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$title = $_POST['title'] ?? '';
$category = $_POST['category'] ?? '';
$type = $_POST['type'] ?? '';
$date = $_POST['date'] ?? '';
$description = $_POST['description'] ?? '';
$video_url = $_POST['video_url'] ?? '';

try {
    // Start transaction
    dbQuery("START TRANSACTION");

    if ($id > 0) {
        // Update existing item
        if ($type === 'video') {
            $query = "UPDATE gallery SET title=?, category=?, type=?, date=?, description=?, video_url=? WHERE id=?";
            $stmt = dbPrepare($query);
            $stmt->bind_param("ssssssi", $title, $category, $type, $date, $description, $video_url, $id);
        } else {
            $query = "UPDATE gallery SET title=?, category=?, type=?, date=?, description=? WHERE id=?";
            $stmt = dbPrepare($query);
            $stmt->bind_param("sssssi", $title, $category, $type, $date, $description, $id);
        }
    } else {
        // Insert new item
        if ($type === 'video') {
            $query = "INSERT INTO gallery (title, category, type, date, description, video_url) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = dbPrepare($query);
            $stmt->bind_param("ssssss", $title, $category, $type, $date, $description, $video_url);
        } else {
            $query = "INSERT INTO gallery (title, category, type, date, description) VALUES (?, ?, ?, ?, ?)";
            $stmt = dbPrepare($query);
            $stmt->bind_param("sssss", $title, $category, $type, $date, $description);
        }
    }
    
    if ($stmt->execute()) {
        $gallery_id = $id > 0 ? $id : $stmt->insert_id;
        
        // Handle multiple image uploads for image galleries
        if ($type === 'image' && isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
            $upload_dir = 'uploads/gallery/';
            
            // Create upload directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Process each uploaded file
            $image_count = count($_FILES['gallery_images']['name']);
            
            for ($i = 0; $i < $image_count; $i++) {
                if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file_name = $_FILES['gallery_images']['name'][$i];
                    $file_tmp = $_FILES['gallery_images']['tmp_name'][$i];
                    $file_type = $_FILES['gallery_images']['type'][$i];
                    $file_size = $_FILES['gallery_images']['size'][$i];
                    
                    // Validate file type
                    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception("Invalid file type: $file_name. Only JPG, PNG, GIF, and WebP are allowed.");
                    }
                    
                    // Validate file size (5MB max)
                    if ($file_size > 5 * 1024 * 1024) {
                        throw new Exception("File too large: $file_name. Maximum size is 5MB.");
                    }
                    
                    // Generate unique filename
                    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                    $unique_filename = uniqid() . '_' . time() . '_' . $i . '.' . $file_extension;
                    $destination = $upload_dir . $unique_filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file_tmp, $destination)) {
                        // Insert image record into gallery_images table
                        $image_query = "INSERT INTO gallery_images (gallery_id, image_path, display_order) VALUES (?, ?, ?)";
                        $image_stmt = dbPrepare($image_query);
                        $image_stmt->bind_param("isi", $gallery_id, $destination, $i);
                        $image_stmt->execute();
                    } else {
                        throw new Exception("Failed to upload file: $file_name");
                    }
                }
            }
        }
        
        // Commit transaction
        dbQuery("COMMIT");
        echo json_encode(['success' => true, 'message' => 'Gallery item saved successfully', 'id' => $gallery_id]);
        
    } else {
        // Rollback transaction
        dbQuery("ROLLBACK");
        echo json_encode(['success' => false, 'message' => 'Failed to save gallery item']);
    }
} catch (Exception $e) {
    // Rollback transaction on error
    dbQuery("ROLLBACK");
    error_log("Gallery Save Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>