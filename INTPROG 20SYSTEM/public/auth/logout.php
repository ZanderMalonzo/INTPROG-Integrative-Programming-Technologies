<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$basePath = '/INTPROG SYSTEM';

// Clear all session data
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_unset();
session_destroy();

// Clear any output buffers
if (ob_get_length()) {
    ob_end_clean();
}

header("Location: {$basePath}/public/auth/login.php");
exit();
?>
