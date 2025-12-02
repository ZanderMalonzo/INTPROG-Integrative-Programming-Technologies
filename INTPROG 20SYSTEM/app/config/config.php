<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "cafe_java";

mysqli_report(MYSQLI_REPORT_OFF);
$hosts = ['127.0.0.1', 'localhost'];
$ports = [3306, 3307, 3308];
$conn = null;
$port = null;
foreach ($hosts as $h) {
    foreach ($ports as $p) {
        try {
            $tmp = new mysqli($h, $dbusername, $dbpassword, '', $p);
            if (!$tmp->connect_error) {
                $conn = $tmp;
                $servername = $h;
                $port = $p;
                break 2;
            } else {
                // Failed to connect on this host/port, try next
            }
        } catch (mysqli_sql_exception $e) {
            // suppress connection exceptions
        }
    }
}
if (!$conn || $conn->connect_error) {
    die("MySQL Connection Error: " . ($conn ? $conn->connect_error : 'Unknown') . "<br><br>" .
        "Please make sure:<br>" .
        "1. XAMPP MySQL service is running (check XAMPP Control Panel)<br>" .
        "2. MySQL is started in XAMPP Control Panel<br>" .
        "3. The database 'cafe_java' exists (create it in phpMyAdmin if needed)");
}

// Now select the database
if (!$conn->select_db($dbname)) {
    // Database doesn't exist, try to create it
    $create_db = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($create_db)) {
        $conn->select_db($dbname);
    } else {
        die("Database Error: Could not create or select database '$dbname'. Error: " . $conn->error);
    }
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>