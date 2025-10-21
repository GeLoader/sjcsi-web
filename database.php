<?php
// database.php - MySQL Database Connection using MySQLi for PHP 7.4

// Error reporting for development (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database {
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    
    public function __construct() {
        $this->host = DB_HOST;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
        
        $this->connect();
    }
    
    private function connect() {
        try {
            // Create connection using MySQLi
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            // Check connection
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            // Set charset to UTF-8
            $this->connection->set_charset("utf8");
            
        } catch (Exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Execute a query and return result (simple query without parameters)
    public function query($sql) {
        try {
            $result = $this->connection->query($sql);
            if ($this->connection->error) {
                throw new Exception("Query Error: " . $this->connection->error);
            }
            return $result;
        } catch (Exception $e) {
            die("Query Error: " . $e->getMessage());
        }
    }
    
    // Execute query with parameters (prepared statement)
    public function queryWithParams($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare Error: " . $this->connection->error);
            }
            
            if (!empty($params)) {
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                }
                $stmt->bind_param($types, ...$params);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Execute Error: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            return $result;
            
        } catch (Exception $e) {
            error_log("Database Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }
    
    // Prepare and execute a statement
    public function prepare($sql) {
        try {
            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare Error: " . $this->connection->error);
            }
            return $stmt;
        } catch (Exception $e) {
            die("Prepare Error: " . $e->getMessage());
        }
    }
    
    // Execute prepared statement and return result
    public function execute($stmt, $params = []) {
        try {
            if (!empty($params)) {
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                }
                $stmt->bind_param($types, ...$params);
            }
            
            $result = $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Execute Error: " . $stmt->error);
            }
            return $stmt;
        } catch (Exception $e) {
            die("Execute Error: " . $e->getMessage());
        }
    }
    
    // Get last inserted ID
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }
    
    // Get affected rows
    public function getAffectedRows() {
        return $this->connection->affected_rows;
    }
    
    // Escape string
    public function escapeString($string) {
        return $this->connection->real_escape_string($string);
    }
    
    // Close connection
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    // Destructor to close connection
    public function __destruct() {
        $this->close();
    }
}

// Create global database instance
try {
    $db = new Database();
    $mysqli = $db->getConnection();
} catch (Exception $e) {
    die("Database initialization failed: " . $e->getMessage());
}

// Updated helper functions for common database operations
function dbQuery($sql, $params = []) {
    global $db;
    
    // If no parameters, use simple query
    if (empty($params)) {
        return $db->query($sql);
    }
    
    // If parameters provided, use prepared statement
    return $db->queryWithParams($sql, $params);
}

function dbPrepare($sql) {
    global $db;
    return $db->prepare($sql);
}

function dbExecute($stmt, $params = []) {
    global $db;
    return $db->execute($stmt, $params);
}

function dbEscape($string) {
    global $db;
    return $db->escapeString($string);
}

function dbLastInsertId() {
    global $db;
    return $db->getLastInsertId();
}

function dbAffectedRows() {
    global $db;
    return $db->getAffectedRows();
}
?>