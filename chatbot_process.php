<?php
// chatbot_process.php - Process chatbot messages and return responses
ob_start();

require_once 'config.php';

// Set headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Check database connection
    if (!$mysqli) {
        throw new Exception("Database connection failed");
    }

    // Get the message from POST data
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($message)) {
        throw new Exception("No message provided");
    }

    // Fetch all responses to find the best match
    $stmt = $mysqli->prepare("SELECT id, keywords, response FROM chatbot_responses WHERE is_suggested IN (0,1)");
    
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $mysqli->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bestMatch = null;
    $bestScore = 0;
    
    while ($row = $result->fetch_assoc()) {
        $keywords = json_decode($row['keywords'], true);
        
        if (is_array($keywords)) {
            foreach ($keywords as $keyword) {
                $score = similar_text(strtolower($message), strtolower($keyword));
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $row;
                }
                
                // Also check for exact matches in the message
                if (stripos($message, $keyword) !== false) {
                    $bestMatch = $row;
                    $bestScore = 100; // High score for exact match
                    break 2;
                }
            }
        }
    }
    
    $stmt->close();
    
    if ($bestMatch && $bestScore > 30) { // Threshold for matching
        $response = $bestMatch['response'];
    } else {
        $response = "I'm sorry, I don't have an answer for that question. Please contact the school administration for more specific inquiries.";
    }
    
    echo json_encode([
        'success' => true,
        'response' => $response,
        'match_score' => $bestScore
    ]);
    
} catch (Exception $e) {
    error_log("Chatbot Process Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'response' => "I'm experiencing technical difficulties. Please try again later or contact the school directly."
    ]);
}

ob_end_flush();
?>