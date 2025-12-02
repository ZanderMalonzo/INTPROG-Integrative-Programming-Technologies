<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../app/config/db.php';
$basePath = '/INTPROG SYSTEM';

// If already logged in as admin, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && strtolower(trim($user['role'])) === 'admin') {
        header("Location: {$basePath}/public/admin/admin_dashboard.php");
        exit;
    }
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username_or_email) || empty($password)) {
        $error_message = "Please enter both username/email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE (username = ? OR email = ?) LIMIT 1");
        $stmt->execute([$username_or_email, $username_or_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Check if user is admin
            $user_role = strtolower(trim($user['role'] ?? ''));
            
            if ($user_role === 'admin') {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = true;
                header("Location: {$basePath}/public/admin/admin_dashboard.php");
                exit;
            } else {
                $error_message = "Access denied. Admin privileges required.";
            }
        } else {
            $error_message = "Invalid username/email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Café Java</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/admin_login.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="admin-top-logo"><img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="Cafe Java Logo"></div>
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-login-header">
                <div class="admin-icon">🔐</div>
                <h1>Admin Portal</h1>
                <p class="subtitle">Café Java Management System</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo $basePath; ?>/public/admin/admin_login.php" class="admin-login-form">
                <div class="form-group">
                    <label for="username">
                        <span class="label-icon">👤</span>
                        Username or Email
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your admin credentials"
                        required
                        autofocus
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <span class="label-icon">🔒</span>
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                </div>
                
                <button type="submit" class="admin-login-btn">
                    <span>Sign In</span>
                    <span class="btn-arrow">→</span>
                </button>
            </form>
            
            <div class="admin-login-footer">
                <p>Regular customer? <a href="<?php echo $basePath; ?>/public/auth/login.php">Customer Login</a></p>
                <p class="security-note">🔒 Secure admin access only</p>
            </div>
        </div>
        
        <div class="admin-login-background">
            <div class="bg-shape shape-1"></div>
            <div class="bg-shape shape-2"></div>
            <div class="bg-shape shape-3"></div>
        </div>
    </div>
</body>
</html>

