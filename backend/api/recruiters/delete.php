<?php
/**
 * DELETE /api/recruiters/{id}
 * 
 * Soft delete a recruiter (set status = inactive). JWT protected.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

// Authenticate
$authUser = authenticate();

// Get recruiter ID from URL
$requestUri = $_SERVER['REQUEST_URI'];
preg_match('/\/api\/recruiters\/(\d+)/', $requestUri, $matches);
$recruiterId = intval($matches[1] ?? 0);

if ($recruiterId <= 0) {
    jsonResponse(400, ['success' => false, 'message' => 'Invalid recruiter ID.']);
}

try {
    $pdo = getDBConnection();

    // Check if recruiter exists
    $stmt = $pdo->prepare("SELECT id, status FROM recruiters WHERE id = ?");
    $stmt->execute([$recruiterId]);
    $recruiter = $stmt->fetch();

    if (!$recruiter) {
        jsonResponse(404, ['success' => false, 'message' => 'Recruiter not found.']);
    }

    if ($recruiter['status'] === 'inactive') {
        jsonResponse(400, ['success' => false, 'message' => 'Recruiter is already inactive.']);
    }

    // Soft delete — set status to inactive
    $stmt = $pdo->prepare("UPDATE recruiters SET status = 'inactive' WHERE id = ?");
    $stmt->execute([$recruiterId]);

    jsonResponse(200, [
        'success' => true,
        'message' => 'Recruiter deactivated successfully.'
    ]);

} catch (PDOException $e) {
    error_log("Delete recruiter error: " . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'Server error.']);
}
