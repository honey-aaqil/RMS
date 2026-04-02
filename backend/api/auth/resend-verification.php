<?php
/**
 * POST /api/auth/resend-verification
 * 
 * Regenerate verification token and resend the verification email.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/mailer.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

$data = getRequestBody();

$error = validateRequired($data, ['email']);
if ($error) {
    jsonResponse(400, ['success' => false, 'message' => $error]);
}

$email = trim(strtolower($data['email']));

try {
    $pdo = getDBConnection();

    // Find user
    $stmt = $pdo->prepare("SELECT id, name, is_verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // Don't reveal if email exists or not
        jsonResponse(200, [
            'success' => true,
            'message' => 'If an account exists with this email, a verification link has been sent.'
        ]);
    }

    if ($user['is_verified']) {
        jsonResponse(400, [
            'success' => false,
            'message' => 'Email is already verified. You can login directly.'
        ]);
    }

    // Regenerate token
    $token     = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $stmt = $pdo->prepare("UPDATE users SET verification_token = ?, token_expires_at = ? WHERE id = ?");
    $stmt->execute([$token, $expiresAt, $user['id']]);

    // Resend email
    $sent = sendVerificationEmail($email, $user['name'], $token);
    if (!$sent) {
        jsonResponse(500, ['success' => false, 'message' => 'Failed to send verification email. Please try again.']);
    }

    jsonResponse(200, [
        'success' => true,
        'message' => 'Verification email resent. Please check your inbox.'
    ]);

} catch (PDOException $e) {
    error_log("Resend verification error: " . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'Server error. Please try again later.']);
}
