<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../app/config/config.php';
$basePath = '/INTPROG SYSTEM';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $sql = "SELECT id, username, password FROM users WHERE username = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: {$basePath}/public/pages/landingpage.php");
            exit;
        } else {
            $message = "Incorrect password!";
        }
    } else {
        $message = "Username or email not found!";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Café Java Login</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/login.css">
    
</head>
<body>
    <div class="main">
        <div class="left">
            <img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="">
        </div>
        <div class="login-container">
            <div class="login-form">
                <h1> Welcome to <span>Café Java</span></h1>
                <p>Log in and taste the best coffee in BSIT3J!</p>
                <p style="color: red;"><?php echo $message; ?></p>
                <form action="<?php echo $basePath; ?>/public/auth/login.php" method="POST">
                    <div class="input-group">
                        <label for="username">Username or Email</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="login-btn">Brew Up!</button>
                </form>
                <p class="signup-link">Don't have an account? <a href="signup.php">Sign up here!</a></p>
                <p class="admin-link" style="margin-top: 15px; text-align: center;">
                    <a href="<?php echo $basePath; ?>/public/admin/admin_login.php" style="color: #666; font-size: 13px; text-decoration: none; padding: 8px 15px; border: 1px solid #ddd; border-radius: 5px; display: inline-block;">
                        🔐 Admin Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
