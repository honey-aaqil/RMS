<?php
/**
 * GET /api/recruiters
 * 
 * List all recruiters with pagination. JWT protected.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed']);
}

// Authenticate
$user = authenticate();

try {
    $pdo = getDBConnection();

    // Pagination params
    $page    = max(1, intval($_GET['page'] ?? 1));
    $limit   = min(50, max(1, intval($_GET['limit'] ?? 10)));
    $offset  = ($page - 1) * $limit;
    $search  = $_GET['search'] ?? '';
    $status  = $_GET['status'] ?? '';

    // Build query
    $where  = [];
    $params = [];

    if (!empty($search)) {
        $where[]  = "(u.name LIKE ? OR r.company_name LIKE ? OR r.position LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if (!empty($status) && in_array($status, ['active', 'inactive'])) {
        $where[]  = "r.status = ?";
        $params[] = $status;
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get total count
    $countSQL = "SELECT COUNT(*) as total FROM recruiters r JOIN users u ON r.user_id = u.id {$whereClause}";
    $stmt = $pdo->prepare($countSQL);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];

    // Get paginated data
    $dataSQL = "SELECT r.id, r.user_id, u.name, u.email, r.company_name, r.position, r.phone, r.status, r.created_at 
                FROM recruiters r 
                JOIN users u ON r.user_id = u.id 
                {$whereClause}
                ORDER BY r.created_at DESC 
                LIMIT ? OFFSET ?";
    
    $dataParams = array_merge($params, [$limit, $offset]);
    $stmt = $pdo->prepare($dataSQL);
    $stmt->execute($dataParams);
    $recruiters = $stmt->fetchAll();

    jsonResponse(200, [
        'success'    => true,
        'data'       => $recruiters,
        'pagination' => [
            'page'       => $page,
            'limit'      => $limit,
            'total'      => (int) $total,
            'totalPages' => ceil($total / $limit)
        ]
    ]);

} catch (PDOException $e) {
    error_log("List recruiters error: " . $e->getMessage());
    jsonResponse(500, ['success' => false, 'message' => 'Server error.']);
}
