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
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        // Create a new order
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['items']) || !is_array($input['items']) || empty($input['items'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Items are required']);
            exit;
        }
        
        if (!isset($input['payment_method'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Payment method is required']);
            exit;
        }
        
        // Get user's address
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $address = $stmt->fetch();
        
        if (!$address) {
            http_response_code(400);
            echo json_encode(['error' => 'Address not found. Please set your address first.']);
            exit;
        }
        
        // Calculate total
        $total = 0;
        foreach ($input['items'] as $item) {
            $total += floatval($item['price']) * intval($item['quantity']);
        }
        
        // Build delivery address string
        $delivery_address = $address['street'] . ', ' . $address['city'] . ', ' . $address['province'] . ' ' . $address['postal_code'];
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, payment_method, total_amount, delivery_address, full_name, phone_number, order_status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $user_id,
            $input['payment_method'],
            $total,
            $delivery_address,
            $address['full_name'],
            $address['phone_number']
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_name, item_price, quantity, subtotal) VALUES (?, ?, ?, ?, ?)");
        foreach ($input['items'] as $item) {
            $subtotal = floatval($item['price']) * intval($item['quantity']);
            $stmt->execute([
                $order_id,
                $item['name'],
                $item['price'],
                $item['quantity'],
                $subtotal
            ]);
        }
        
        $pdo->commit();
        
        $response = [
            'success' => true,
            'message' => 'Order placed successfully',
            'order_id' => (int)$order_id
        ];
        
        // Log for debugging (remove in production)
        error_log("Order created successfully: Order ID " . $order_id . " for User ID " . $user_id);
        
        echo json_encode($response);
        
    } elseif ($method === 'GET') {
        // Get order history
        $order_id = $_GET['order_id'] ?? null;
        
        if ($order_id) {
            // Get specific order with items
            $stmt = $pdo->prepare("
                SELECT o.*, u.username, u.email 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.order_id = ? AND o.user_id = ?
            ");
            $stmt->execute([$order_id, $user_id]);
            $order = $stmt->fetch();
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                exit;
            }
            
            // Get order items
            $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt->execute([$order_id]);
            $items = $stmt->fetchAll();
            
            $order['items'] = $items;
            
            echo json_encode($order);
        } else {
            // Get all orders for the user - use GROUP BY to prevent duplicates from JOIN
            $stmt = $pdo->prepare("
                SELECT o.order_id, o.user_id, o.payment_method, o.total_amount, 
                       o.delivery_address, o.full_name, o.phone_number, o.order_status, 
                       o.created_at, u.username, u.email,
                       COUNT(oi.item_id) as item_count
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.user_id = ? 
                GROUP BY o.order_id, o.user_id, o.payment_method, o.total_amount, 
                         o.delivery_address, o.full_name, o.phone_number, o.order_status, 
                         o.created_at, u.username, u.email
                ORDER BY o.order_id ASC, o.created_at ASC
            ");
            $stmt->execute([$user_id]);
            $orders = $stmt->fetchAll();
            
            // Additional safety check - remove duplicates by order_id
            $uniqueOrders = [];
            $seenOrderIds = [];
            foreach ($orders as $order) {
                $orderId = (int)$order['order_id'];
                if (!in_array($orderId, $seenOrderIds)) {
                    $uniqueOrders[] = $order;
                    $seenOrderIds[] = $orderId;
                }
            }
            $orders = $uniqueOrders;
            
            // Get items for each order
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
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log the error for debugging
    error_log("Database error in orders.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => 'Failed to save order. Please try again.',
        'details' => $e->getMessage() // Remove in production for security
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log the error for debugging
    error_log("Server error in orders.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => 'Failed to process order. Please try again.',
        'details' => $e->getMessage() // Remove in production for security
    ]);
}
?>

