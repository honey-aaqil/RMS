<?php
/**
 * RecruiterMS — CORS & Response Helpers
 */

function setCorsHeaders(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    // Allow any localhost origin or the specific APP_URL
    $allowedOrigin = $_ENV['APP_URL'] ?? 'http://localhost:5173';
    if (preg_match('/^http:\/\/localhost:\d+$/', $origin)) {
        $allowedOrigin = $origin;
    }
    
    header("Access-Control-Allow-Origin: {$allowedOrigin}");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json; charset=UTF-8");

    // Handle preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

function jsonResponse(int $code, array $data): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getRequestBody(): array {
    $body = file_get_contents('php://input');
    return json_decode($body, true) ?? [];
}

function validateRequired(array $data, array $fields): ?string {
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            return "Field '{$field}' is required.";
        }
    }
    return null;
}
