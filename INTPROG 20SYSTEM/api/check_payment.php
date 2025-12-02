<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../app/config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$payment_method = $_GET['payment_method'] ?? '';

// Check if payment method information exists
$has_gcash = false;
$has_credit = false;

// Check GCash
$stmt = $pdo->prepare("SELECT gcash_id FROM gcash_accounts WHERE user_id = ? AND gcash_number IS NOT NULL AND gcash_number != '' AND gcash_name IS NOT NULL AND gcash_name != ''");
$stmt->execute([$user_id]);
$has_gcash = $stmt->fetch() !== false;

// Check Credit Card
$stmt = $pdo->prepare("SELECT card_id FROM credit_cards WHERE user_id = ? AND card_number IS NOT NULL AND card_number != '' AND card_name IS NOT NULL AND card_name != ''");
$stmt->execute([$user_id]);
$has_credit = $stmt->fetch() !== false;

// Check Address (for Cash on Delivery)
$stmt = $pdo->prepare("SELECT address_id FROM addresses WHERE user_id = ? AND street IS NOT NULL AND street != '' AND city IS NOT NULL AND city != '' AND province IS NOT NULL AND province != ''");
$stmt->execute([$user_id]);
$has_address = $stmt->fetch() !== false;

$response = [
    'has_gcash' => $has_gcash,
    'has_credit' => $has_credit,
    'has_address' => $has_address
];

// Check specific payment method
if ($payment_method === 'GCash') {
    $response['valid'] = $has_gcash;
    $response['message'] = $has_gcash ? 'GCash information is set up' : 'Please set up your GCash information in your profile';
} elseif ($payment_method === 'Credit Card') {
    $response['valid'] = $has_credit;
    $response['message'] = $has_credit ? 'Credit card information is set up' : 'Please set up your Credit Card information in your profile';
} elseif ($payment_method === 'Cash on Delivery') {
    // Check if user has address set
    $response['valid'] = $has_address;
    $response['message'] = $has_address ? 'Address is set' : 'Please set your address in your profile';
} elseif ($payment_method === 'all') {
    // Return status for all payment methods (for modal display)
    $response['valid'] = true;
    $response['message'] = 'Payment methods status retrieved';
} else {
    $response['valid'] = true; // Other payment methods
    $response['message'] = 'Payment method is valid';
}

echo json_encode($response);
?>

