<?php
// test_suggestions.php - Direct test of chatbot_get_suggestions.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Suggestions Endpoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .result-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        pre { background: #272822; color: #f8f8f2; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .status-good { color: #28a745; }
        .status-bad { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🧪 Chatbot Suggestions Endpoint Test</h1>
        
        <div class="result-box mb-4">
            <h3>1️⃣ Direct PHP Include Test</h3>
            <?php
            ob_start();
            require_once 'config.php';
            global $mysqli;
            
            if ($mysqli && $mysqli->ping()) {
                echo '<p class="status-good">✅ Database Connected</p>';
                
                // Check table
                $result = $mysqli->query("SHOW TABLES LIKE 'chatbot_responses'");
                if ($result && $result->num_rows > 0) {
                    echo '<p class="status-good">✅ Table exists</p>';
                    
                    // Count total
                    $result = $mysqli->query("SELECT COUNT(*) as total FROM chatbot_responses");
                    $row = $result->fetch_assoc();
                    echo '<p>Total responses: <strong>' . $row['total'] . '</strong></p>';
                    
                    // Count suggested
                    $result = $mysqli->query("SELECT COUNT(*) as total FROM chatbot_responses WHERE is_suggested = 1");
                    $row = $result->fetch_assoc();
                    echo '<p>Suggested responses: <strong>' . $row['total'] . '</strong></p>';
                    
                    if ($row['total'] == 0) {
                        echo '<div class="alert alert-warning mt-3">';
                        echo '<strong>⚠️ No suggestions marked!</strong> Run this SQL:<br>';
                        echo '<code>UPDATE chatbot_responses SET is_suggested = 1 WHERE id IN (1,2,3,4);</code>';
                        echo '</div>';
                    }
                    
                    // Get sample data
                    $result = $mysqli->query("SELECT id, keywords, LEFT(response, 50) as response_preview, is_suggested FROM chatbot_responses LIMIT 5");
                    if ($result && $result->num_rows > 0) {
                        echo '<table class="table table-sm table-striped mt-3">';
                        echo '<thead><tr><th>ID</th><th>Keywords Preview</th><th>Response Preview</th><th>Suggested</th></tr></thead>';
                        echo '<tbody>';
                        while ($row = $result->fetch_assoc()) {
                            $keywords = json_decode($row['keywords'], true);
                            $keyword_preview = is_array($keywords) ? implode(', ', array_slice($keywords, 0, 2)) : 'N/A';
                            echo '<tr class="' . ($row['is_suggested'] ? 'table-success' : '') . '">';
                            echo '<td>' . $row['id'] . '</td>';
                            echo '<td><small>' . htmlspecialchars($keyword_preview) . '</small></td>';
                            echo '<td><small>' . htmlspecialchars($row['response_preview']) . '...</small></td>';
                            echo '<td>' . ($row['is_suggested'] ? '✅ Yes' : '❌ No') . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    }
                    
                } else {
                    echo '<p class="status-bad">❌ Table does not exist</p>';
                }
            } else {
                echo '<p class="status-bad">❌ Database NOT connected</p>';
            }
            
            $php_output = ob_get_clean();
            echo $php_output;
            ?>
        </div>
        
        <div class="result-box mb-4">
            <h3>2️⃣ API Endpoint Test</h3>
            <p>Testing: <code>chatbot_get_suggestions.php</code></p>
            
            <div id="api-test-result">
                <p><i class="fas fa-spinner fa-spin"></i> Testing API...</p>
            </div>
        </div>
        
        <div class="result-box mb-4">
            <h3>3️⃣ JavaScript Fetch Test</h3>
            <button onclick="testFetch()" class="btn btn-primary">Run Fetch Test</button>
            <div id="fetch-test-result" class="mt-3"></div>
        </div>
        
        <div class="text-center mt-4">
            <a href="test_chatbot_system.php" class="btn btn-secondary">← Full System Test</a>
            <a href="index.php" class="btn btn-primary">Go to Website →</a>
        </div>
    </div>

    <script>
        // Test API with fetch
        async function testAPI() {
            const resultDiv = document.getElementById('api-test-result');
            
            try {
                const response = await fetch('chatbot_get_suggestions.php', {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                
                const text = await response.text();
                console.log('Raw response:', text);
                
                let html = '<p class="status-good">✅ API responded with status: ' + response.status + '</p>';
                
                try {
                    const data = JSON.parse(text);
                    html += '<p><strong>Response:</strong></p>';
                    html += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                    
                    if (data.success && data.suggestions && data.suggestions.length > 0) {
                        html += '<p class="status-good">✅ Found ' + data.suggestions.length + ' suggestions</p>';
                    } else {
                        html += '<p class="status-bad">⚠️ No suggestions returned</p>';
                    }
                } catch (e) {
                    html += '<p class="status-bad">❌ Invalid JSON response:</p>';
                    html += '<pre>' + text + '</pre>';
                }
                
                resultDiv.innerHTML = html;
                
            } catch (error) {
                resultDiv.innerHTML = '<p class="status-bad">❌ Error: ' + error.message + '</p>';
            }
        }
        
        // Manual fetch test
        function testFetch() {
            const resultDiv = document.getElementById('fetch-test-result');
            resultDiv.innerHTML = '<p><i class="fas fa-spinner fa-spin"></i> Testing...</p>';
            
            console.log('Starting fetch test...');
            
            fetch('chatbot_get_suggestions.php')
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.text();
                })
                .then(text => {
                    console.log('Response text:', text);
                    
                    let html = '<p class="status-good">✅ Fetch successful</p>';
                    html += '<p><strong>Response:</strong></p>';
                    html += '<pre>' + text + '</pre>';
                    
                    try {
                        const data = JSON.parse(text);
                        html += '<p class="status-good">✅ Valid JSON</p>';
                        html += '<p>Success: ' + data.success + '</p>';
                        html += '<p>Count: ' + (data.count || 0) + '</p>';
                        html += '<p>Suggestions: ' + (data.suggestions ? data.suggestions.length : 0) + '</p>';
                    } catch (e) {
                        html += '<p class="status-bad">❌ Invalid JSON</p>';
                    }
                    
                    resultDiv.innerHTML = html;
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    resultDiv.innerHTML = '<p class="status-bad">❌ Fetch failed: ' + error.message + '</p>';
                });
        }
        
        // Auto-run API test on load
        window.addEventListener('DOMContentLoaded', testAPI);
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>