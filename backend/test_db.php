<?php
require_once __DIR__ . '/config/db.php';
try {
    $pdo = getDBConnection();
    echo "Connected successfully to TiDB Cloud!\n";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
