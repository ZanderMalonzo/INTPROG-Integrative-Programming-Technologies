<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #222;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: #333;
            padding: 32px 40px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #fff;
            font-weight: 700;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #bbb;
            font-weight: 500;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #444;
            border-radius: 8px;
            font-size: 16px;
            background: #222;
            color: #fff;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #888;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #444;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #555;
        }
        .login-link {
            text-align: center;
            margin-top: 16px;
        }
        .login-link a {
            color: #bbb;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Register</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
        <?php
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = htmlspecialchars($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            // Here you would save $username and $password to your database
            echo "<p style='color:#fff; text-align:center;'>Registration successful for $username!</p>";
        }
        ?>
    </div>
</body>
</html>
