<?php
/**
 * PUT /api/recruiters/{id}
 * 
 * Update a recruiter profile. JWT protected.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

$data = getRequestBody();

try {
    $pdo = getDBConnection();

    // Check if recruiter exists
    $stmt = $pdo->prepare("SELECT id, user_id FROM recruiters WHERE id = ?");
    $stmt->execute([$recruiterId]);
    $recruiter = $stmt->fetch();

    if (!$recruiter) {
        jsonResponse(404, ['success' => false, 'message' => 'Recruiter not found.']);
    }

    // Build dynamic update
    $updates = [];
    $params  = [];

    if (isset($data['company_name'])) {
        $updates[] = "company_name = ?";
        $params[]  = trim($data['company_name']);
    }
    if (isset($data['position'])) {
        $updates[] = "position = ?";
        $params[]  = trim($data['position']);
    }
    if (isset($data['phone'])) {
        $updates[] = "phone = ?";
        $params[]  = trim($data['phone']);
    }
    if (isset($data['status']) && in_array($data['status'], ['active', 'inactive'])) {
        $updates[] = "status = ?";
        $params[]  = $data['status'];
    }

    if (empty($updates)) {
        jsonResponse(400, ['success' => false, 'message' => 'No fields to update.']);
    }

    $params[] = $recruiterId;
    $sql = "UPDATE recruiters SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Fetch updated record
    $stmt = $pdo->prepare("SELECT r.*, u.name, u.email FROM recruiters r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
    $stmt->execute([$recruiterId]);
    $updated = $stmt->fetch();

    jsonResponse(200, [
        'success' => true,
        'message' => 'Recruiter updated successfully.',
        'data'    => $updated
    ]);

} catch (PDOException $e) {
    error_log("Update recruiter error: " . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'Server error.']);
}
