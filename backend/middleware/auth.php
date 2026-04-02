<?php
/**
 * RecruiterMS — JWT Authentication Middleware
 * 
 * Validates JWT tokens from Authorization header.
 * Uses firebase/php-jwt library.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

function authenticate(): array {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Access denied. No token provided.'
        ]);
        exit;
    }

    $token = trim(str_replace('Bearer', '', $authHeader));
    $secret = $_ENV['JWT_SECRET'] ?? 'default-secret-key';

    try {
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        return (array) $decoded->data;
    } catch (ExpiredException $e) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Token has expired. Please login again.'
        ]);
        exit;
    } catch (\Exception $e) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid token.',
            'error'   => $e->getMessage()
        ]);
        exit;
    }
}

function generateJWT(array $userData): string {
    $secret   = $_ENV['JWT_SECRET'] ?? 'default-secret-key';
    $issuedAt = time();
    $expire   = $issuedAt + (7 * 24 * 60 * 60); // 7 days

    $payload = [
        'iss'  => 'recruiterms',
        'iat'  => $issuedAt,
        'exp'  => $expire,
        'data' => $userData
    ];

    return JWT::encode($payload, $secret, 'HS256');
}
