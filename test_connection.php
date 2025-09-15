<?php
// test_connection.php
require 'config/db.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<h2>✅ Database Connection Successful!</h2>";
    echo "<p>Connected to database: event_db</p>";
    
    // Check if tables exist
    $tables = ['users', 'events', 'registrations'];
    
    foreach ($tables as $table) {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        if ($result !== false) {
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p>❌ Table '$table' does not exist</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Database Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config/db.php</p>";
}
?>