<?php
// chatbot_get_suggestions.php - Get suggested chatbot questions from database only
 
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
    
    // Fetch only responses marked as suggested (is_suggested = 1)
    // Order by id and limit to 6 suggestions for better UI
    $stmt = $mysqli->prepare("SELECT id, keywords, response FROM chatbot_responses WHERE is_suggested = 1 ORDER BY id ASC LIMIT 6");
    
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $mysqli->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $keywords = json_decode($row['keywords'], true);
        
        // Create user-friendly question text from keywords
        $suggestionText = generateSuggestionText($keywords);
        
        $suggestions[] = [
            'id' => $row['id'],
            'text' => $suggestionText,
            'response' => $row['response'],
            'keywords' => $keywords
        ];
    }
    
    $stmt->close();
    
    // Return suggestions (empty array if none found)
    echo json_encode([
        'success' => true,
        'count' => count($suggestions),
        'suggestions' => $suggestions
    ]);
    
} catch (Exception $e) {
    error_log("Get Suggestions Error: " . $e->getMessage());
    
    // Return empty suggestions on error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'suggestions' => []
    ]);
}

// Generate user-friendly suggestion text from keywords
function generateSuggestionText($keywords) {
    // If we have keywords, use the most descriptive one
    if (is_array($keywords) && !empty($keywords)) {
        // Prefer longer keywords as they're more descriptive
        usort($keywords, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        
        $firstKeyword = ucfirst(trim($keywords[0]));
        
        // Format as a question if appropriate
        if (strlen($firstKeyword) < 30) {
            return $firstKeyword;
        }
    }
    
    return 'Ask a question';
}

ob_end_flush();
?>