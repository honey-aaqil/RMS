<?php
/**
 * RecruiterMS — TiDB Cloud Database Connection
 * 
 * PDO connection with SSL support for TiDB Cloud.
 * Reads credentials from environment variables loaded via .env
 */

// Load .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

function getDBConnection(): PDO {
    $host     = $_ENV['TIDB_HOST'] ?? 'localhost';
    $port     = $_ENV['TIDB_PORT'] ?? '4000';
    $dbname   = $_ENV['TIDB_DB'] ?? 'recruiter_db';
    $user     = $_ENV['TIDB_USER'] ?? 'root';
    $password = $_ENV['TIDB_PASSWORD'] ?? '';
    $sslCA    = $_ENV['TIDB_SSL_CA'] ?? '';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Add SSL CA certificate if configured
    if (!empty($sslCA)) {
        $sslPath = realpath(__DIR__ . '/../' . $sslCA) ?: $sslCA;
        $options[PDO::MYSQL_ATTR_SSL_CA] = $sslPath;
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    }

    try {
        $pdo = new PDO($dsn, $user, $password, $options);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed',
            'error'   => $e->getMessage()
        ]);
        exit;
    }
}
