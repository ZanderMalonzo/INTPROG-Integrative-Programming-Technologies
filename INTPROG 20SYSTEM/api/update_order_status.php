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

// Validate and sanitize user_id
$user_id = (int)$_SESSION['user_id'];
if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid user session']);
    exit;
}

// Check if user is admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || strtolower(trim($user['role'])) !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Admin privileges required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['order_id']) || !isset($input['order_status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID and status are required']);
            exit;
        }
        
        $order_id = (int)$input['order_id'];
        $order_status = trim($input['order_status']);
        
        // Validate order status
        $valid_statuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled'];
        if (!in_array($order_status, $valid_statuses)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid order status']);
            exit;
        }
        
        // Check if order exists
        $stmt = $pdo->prepare("SELECT order_id FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            exit;
        }
        
        // Update order status
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ?, updated_at = NOW() WHERE order_id = ?");
        $stmt->execute([$order_status, $order_id]);
        
        // If status is delivered, also update delivery_status
        if ($order_status === 'delivered') {
            $stmt = $pdo->prepare("UPDATE orders SET delivery_status = 'delivered' WHERE order_id = ?");
            $stmt->execute([$order_id]);
        }
        
        // Get order details for response
        $stmt = $pdo->prepare("SELECT total_amount, created_at FROM orders WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $order_details = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order_id' => $order_id,
            'new_status' => $order_status,
            'order_amount' => $order_details ? (float)$order_details['total_amount'] : 0,
            'is_delivered' => $order_status === 'delivered'
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    error_log("Database error in update_order_status.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => 'Failed to update order status.',
        'details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Server error in update_order_status.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => 'Failed to process request.',
        'details' => $e->getMessage()
    ]);
}
?>

