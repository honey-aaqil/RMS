<?php
/**
 * GET /api/auth/verify-email?token=xxx
 * 
 * Verify user email address using the token from the verification link.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

$token = $_GET['token'] ?? '';

if (empty($token)) {
    jsonResponse(400, ['success' => false, 'message' => 'Verification token is required.']);
}

try {
    $pdo = getDBConnection();

    // Find user with this token
    $stmt = $pdo->prepare("SELECT id, email, is_verified, token_expires_at FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Invalid verification link.',
            'code'    => 'INVALID_TOKEN'
        ]);
    }

    // Check if already verified
    if ($user['is_verified']) {
        jsonResponse(200, [
            'success' => true,
            'message' => 'Email is already verified. You can login.',
            'code'    => 'ALREADY_VERIFIED'
        ]);
    }

    // Check if token has expired
    if (strtotime($user['token_expires_at']) < time()) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Verification link has expired. Please request a new one.',
            'code'    => 'TOKEN_EXPIRED'
        ]);
    }

    // Mark user as verified and clear token
    $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, token_expires_at = NULL WHERE id = ?");
    $stmt->execute([$user['id']]);

    jsonResponse(200, [
        'success' => true,
        'message' => 'Email verified successfully! You can now login.',
        'code'    => 'VERIFIED'
    ]);

} catch (PDOException $e) {
    error_log("Verify email error: " . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'Server error. Please try again later.']);
}
