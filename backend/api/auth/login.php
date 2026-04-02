<?php
/**
 * POST /api/auth/login
 * 
 * Authenticate user and return JWT token. Requires verified email.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

$data = getRequestBody();

// Validate required fields
$error = validateRequired($data, ['email', 'password']);
if ($error) {
    jsonResponse(400, ['success' => false, 'message' => $error]);
}

$email    = trim(strtolower($data['email']));
$password = $data['password'];

try {
    $pdo = getDBConnection();

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT id, name, email, password, role, is_verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(401, ['success' => false, 'message' => 'Invalid email or password.']);
    }

    // Check if email is verified
    if (!$user['is_verified']) {
        jsonResponse(403, [
            'success' => false,
            'message' => 'Email not verified. Please check your inbox or request a new verification link.',
            'code'    => 'EMAIL_NOT_VERIFIED'
        ]);
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        jsonResponse(401, ['success' => false, 'message' => 'Invalid email or password.']);
    }

    // Generate JWT
    $tokenData = [
        'id'    => $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role']
    ];

    $jwt = generateJWT($tokenData);

    jsonResponse(200, [
        'success' => true,
        'message' => 'Login successful.',
        'token'   => $jwt,
        'user'    => [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role']
        ]
    ]);

} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'Server error. Please try again later.']);
}
