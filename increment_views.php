<?php
// increment_views.php - AJAX endpoint for tracking view counts
require_once __DIR__ . '/config.php';

// Set JSON header
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['type']) || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }
    
    $type = $input['type'];
    $id = intval($input['id']);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ID']);
        exit;
    }
    
    // Validate type and increment views
    if ($type === 'news') {
        $query = "UPDATE news SET views = views + 1 WHERE id = ? AND status = 'published'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            // Get updated view count
            $select_query = "SELECT views FROM news WHERE id = ?";
            $select_stmt = $conn->prepare($select_query);
            $select_stmt->bind_param('i', $id);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'views' => $row['views'],
                'type' => 'news',
                'id' => $id
            ]);
        } else {
            echo json_encode(['error' => 'News item not found']);
        }
        
    } elseif ($type === 'event') {
        $query = "UPDATE events SET views = views + 1 WHERE id = ? AND status IN ('upcoming', 'published')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            // Get updated view count
            $select_query = "SELECT views FROM events WHERE id = ?";
            $select_stmt = $conn->prepare($select_query);
            $select_stmt->bind_param('i', $id);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            $row = $result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'views' => $row['views'],
                'type' => 'event',
                'id' => $id
            ]);
        } else {
            echo json_encode(['error' => 'Event not found']);
        }
        
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid type']);
    }
    
} catch (Exception $e) {
    error_log("View increment error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
?>