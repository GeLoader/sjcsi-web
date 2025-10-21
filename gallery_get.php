<?php
require_once __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID parameter required']);
    exit;
}

$id = intval($_GET['id']);

try {
    // Get gallery item
    $result = dbQuery("SELECT * FROM gallery WHERE id = $id");
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
        
        // Get associated images for image galleries
        if ($item['type'] === 'image') {
            $images_result = dbQuery("SELECT * FROM gallery_images WHERE gallery_id = $id ORDER BY display_order");
            $images = [];
            while ($image = $images_result->fetch_assoc()) {
                $images[] = $image;
            }
            $item['images'] = $images;
        }
        
        echo json_encode(['success' => true, 'item' => $item]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gallery item not found']);
    }
} catch (Exception $e) {
    error_log("Gallery Get Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>