<?php
// ajax/delete_user.php - Delete user via AJAX
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$user_id = $input['id'] ?? 0;

// Validate user ID
if (!$user_id || !is_numeric($user_id)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

// Prevent deleting current admin user
if ($user_id == $_SESSION['user']['id']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
    exit;
}

// Set content type early
header('Content-Type: application/json');

try {
    // Check if user exists
    $checkSql = "SELECT id, email, role FROM users WHERE id = ?";
    $checkStmt = dbPrepare($checkSql);
    $checkStmt->bind_param('i', $user_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Begin transaction to handle related data
    $mysqli = $GLOBALS['db']->getConnection();
    $mysqli->autocommit(false);
    
    try {
        // Update related records to remove foreign key references
        // Set author_id to NULL in news table
        $updateNewsSql = "UPDATE news SET author_id = NULL WHERE author_id = ?";
        $updateNewsStmt = dbPrepare($updateNewsSql);
        $updateNewsStmt->bind_param('i', $user_id);
        $updateNewsStmt->execute();
        
        // Set author_id to NULL in events table
        $updateEventsSql = "UPDATE events SET author_id = NULL WHERE author_id = ?";
        $updateEventsStmt = dbPrepare($updateEventsSql);
        $updateEventsStmt->bind_param('i', $user_id);
        $updateEventsStmt->execute();
        
        // Set author_id to NULL in gallery table
        $updateGallerySql = "UPDATE gallery SET author_id = NULL WHERE author_id = ?";
        $updateGalleryStmt = dbPrepare($updateGallerySql);
        $updateGalleryStmt->bind_param('i', $user_id);
        $updateGalleryStmt->execute();
        
        // Delete the user
        $deleteSql = "DELETE FROM users WHERE id = ?";
        $deleteStmt = dbPrepare($deleteSql);
        $deleteStmt->bind_param('i', $user_id);
        $deleteStmt->execute();
        
        if ($deleteStmt->affected_rows > 0) {
            // Commit the transaction
            $mysqli->commit();
            
            // Log the deletion
            error_log("User deleted: ID {$user_id}, Email: {$user['email']}, Role: {$user['role']}, By: {$_SESSION['user']['email']}");
            
            echo json_encode([
                'success' => true, 
                'message' => 'User deleted successfully',
                'deleted_id' => $user_id
            ]);
        } else {
            // Rollback on failure
            $mysqli->rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
        
    } catch (Exception $e) {
        // Rollback on error
        $mysqli->rollback();
        throw $e;
    } finally {
        $mysqli->autocommit(true);
    }

} catch (Exception $e) {
    error_log("Delete user error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>