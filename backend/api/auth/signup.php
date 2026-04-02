<?php
/**
 * POST /api/auth/signup
 * 
 * Register a new user, generate verification token, send verification email.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/mailer.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

$data = getRequestBody();

// Validate required fields
$error = validateRequired($data, ['name', 'email', 'password', 'confirm_password']);
if ($error) {
    jsonResponse(400, ['success' => false, 'message' => $error]);
}

$name     = trim($data['name']);
$email    = trim(strtolower($data['email']));
$password = $data['password'];
$confirm  = $data['confirm_password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(400, ['success' => false, 'message' => 'Invalid email address.']);
}

// Validate password length
if (strlen($password) < 8) {
    jsonResponse(400, ['success' => false, 'message' => 'Password must be at least 8 characters.']);
}

// Confirm passwords match
if ($password !== $confirm) {
    jsonResponse(400, ['success' => false, 'message' => 'Passwords do not match.']);
}

try {
    $pdo = getDBConnection();

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        if ($existingUser['is_verified']) {
            jsonResponse(409, ['success' => false, 'message' => 'Email already registered.']);
        } else {
            // User exists but not verified — regenerate token
            $token     = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $stmt = $pdo->prepare("UPDATE users SET verification_token = ?, token_expires_at = ?, name = ?, password = ? WHERE email = ?");
            $stmt->execute([$token, $expiresAt, $name, password_hash($password, PASSWORD_BCRYPT), $email]);

            $sent = sendVerificationEmail($email, $name, $token);
            if (!$sent) {
                jsonResponse(500, ['success' => false, 'message' => 'Failed to send verification email. Please try again.']);
            }

            jsonResponse(200, [
                'success' => true,
                'message' => 'Verification email sent. Please check your inbox.',
                'email'   => $email
            ]);
        }
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

    // Generate verification token
    $token     = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_verified, verification_token, token_expires_at) VALUES (?, ?, ?, 'recruiter', 0, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword, $token, $expiresAt]);

    // Send verification email via Brevo
    $sent = sendVerificationEmail($email, $name, $token);
    if (!$sent) {
        jsonResponse(500, ['success' => false, 'message' => 'Account created but failed to send verification email. Please request a new one.']);
    }

    jsonResponse(201, [
        'success' => true,
        'message' => 'Verification email sent. Please check your inbox.',
        'email'   => $email
    ]);

} catch (PDOException $e) {
    error_log("Signup error: " . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'Server error. Please try again later.']);
}
