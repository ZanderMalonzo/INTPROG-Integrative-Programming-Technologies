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
$nodeApiUrl = 'http://localhost:3000/api/orders';

// Check if Node.js server is running (quick health check)
function isNodeServerRunning() {
    $ch = curl_init('http://localhost:3000/api/health');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    @curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode === 200;
}

// If Node.js is not running, automatically use PHP API
if (!isNodeServerRunning()) {
    require_once 'orders.php';
    exit;
}

// Try to use Node.js API
$ch = curl_init();

// Set common options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-User-ID: ' . $user_id
]);

if ($method === 'POST') {
    // POST request - create order
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        exit;
    }
    
    // Add user_id to the request body
    $input['user_id'] = $user_id;
    
    curl_setopt($ch, CURLOPT_URL, $nodeApiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));
} else {
    // GET request - retrieve orders
    $url = $nodeApiUrl . '?user_id=' . $user_id;
    
    if (isset($_GET['order_id'])) {
        $url .= '&order_id=' . intval($_GET['order_id']);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
}

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// If Node.js fails, automatically fallback to PHP API
if ($curlError || $httpCode === 0 || $httpCode >= 500) {
    // Log the error for debugging
    error_log("Node.js API failed, falling back to PHP API. Error: " . $curlError . ", HTTP Code: " . $httpCode);
    require_once 'orders.php';
    exit;
}

// Check if response is valid JSON
if ($httpCode >= 400) {
    // Try to parse error response
    $errorData = json_decode($response, true);
    if ($errorData && isset($errorData['error'])) {
        http_response_code($httpCode);
        echo $response;
        exit;
    }
}

// Return the response with appropriate status code
http_response_code($httpCode);
echo $response;
?>
