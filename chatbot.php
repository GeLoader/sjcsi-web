<?php
require_once 'config.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to user
ini_set('log_errors', 1);

// Simple chatbot logic
function getChatbotResponse($message, $mysqli) {
    $message = strtolower(trim($message));
    
    try {
        // Get all responses from database
        $stmt = $mysqli->prepare("SELECT id, keywords, response, is_suggested FROM chatbot_responses");
        
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $mysqli->error);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $responses = [];
        while ($row = $result->fetch_assoc()) {
            $responses[] = $row;
        }
        
        $stmt->close();
        
        // Check for matching keywords
        foreach ($responses as $response) {
            // Decode keywords if stored as JSON
            $keywords = is_string($response['keywords']) ? 
                json_decode($response['keywords'], true) : $response['keywords'];
            
            if (is_array($keywords)) {
                foreach ($keywords as $keyword) {
                    $cleanKeyword = strtolower(trim($keyword));
                    if (strpos($message, $cleanKeyword) !== false) {
                        return $response['response'];
                    }
                }
            }
        }
        
        // Return default response if no matches found
        return 'I\'m sorry, I didn\'t understand that. Could you please rephrase your question? You can ask about admission requirements, tuition fees, academic programs, or contact information.';
        
    } catch (Exception $e) {
        error_log("Chatbot Error: " . $e->getMessage());
        return 'I\'m experiencing technical difficulties. Please try again later.';
    }
}

// Set headers
header('Content-Type: application/json');

// Handle the incoming message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get the message from POST data
    $userMessage = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($userMessage)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a message.'
        ]);
        exit;
    }
    
    // Use global $mysqli from database.php
    global $mysqli;
    
    if (!$mysqli) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection not available.'
        ]);
        exit;
    }
    
    $botResponse = getChatbotResponse($userMessage, $mysqli);
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'message' => $botResponse
    ]);
    exit;
}

// If not POST request, return error
echo json_encode([
    'success' => false,
    'message' => 'Invalid request method'
]);
exit;
?>