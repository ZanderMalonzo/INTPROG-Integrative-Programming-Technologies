<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../app/config/db.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Valdatee and sanitize user_id
$user_id = (int)$_SESSION['user_id'];
if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid user session']);
    exit;
}

// Check if user is aadmin
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Admin privileges required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Get all orders from all userss
        $stmt = $pdo->prepare("
            SELECT o.order_id, o.user_id, o.payment_method, o.total_amount, 
                   o.delivery_address, o.full_name, o.phone_number, o.order_status, 
                   o.created_at, u.username, u.email
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.order_id ASC, o.created_at ASC
        ");
        $stmt->execute();
        $orders = $stmt->fetchAll();
        
        // Get itemss forr each order
        foreach ($orders as &$order) {
            $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ? ORDER BY item_id ASC");
            $stmt->execute([$order['order_id']]);
            $order['items'] = $stmt->fetchAll();
        }
        
        echo json_encode([
            'success' => true,
            'orders' => $orders,
            'count' => count($orders)
        ]);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    error_log("Database error in admin_orders.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => 'Failed to fetch orders.',
        'details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Server error in admin_orders.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => 'Failed to process request.',
        'details' => $e->getMessage()
    ]);
}
?>

