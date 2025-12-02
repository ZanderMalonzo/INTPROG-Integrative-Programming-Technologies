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

try {
    // Get date range from query parameters
    $start_date = $_GET['start_date'] ?? date('Y-m-d');
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    
    // Validate dates
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid date format']);
        exit;
    }
    
    // Get sales data for the date range
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(CASE WHEN order_status = 'delivered' THEN total_amount ELSE 0 END), 0) as total_sales,
            COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as completed_orders,
            COALESCE(AVG(CASE WHEN order_status = 'delivered' THEN total_amount END), 0) as avg_order_value
        FROM orders
        WHERE DATE(created_at) BETWEEN ? AND ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $sales_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ensure all values are set (handle NULL cases)
    if (!$sales_data) {
        $sales_data = [
            'total_orders' => 0,
            'total_sales' => 0,
            'completed_orders' => 0,
            'avg_order_value' => 0
        ];
    }
    
    // Get payment method breakdown
    $stmt = $pdo->prepare("
        SELECT 
            payment_method,
            SUM(total_amount) as total_amount
        FROM orders
        WHERE DATE(created_at) BETWEEN ? AND ? 
        AND order_status = 'delivered'
        GROUP BY payment_method
    ");
    $stmt->execute([$start_date, $end_date]);
    $payment_methods = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $payment_methods[$row['payment_method']] = (float)$row['total_amount'];
    }
    
    // Get recent orders (last 10)
    $stmt = $pdo->prepare("
        SELECT 
            o.order_id,
            o.total_amount,
            o.order_status,
            o.payment_method,
            o.full_name,
            o.created_at
        FROM orders o
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$start_date, $end_date]);
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate comparison with previous period (for percentage change)
    $sales_change = null;
    if ($start_date === $end_date) {
        // For single day, compare with previous day
        $prev_date = date('Y-m-d', strtotime($start_date . ' -1 day'));
        $stmt = $pdo->prepare("
            SELECT SUM(total_amount) as prev_sales
            FROM orders
            WHERE DATE(created_at) = ? AND order_status = 'delivered'
        ");
        $stmt->execute([$prev_date]);
        $prev_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $prev_sales = (float)($prev_data['prev_sales'] ?? 0);
        $current_sales = (float)($sales_data['total_sales'] ?? 0);
        
        if ($prev_sales > 0) {
            $sales_change = (($current_sales - $prev_sales) / $prev_sales) * 100;
        } else if ($current_sales > 0) {
            $sales_change = 100; // 100% increase from 0
        }
    }
    
    // Ensure all numeric values are properly formatted
    $total_sales = (float)($sales_data['total_sales'] ?? 0);
    $total_orders = (int)($sales_data['total_orders'] ?? 0);
    $completed_orders = (int)($sales_data['completed_orders'] ?? 0);
    $avg_order_value = (float)($sales_data['avg_order_value'] ?? 0);
    
    // Fix NaN values
    if (is_nan($total_sales)) $total_sales = 0;
    if (is_nan($avg_order_value)) $avg_order_value = 0;
    
    echo json_encode([
        'success' => true,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'total_sales' => $total_sales,
        'total_orders' => $total_orders,
        'completed_orders' => $completed_orders,
        'avg_order_value' => $avg_order_value,
        'payment_methods' => $payment_methods,
        'recent_orders' => $recent_orders,
        'sales_change' => $sales_change !== null ? (float)$sales_change : null
    ], JSON_NUMERIC_CHECK);
    
} catch (PDOException $e) {
    error_log("Database error in sales_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => 'Failed to fetch sales data.',
        'details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Server error in sales_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => 'Failed to process request.',
        'details' => $e->getMessage()
    ]);
}
?>

