<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /INTPROG SYSTEM/public/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest user info from DB
$user_query = "SELECT username, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$profile_pic = $user_data['profile_pic'] ?? 'profile-placeholder.jpg';
$username = $user_data['username'] ?? $_SESSION['username'] ?? 'User';

//Fetch latest address info
$address_query = "SELECT full_name, phone_number, street, city, province, postal_code 
                  FROM addresses WHERE user_id = ? 
                  ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($address_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_result = $stmt->get_result();
$address_data = $address_result->fetch_assoc();

$name = $address_data['full_name'] ?? $username;
$phone = $address_data['phone_number'] ?? 'Not provided';
if ($address_data) {
    $address = $address_data['street'] . ', ' . $address_data['city'] . ', ' . 
               $address_data['province'] . ' ' . $address_data['postal_code'];
} else {
    $address = 'Not provided';
}
$stmt->close();
?>


