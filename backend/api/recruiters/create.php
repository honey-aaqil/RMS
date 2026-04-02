<?php
/**
 * POST /api/recruiters
 * 
 * Create a new recruiter profile. JWT protected.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

// Authenticate
$authUser = authenticate();

$data = getRequestBody();

// Validate required fields
$error = validateRequired($data, ['user_id', 'company_name', 'position']);
if ($error) {
    jsonResponse(400, ['success' => false, 'message' => $error]);
}

$userId      = intval($data['user_id']);
$companyName = trim($data['company_name']);
$position    = trim($data['position']);
$phone       = trim($data['phone'] ?? '');

try {
    $pdo = getDBConnection();

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        jsonResponse(404, ['success' => false, 'message' => 'User not found.']);
    }

    // Check if recruiter profile already exists for this user
    $stmt = $pdo->prepare("SELECT id FROM recruiters WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) {
        jsonResponse(409, ['success' => false, 'message' => 'Recruiter profile already exists for this user.']);
    }

    // Insert recruiter
    $stmt = $pdo->prepare("INSERT INTO recruiters (user_id, company_name, position, phone, status) VALUES (?, ?, ?, ?, 'active')");
    $stmt->execute([$userId, $companyName, $position, $phone]);

    $recruiterId = $pdo->lastInsertId();

    jsonResponse(201, [
        'success' => true,
        'message' => 'Recruiter created successfully.',
        'data'    => [
            'id'           => (int) $recruiterId,
            'user_id'      => $userId,
            'company_name' => $companyName,
            'position'     => $position,
            'phone'        => $phone,
            'status'       => 'active'
        ]
    ]);

} catch (PDOException $e) {
    error_log("Create recruiter error: " . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'Server error.']);
}
