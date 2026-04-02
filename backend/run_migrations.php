<?php
require_once __DIR__ . '/config/db.php';

try {
    $pdo = getDBConnection();
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "Tables do not exist. Running migrations...\n";
        $sql = file_get_contents(__DIR__ . '/migrations/schema.sql');
        $pdo->exec($sql);
        echo "Schema created successfully.\n";
    } else {
        echo "Tables already exist.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
