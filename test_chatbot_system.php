<?php
// test_chatbot_system.php - Complete system test for chatbot
error_reporting(E_ALL);
ini_set('display_errors', 1);

$tests = [];
$overall_status = true;

// Test 1: Check if config.php exists and loads
try {
    require_once 'config.php';
    $tests['config'] = ['status' => 'pass', 'message' => 'config.php loaded successfully'];
} catch (Exception $e) {
    $tests['config'] = ['status' => 'fail', 'message' => 'config.php error: ' . $e->getMessage()];
    $overall_status = false;
}

// Test 2: Check database connection (using $mysqli from database.php)
global $mysqli;
if (isset($mysqli) && $mysqli && $mysqli->ping()) {
    $tests['database'] = ['status' => 'pass', 'message' => 'Database connected: ' . $mysqli->server_info];
} else {
    $tests['database'] = ['status' => 'fail', 'message' => 'Database connection failed or not available'];
    $overall_status = false;
}

// Test 3: Check if chatbot_responses table exists
if (isset($mysqli) && $mysqli) {
    $result = $mysqli->query("SHOW TABLES LIKE 'chatbot_responses'");
    if ($result && $result->num_rows > 0) {
        $tests['table'] = ['status' => 'pass', 'message' => 'chatbot_responses table exists'];
    } else {
        $tests['table'] = ['status' => 'fail', 'message' => 'chatbot_responses table NOT found'];
        $overall_status = false;
    }
}

// Test 4: Check if is_suggested column exists
if (isset($mysqli) && $mysqli) {
    $result = $mysqli->query("SHOW COLUMNS FROM chatbot_responses LIKE 'is_suggested'");
    if ($result && $result->num_rows > 0) {
        $tests['column'] = ['status' => 'pass', 'message' => 'is_suggested column exists'];
    } else {
        $tests['column'] = ['status' => 'fail', 'message' => 'is_suggested column missing - run ALTER TABLE command'];
        $overall_status = false;
    }
}

// Test 5: Count total responses
$total_responses = 0;
$suggested_responses = 0;
if (isset($mysqli) && $mysqli) {
    $result = $mysqli->query("SELECT COUNT(*) as total FROM chatbot_responses");
    if ($result) {
        $row = $result->fetch_assoc();
        $total_responses = $row['total'];
        $tests['count'] = ['status' => 'pass', 'message' => "Found {$total_responses} total responses"];
    }
    
    // Count suggested responses
    $result = $mysqli->query("SELECT COUNT(*) as total FROM chatbot_responses WHERE is_suggested = 1");
    if ($result) {
        $row = $result->fetch_assoc();
        $suggested_responses = $row['total'];
        if ($suggested_responses > 0) {
            $tests['suggested'] = ['status' => 'pass', 'message' => "{$suggested_responses} responses marked as suggested"];
        } else {
            $tests['suggested'] = ['status' => 'warning', 'message' => 'No responses marked as suggested - chatbot will show "No quick questions available"'];
        }
    }
}

// Test 6: Check if required PHP files exist
$required_files = ['chatbot.php', 'chatbot_get_suggestions.php', 'chatbot_toggle_suggestion.php', 'footer.php'];
$missing_files = [];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        $missing_files[] = $file;
    }
}
if (empty($missing_files)) {
    $tests['files'] = ['status' => 'pass', 'message' => 'All required PHP files exist'];
} else {
    $tests['files'] = ['status' => 'fail', 'message' => 'Missing files: ' . implode(', ', $missing_files)];
    $overall_status = false;
}

// Test 7: Test chatbot_get_suggestions.php
$suggestions_data = null;
if (file_exists('chatbot_get_suggestions.php')) {
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['REQUEST_URI']) . '/chatbot_get_suggestions.php';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $suggestions_data = json_decode($response, true);
        if ($suggestions_data && isset($suggestions_data['success'])) {
            $count = isset($suggestions_data['count']) ? $suggestions_data['count'] : 0;
            $tests['api'] = ['status' => 'pass', 'message' => "API working - returned {$count} suggestions"];
        } else {
            $tests['api'] = ['status' => 'fail', 'message' => 'API returned invalid JSON: ' . substr($response, 0, 100)];
            $overall_status = false;
        }
    } else {
        $tests['api'] = ['status' => 'fail', 'message' => "API returned HTTP {$http_code}"];
        $overall_status = false;
    }
}

