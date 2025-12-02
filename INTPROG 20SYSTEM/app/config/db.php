<?php
   $host = 'localhost';
   $dbname = 'cafe_java';
   $username = 'root';
   $password = '';

   try {
       $hosts = ['127.0.0.1', 'localhost'];
       $ports = [3306, 3307, 3308];
       $useHost = null;
       $usePort = null;
       $pdo = null;
       foreach ($hosts as $h) {
           foreach ($ports as $p) {
               try {
                   $pdo = new PDO("mysql:host=$h;port=$p", $username, $password);
                   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                   $useHost = $h;
                   $usePort = $p;
                   break 2;
               } catch (PDOException $e) {}
           }
       }
       if (!$pdo) {
           throw new PDOException('Unable to connect to MySQL');
       }
       $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
       $pdo = new PDO("mysql:host=$useHost;port=$usePort;dbname=$dbname", $username, $password);
       $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
   } catch (PDOException $e) {
       die("Database Connection Error: " . $e->getMessage() . "<br><br>" .
           "Please make sure:<br>" .
           "1. XAMPP MySQL service is running (check XAMPP Control Panel)<br>" .
           "2. MySQL is started in XAMPP Control Panel<br>" .
           "3. MySQL credentials are correct in db.php");
   }
?>