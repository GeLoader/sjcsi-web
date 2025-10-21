<?php
// setup.php - Database Setup Script
// Run this file once to set up your database

// Include configuration
require_once __DIR__ . '/config.php';

echo "<h2>SJCSI Database Setup</h2>";
echo "<p>Setting up database: " . DB_NAME . "</p>";

try {
    // Connect to MySQL without selecting a database first
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);

    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }

    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($mysqli->query($sql)) {
        echo "<p>✅ Database '" . DB_NAME . "' created successfully or already exists.</p>";
    } else {
        throw new Exception("Error creating database: " . $mysqli->error);
    }

    // Select the database
    $mysqli->select_db(DB_NAME);

    // Read and execute the SQL setup file
    $sqlFile = __DIR__ . '/setup_database.sql';
    
    if (!file_exists($sqlFile)) {
        echo "<p>❌ SQL file not found. Please make sure 'setup_database.sql' exists in the same directory.</p>";
        exit;
    }

    $sqlContent = file_get_contents($sqlFile);
    
    // Split the SQL file into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sqlContent)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt) && !preg_match('/^\s*\/\*/', $stmt);
        }
    );

    echo "<h3>Executing SQL Statements:</h3>";
    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        if (trim($statement)) {
            if ($mysqli->query($statement . ';')) {
                $successCount++;
                // Only show table creation statements
                if (preg_match('/CREATE TABLE.*`?(\w+)`?/i', $statement, $matches)) {
                    echo "<p>✅ Table '{$matches[1]}' created successfully.</p>";
                } elseif (preg_match('/INSERT INTO.*`?(\w+)`?/i', $statement, $matches)) {
                    echo "<p>✅ Data inserted into '{$matches[1]}' table.</p>";
                }
            } else {
                $errorCount++;
                echo "<p>❌ Error executing statement: " . $mysqli->error . "</p>";
                echo "<pre>" . htmlspecialchars(substr($statement, 0, 100)) . "...</pre>";
            }
        }
    }

    echo "<h3>Setup Summary:</h3>";
    echo "<p>✅ Successful operations: $successCount</p>";
    if ($errorCount > 0) {
        echo "<p>❌ Failed operations: $errorCount</p>";
    }

    // Test the database connection using our database class
    echo "<h3>Testing Database Connection:</h3>";
    
    // Include our database class
    include_once __DIR__ . '/database.php';
    
    // Test query
    $testResult = dbQuery("SELECT COUNT(*) as count FROM users");
    $userCount = $testResult->fetch_assoc()['count'];
    
    echo "<p>✅ Database connection successful!</p>";
    echo "<p>✅ Users table contains: $userCount users</p>";

    // Show default login credentials
    echo "<h3>Default Login Credentials:</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;'>";
    echo "<h4>Admin Account:</h4>";
    echo "<p><strong>Email:</strong> admin@sjcsi.edu.ph<br>";
    echo "<strong>Password:</strong> password</p>";
    
    echo "<h4>Department Accounts:</h4>";
    echo "<ul>";
    $departments = ['cit', 'cba', 'caste', 'coa', 'cje', 'shs', 'jhs'];
    foreach ($departments as $dept) {
        echo "<li><strong>$dept@sjcsi.edu.ph</strong> - password: password</li>";
    }
    echo "</ul>";
    
    echo "<h4>Office Accounts:</h4>";
    echo "<ul>";
    echo "<li><strong>registrar@sjcsi.edu.ph</strong> - password: password</li>";
    echo "<li><strong>accounting@sjcsi.edu.ph</strong> - password: password</li>";
    echo "</ul>";
    echo "</div>";

    echo "<p><strong>⚠️ Important:</strong> Change these default passwords immediately after logging in!</p>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Delete or secure this setup.php file</li>";
    echo "<li>Login to the admin dashboard</li>";
    echo "<li>Change all default passwords</li>";
    echo "<li>Configure website settings</li>";
    echo "<li>Add your content</li>";
    echo "</ol>";

    echo "<p><a href='login.php' class='btn btn-primary' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Go to Login Page</a></p>";

    $mysqli->close();

} catch (Exception $e) {
    echo "<p>❌ <strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running</li>";
    echo "<li>Check your database credentials in config.php</li>";
    echo "<li>Ensure the MySQL user has permission to create databases</li>";
    echo "<li>Verify that PHP has mysqli extension enabled</li>";
    echo "</ul>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    line-height: 1.6;
}
h2, h3 {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}
p {
    margin: 10px 0;
}
pre {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    overflow-x: auto;
}
ul, ol {
    margin: 10px 0;
    padding-left: 30px;
}
</style>