// Get sample data for display
$sample_data = [];
if (isset($mysqli) && $mysqli && $total_responses > 0) {
    $result = $mysqli->query("SELECT id, keywords, LEFT(response, 100) as response_preview, is_suggested FROM chatbot_responses ORDER BY is_suggested DESC, id ASC LIMIT 10");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sample_data[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-pass { color: #28a745; }
        .status-fail { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .test-card { margin-bottom: 1rem; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .main-container { padding: 2rem 0; }
    </style>
</head>
<body>
    <div class="container main-container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg">
                    <div class="card-header bg-<?= $overall_status ? 'success' : 'danger' ?> text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-<?= $overall_status ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                            Chatbot System Test Results
                        </h3>
                        <small>Database Connection: Using <code>$mysqli</code> from database.php</small>
                    </div>
                    <div class="card-body">
                        <?php foreach ($tests as $test_name => $test_result): ?>
                            <div class="test-card card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-<?= $test_result['status'] == 'pass' ? 'check-circle status-pass' : ($test_result['status'] == 'warning' ? 'exclamation-circle status-warning' : 'times-circle status-fail') ?>"></i>
                                        <?= ucfirst(str_replace('_', ' ', $test_name)) ?> Test
                                    </h5>
                                    <p class="card-text mb-0"><?= htmlspecialchars($test_result['message']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!empty($sample_data)): ?>
                <div class="card shadow-lg mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-database"></i> Database Content (Top 10)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Keywords</th>
                                        <th>Response Preview</th>
                                        <th>Suggested</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sample_data as $row): ?>
                                        <tr class="<?= $row['is_suggested'] ? 'table-success' : '' ?>">
                                            <td><strong><?= $row['id'] ?></strong></td>
                                            <td>
                                                <?php 
                                                $keywords = json_decode($row['keywords'], true);
                                                if (is_array($keywords)) {
                                                    echo '<small class="text-muted">' . htmlspecialchars(implode(', ', array_slice($keywords, 0, 3))) . '</small>';
                                                }
                                                ?>
                                            </td>
                                            <td><small><?= htmlspecialchars($row['response_preview']) ?>...</small></td>
                                            <td>
                                                <?php if ($row['is_suggested']): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check"></i> Yes</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mb-0 mt-3">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Total:</strong> <?= $total_responses ?> responses | 
                            <strong>Suggested:</strong> <?= $suggested_responses ?> 
                            (highlighted in green)
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($suggestions_data): ?>
                <div class="card shadow-lg mt-4">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-comments"></i> API Response from chatbot_get_suggestions.php</h4>
                    </div>
                    <div class="card-body">
                        <pre class="bg-dark text-light p-3 rounded"><?= json_encode($suggestions_data, JSON_PRETTY_PRINT) ?></pre>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card shadow-lg mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="fas fa-wrench"></i> Quick Fixes & Actions</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($suggested_responses == 0 && $total_responses > 0): ?>
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-exclamation-triangle"></i> No Suggested Responses!</h5>
                                <p class="mb-2">Your chatbot won't show any quick questions. Run this SQL to enable some:</p>
                                <pre class="mb-0 bg-light p-3 rounded border"><code>UPDATE chatbot_responses SET is_suggested = 1 WHERE id IN (1,2,3,4);</code></pre>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($total_responses == 0): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-times-circle"></i> No Responses in Database!</h5>
                                <p class="mb-2">Run the full SQL script to insert sample data. Check the artifacts for "Database Setup with is_suggested Column".</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!isset($tests['column']) || $tests['column']['status'] == 'fail'): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-database"></i> Missing Column!</h5>
                                <p class="mb-2">Run this SQL to add the <code>is_suggested</code> column:</p>
                                <pre class="mb-0 bg-light p-3 rounded border"><code>ALTER TABLE chatbot_responses ADD COLUMN is_suggested tinyint(1) DEFAULT 0 AFTER response;</code></pre>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!isset($tests['table']) || $tests['table']['status'] == 'fail'): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-table"></i> Table Missing!</h5>
                                <p class="mb-2">Run the SQL script to create the <code>chatbot_responses</code> table. Check the artifacts.</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-<?= $overall_status ? 'success' : 'info' ?> mb-0">
                            <h5><i class="fas fa-<?= $overall_status ? 'check-circle' : 'info-circle' ?>"></i> Status</h5>
                            <?php if ($overall_status): ?>
                                <p class="mb-0">✅ All systems operational! Your chatbot should be working correctly. Visit your website to test it.</p>
                            <?php else: ?>
                                <p class="mb-0">⚠️ Please fix the issues above and refresh this page to re-test.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="index.php" class="btn btn-primary btn-lg"><i class="fas fa-home"></i> Go to Homepage</a>
                    <button onclick="location.reload()" class="btn btn-secondary btn-lg"><i class="fas fa-sync"></i> Refresh Test</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>