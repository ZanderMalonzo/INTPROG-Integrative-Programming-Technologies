<?php
require_once __DIR__ . '/../../app/config/db.php';
$basePath = '/INTPROG SYSTEM';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check if passwords match
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$username, $email, $hashed_password])) {
            $message = "Signup successful!";
        } else {
            $message = "Error: Failed to create user.";
        }
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Café Java Signup</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/signup.css">

</head>
<body>
    <div class="main">
        <div class="left">
            <img src="<?php echo $basePath; ?>/assets/images/cafejavalogo.jpg" alt="">
        </div>
        <div class="login-container">
            <div class="login-form">
                <h1> Sign Up</h1>
                <p>Create an account and enjoy our specials!</p>
                <p style="color: red;"><?php echo $message; ?></p>
                <form action="<?php echo $basePath; ?>/public/auth/signup.php" method="POST">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="input-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="login-btn">Sign Up</button>
                </form>

                <p class="signup-link">Already have an account? <a href="login.php">Login here!</a></p>
            </div>
        </div>
    </div>
</body>
</html>
